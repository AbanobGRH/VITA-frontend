<?php
/**
 * Health Metrics API Endpoints
 */

require_once '../config/database.php';

class HealthMetricsAPI {
    private $db;
    private $conn;
    
    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }
    
    public function recordMetrics($data) {
        $rules = [
            'user_id' => ['required' => true],
            'device_id' => ['required' => false],
            'heart_rate' => ['required' => false],
            'blood_pressure_systolic' => ['required' => false],
            'blood_pressure_diastolic' => ['required' => false],
            'blood_oxygen' => ['required' => false],
            'blood_glucose' => ['required' => false],
            'temperature' => ['required' => false],
            'activity_level' => ['required' => false],
            'location_context' => ['required' => false]
        ];
        
        $errors = ApiResponse::validate($data, $rules);
        if (!empty($errors)) {
            ApiResponse::error('Validation failed', 400, $errors);
        }
        
        try {
            $query = "INSERT INTO health_metrics 
                     (user_id, device_id, heart_rate, blood_pressure_systolic, blood_pressure_diastolic, 
                      blood_oxygen, blood_glucose, temperature, activity_level, location_context) 
                     VALUES (:user_id, :device_id, :heart_rate, :blood_pressure_systolic, :blood_pressure_diastolic, 
                             :blood_oxygen, :blood_glucose, :temperature, :activity_level, :location_context)";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $data['user_id']);
            $stmt->bindParam(':device_id', $data['device_id'] ?? null);
            $stmt->bindParam(':heart_rate', $data['heart_rate'] ?? null);
            $stmt->bindParam(':blood_pressure_systolic', $data['blood_pressure_systolic'] ?? null);
            $stmt->bindParam(':blood_pressure_diastolic', $data['blood_pressure_diastolic'] ?? null);
            $stmt->bindParam(':blood_oxygen', $data['blood_oxygen'] ?? null);
            $stmt->bindParam(':blood_glucose', $data['blood_glucose'] ?? null);
            $stmt->bindParam(':temperature', $data['temperature'] ?? null);
            $stmt->bindParam(':activity_level', $data['activity_level'] ?? null);
            $stmt->bindParam(':location_context', $data['location_context'] ?? null);
            
            if ($stmt->execute()) {
                $metric_id = $this->conn->lastInsertId();
                
                // Check for anomalies and create alerts if needed
                $this->checkHealthAnomalies($data);
                
                ApiResponse::success(['metric_id' => $metric_id], 'Health metrics recorded successfully');
            }
        } catch (PDOException $e) {
            ApiResponse::error('Failed to record metrics: ' . $e->getMessage(), 500);
        }
    }
    
    public function getMetrics($user_id, $period = 'week', $limit = 100) {
        try {
            $date_condition = '';
            switch ($period) {
                case 'day':
                    $date_condition = 'AND recorded_at >= DATE_SUB(NOW(), INTERVAL 1 DAY)';
                    break;
                case 'week':
                    $date_condition = 'AND recorded_at >= DATE_SUB(NOW(), INTERVAL 1 WEEK)';
                    break;
                case 'month':
                    $date_condition = 'AND recorded_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)';
                    break;
                case 'year':
                    $date_condition = 'AND recorded_at >= DATE_SUB(NOW(), INTERVAL 1 YEAR)';
                    break;
            }
            
            $query = "SELECT * FROM health_metrics 
                     WHERE user_id = :user_id $date_condition 
                     ORDER BY recorded_at DESC 
                     LIMIT :limit";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            $metrics = $stmt->fetchAll();
            
            // Calculate averages
            $averages = $this->calculateAverages($user_id, $period);
            
            ApiResponse::success([
                'metrics' => $metrics,
                'averages' => $averages,
                'period' => $period
            ]);
        } catch (PDOException $e) {
            ApiResponse::error('Failed to fetch metrics: ' . $e->getMessage(), 500);
        }
    }
    
    private function calculateAverages($user_id, $period) {
        $date_condition = '';
        switch ($period) {
            case 'day':
                $date_condition = 'AND recorded_at >= DATE_SUB(NOW(), INTERVAL 1 DAY)';
                break;
            case 'week':
                $date_condition = 'AND recorded_at >= DATE_SUB(NOW(), INTERVAL 1 WEEK)';
                break;
            case 'month':
                $date_condition = 'AND recorded_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)';
                break;
            case 'year':
                $date_condition = 'AND recorded_at >= DATE_SUB(NOW(), INTERVAL 1 YEAR)';
                break;
        }
        
        $query = "SELECT 
                    AVG(heart_rate) as avg_heart_rate,
                    AVG(blood_pressure_systolic) as avg_systolic,
                    AVG(blood_pressure_diastolic) as avg_diastolic,
                    AVG(blood_oxygen) as avg_oxygen,
                    AVG(blood_glucose) as avg_glucose,
                    AVG(temperature) as avg_temperature
                  FROM health_metrics 
                  WHERE user_id = :user_id $date_condition";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    private function checkHealthAnomalies($data) {
        $alerts = [];
        
        // Heart rate anomalies
        if (isset($data['heart_rate'])) {
            if ($data['heart_rate'] > 120 || $data['heart_rate'] < 50) {
                $alerts[] = [
                    'type' => 'Health',
                    'priority' => $data['heart_rate'] > 140 || $data['heart_rate'] < 40 ? 'High' : 'Medium',
                    'title' => 'Heart Rate Anomaly',
                    'message' => "Heart rate of {$data['heart_rate']} bpm detected"
                ];
            }
        }
        
        // Blood glucose anomalies
        if (isset($data['blood_glucose'])) {
            if ($data['blood_glucose'] > 180 || $data['blood_glucose'] < 70) {
                $alerts[] = [
                    'type' => 'Health',
                    'priority' => $data['blood_glucose'] > 250 || $data['blood_glucose'] < 50 ? 'High' : 'Medium',
                    'title' => 'Blood Glucose Alert',
                    'message' => "Blood glucose level of {$data['blood_glucose']} mg/dL detected"
                ];
            }
        }
        
        // Create alerts
        foreach ($alerts as $alert) {
            $this->createAlert($data['user_id'], $data['device_id'] ?? null, $alert);
        }
    }
    
    private function createAlert($user_id, $device_id, $alert_data) {
        $query = "INSERT INTO alerts (user_id, device_id, alert_type, priority, title, message) 
                 VALUES (:user_id, :device_id, :alert_type, :priority, :title, :message)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':device_id', $device_id);
        $stmt->bindParam(':alert_type', $alert_data['type']);
        $stmt->bindParam(':priority', $alert_data['priority']);
        $stmt->bindParam(':title', $alert_data['title']);
        $stmt->bindParam(':message', $alert_data['message']);
        $stmt->execute();
    }
}

// Handle API requests
$method = $_SERVER['REQUEST_METHOD'];
$healthAPI = new HealthMetricsAPI();

switch ($method) {
    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        $healthAPI->recordMetrics($data);
        break;
        
    case 'GET':
        $user_id = $_GET['user_id'] ?? null;
        $period = $_GET['period'] ?? 'week';
        $limit = $_GET['limit'] ?? 100;
        
        if (!$user_id) {
            ApiResponse::error('User ID is required', 400);
        }
        
        $healthAPI->getMetrics($user_id, $period, $limit);
        break;
        
    default:
        ApiResponse::error('Method not allowed', 405);
}
?>