<?php
/**
 * Alerts Management API Endpoints
 */

require_once '../config/database.php';

class AlertsAPI {
    private $db;
    private $conn;
    
    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }
    
    public function createAlert($data) {
        $rules = [
            'user_id' => ['required' => true],
            'alert_type' => ['required' => true],
            'priority' => ['required' => false],
            'title' => ['required' => true, 'max_length' => 200],
            'message' => ['required' => true],
            'device_id' => ['required' => false],
            'metadata' => ['required' => false]
        ];
        
        $errors = ApiResponse::validate($data, $rules);
        if (!empty($errors)) {
            ApiResponse::error('Validation failed', 400, $errors);
        }
        
        try {
            $metadata = isset($data['metadata']) ? json_encode($data['metadata']) : null;
            
            $query = "INSERT INTO alerts 
                     (user_id, device_id, alert_type, priority, title, message, metadata) 
                     VALUES (:user_id, :device_id, :alert_type, :priority, :title, :message, :metadata)";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $data['user_id']);
            $stmt->bindParam(':device_id', $data['device_id'] ?? null);
            $stmt->bindParam(':alert_type', $data['alert_type']);
            $stmt->bindParam(':priority', $data['priority'] ?? 'Medium');
            $stmt->bindParam(':title', $data['title']);
            $stmt->bindParam(':message', $data['message']);
            $stmt->bindParam(':metadata', $metadata);
            
            if ($stmt->execute()) {
                $alert_id = $this->conn->lastInsertId();
                
                // Notify emergency contacts if high priority
                if (($data['priority'] ?? 'Medium') === 'High' || ($data['priority'] ?? 'Medium') === 'Critical') {
                    $this->notifyEmergencyContacts($data['user_id'], $data);
                }
                
                ApiResponse::success(['alert_id' => $alert_id], 'Alert created successfully');
            }
        } catch (PDOException $e) {
            ApiResponse::error('Failed to create alert: ' . $e->getMessage(), 500);
        }
    }
    
    public function getAlerts($user_id, $limit = 50, $dismissed = false) {
        try {
            $dismissed_condition = $dismissed ? '' : 'AND is_dismissed = 0';
            
            $query = "SELECT * FROM alerts 
                     WHERE user_id = :user_id $dismissed_condition 
                     ORDER BY alert_time DESC 
                     LIMIT :limit";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            $alerts = $stmt->fetchAll();
            
            // Decode JSON metadata
            foreach ($alerts as &$alert) {
                if ($alert['metadata']) {
                    $alert['metadata'] = json_decode($alert['metadata'], true);
                }
            }
            
            ApiResponse::success($alerts);
        } catch (PDOException $e) {
            ApiResponse::error('Failed to fetch alerts: ' . $e->getMessage(), 500);
        }
    }
    
    public function dismissAlert($alert_id) {
        try {
            $query = "UPDATE alerts 
                     SET is_dismissed = 1, dismissed_at = NOW() 
                     WHERE alert_id = :alert_id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':alert_id', $alert_id);
            
            if ($stmt->execute()) {
                ApiResponse::success(null, 'Alert dismissed successfully');
            }
        } catch (PDOException $e) {
            ApiResponse::error('Failed to dismiss alert: ' . $e->getMessage(), 500);
        }
    }
    
    public function clearAllAlerts($user_id) {
        try {
            $query = "UPDATE alerts 
                     SET is_dismissed = 1, dismissed_at = NOW() 
                     WHERE user_id = :user_id AND is_dismissed = 0";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $user_id);
            
            if ($stmt->execute()) {
                $affected_rows = $stmt->rowCount();
                ApiResponse::success(['dismissed_count' => $affected_rows], 'All alerts cleared successfully');
            }
        } catch (PDOException $e) {
            ApiResponse::error('Failed to clear alerts: ' . $e->getMessage(), 500);
        }
    }
    
    public function triggerEmergencyAlert($user_id) {
        try {
            // Create emergency alert
            $alert_data = [
                'user_id' => $user_id,
                'alert_type' => 'Emergency',
                'priority' => 'Critical',
                'title' => 'Emergency Alert Activated',
                'message' => 'Emergency alert has been manually triggered by the user',
                'metadata' => ['triggered_manually' => true, 'timestamp' => date('Y-m-d H:i:s')]
            ];
            
            $this->createAlert($alert_data);
            
            // Immediately notify all emergency contacts
            $this->notifyEmergencyContacts($user_id, $alert_data, true);
            
            ApiResponse::success(null, 'Emergency alert triggered successfully');
        } catch (Exception $e) {
            ApiResponse::error('Failed to trigger emergency alert: ' . $e->getMessage(), 500);
        }
    }
    
    private function notifyEmergencyContacts($user_id, $alert_data, $is_emergency = false) {
        try {
            $priority_condition = $is_emergency ? '' : "AND priority_level IN ('Primary', 'Secondary')";
            
            $query = "SELECT * FROM emergency_contacts 
                     WHERE user_id = :user_id $priority_condition 
                     ORDER BY 
                         CASE priority_level 
                             WHEN 'Primary' THEN 1 
                             WHEN 'Secondary' THEN 2 
                             ELSE 3 
                         END";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            
            $contacts = $stmt->fetchAll();
            
            // In a real implementation, you would send SMS/email notifications here
            // For now, we'll just log the notification attempts
            foreach ($contacts as $contact) {
                $log_query = "INSERT INTO system_logs (user_id, action, details) 
                             VALUES (:user_id, 'EMERGENCY_NOTIFICATION', :details)";
                
                $details = "Notified {$contact['name']} ({$contact['phone']}) about: {$alert_data['title']}";
                
                $log_stmt = $this->conn->prepare($log_query);
                $log_stmt->bindParam(':user_id', $user_id);
                $log_stmt->bindParam(':details', $details);
                $log_stmt->execute();
            }
        } catch (PDOException $e) {
            // Log error but don't fail the main operation
            error_log("Failed to notify emergency contacts: " . $e->getMessage());
        }
    }
}

// Handle API requests
$method = $_SERVER['REQUEST_METHOD'];
$alertsAPI = new AlertsAPI();

switch ($method) {
    case 'POST':
        $action = $_GET['action'] ?? 'create';
        $data = json_decode(file_get_contents('php://input'), true);
        
        if ($action === 'emergency') {
            $user_id = $data['user_id'] ?? null;
            if (!$user_id) {
                ApiResponse::error('User ID is required', 400);
            }
            $alertsAPI->triggerEmergencyAlert($user_id);
        } else {
            $alertsAPI->createAlert($data);
        }
        break;
        
    case 'PUT':
        $alert_id = $_GET['alert_id'] ?? null;
        $action = $_GET['action'] ?? 'dismiss';
        
        if ($action === 'dismiss') {
            if (!$alert_id) {
                ApiResponse::error('Alert ID is required', 400);
            }
            $alertsAPI->dismissAlert($alert_id);
        } elseif ($action === 'clear_all') {
            $user_id = $_GET['user_id'] ?? null;
            if (!$user_id) {
                ApiResponse::error('User ID is required', 400);
            }
            $alertsAPI->clearAllAlerts($user_id);
        }
        break;
        
    case 'GET':
        $user_id = $_GET['user_id'] ?? null;
        $limit = $_GET['limit'] ?? 50;
        $dismissed = $_GET['dismissed'] ?? false;
        
        if (!$user_id) {
            ApiResponse::error('User ID is required', 400);
        }
        
        $alertsAPI->getAlerts($user_id, $limit, $dismissed);
        break;
        
    default:
        ApiResponse::error('Method not allowed', 405);
}
?>