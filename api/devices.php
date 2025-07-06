<?php
/**
 * Device Management API Endpoints
 */

require_once '../config/database.php';

class DevicesAPI {
    private $db;
    private $conn;
    
    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }
    
    public function pairDevice($data) {
        $rules = [
            'user_id' => ['required' => true],
            'serial_number' => ['required' => true, 'max_length' => 100],
            'device_type' => ['required' => false, 'max_length' => 50],
            'nickname' => ['required' => false, 'max_length' => 50]
        ];
        
        $errors = ApiResponse::validate($data, $rules);
        if (!empty($errors)) {
            ApiResponse::error('Validation failed', 400, $errors);
        }
        
        try {
            // Check if device already exists
            $check_query = "SELECT device_id FROM devices WHERE serial_number = :serial_number";
            $check_stmt = $this->conn->prepare($check_query);
            $check_stmt->bindParam(':serial_number', $data['serial_number']);
            $check_stmt->execute();
            
            if ($check_stmt->fetch()) {
                ApiResponse::error('Device already paired', 400);
            }
            
            $query = "INSERT INTO devices (user_id, serial_number, device_type, nickname, status) 
                     VALUES (:user_id, :serial_number, :device_type, :nickname, 'Connected')";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $data['user_id']);
            $stmt->bindParam(':serial_number', $data['serial_number']);
            $stmt->bindParam(':device_type', $data['device_type'] ?? 'VITA Watch Pro');
            $stmt->bindParam(':nickname', $data['nickname'] ?? $data['device_type'] ?? 'VITA Watch Pro');
            
            if ($stmt->execute()) {
                $device_id = $this->conn->lastInsertId();
                
                // Create default device configuration
                $this->createDefaultConfiguration($device_id);
                
                ApiResponse::success(['device_id' => $device_id], 'Device paired successfully');
            }
        } catch (PDOException $e) {
            ApiResponse::error('Failed to pair device: ' . $e->getMessage(), 500);
        }
    }
    
    public function updateDeviceStatus($device_id, $data) {
        try {
            $fields = [];
            $params = [':device_id' => $device_id];
            
            $allowed_fields = ['status', 'battery_level', 'nickname', 'firmware_version'];
            
            foreach ($data as $key => $value) {
                if (in_array($key, $allowed_fields)) {
                    $fields[] = "$key = :$key";
                    $params[":$key"] = $value;
                }
            }
            
            if (empty($fields)) {
                ApiResponse::error('No valid fields to update', 400);
            }
            
            $fields[] = "last_sync = NOW()";
            
            $query = "UPDATE devices SET " . implode(', ', $fields) . " WHERE device_id = :device_id";
            $stmt = $this->conn->prepare($query);
            
            if ($stmt->execute($params)) {
                // Check for low battery alert
                if (isset($data['battery_level']) && $data['battery_level'] <= 20) {
                    $this->createLowBatteryAlert($device_id, $data['battery_level']);
                }
                
                ApiResponse::success(null, 'Device updated successfully');
            }
        } catch (PDOException $e) {
            ApiResponse::error('Failed to update device: ' . $e->getMessage(), 500);
        }
    }
    
    public function getDevices($user_id) {
        try {
            $query = "SELECT d.*, dc.measurement_frequency, dc.power_saving_mode, dc.auto_sync 
                     FROM devices d 
                     LEFT JOIN device_configurations dc ON d.device_id = dc.device_id 
                     WHERE d.user_id = :user_id 
                     ORDER BY d.created_at DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            
            $devices = $stmt->fetchAll();
            ApiResponse::success($devices);
        } catch (PDOException $e) {
            ApiResponse::error('Failed to fetch devices: ' . $e->getMessage(), 500);
        }
    }
    
    public function updateConfiguration($device_id, $data) {
        try {
            $fields = [];
            $params = [':device_id' => $device_id];
            
            $allowed_fields = ['measurement_frequency', 'power_saving_mode', 'auto_sync'];
            
            foreach ($data as $key => $value) {
                if (in_array($key, $allowed_fields)) {
                    $fields[] = "$key = :$key";
                    $params[":$key"] = $value;
                }
            }
            
            if (isset($data['alert_thresholds'])) {
                $fields[] = "alert_thresholds = :alert_thresholds";
                $params[':alert_thresholds'] = json_encode($data['alert_thresholds']);
            }
            
            if (empty($fields)) {
                ApiResponse::error('No valid fields to update', 400);
            }
            
            $query = "UPDATE device_configurations SET " . implode(', ', $fields) . " WHERE device_id = :device_id";
            $stmt = $this->conn->prepare($query);
            
            if ($stmt->execute($params)) {
                ApiResponse::success(null, 'Device configuration updated successfully');
            }
        } catch (PDOException $e) {
            ApiResponse::error('Failed to update configuration: ' . $e->getMessage(), 500);
        }
    }
    
    private function createDefaultConfiguration($device_id) {
        $default_thresholds = [
            'heart_rate' => ['low' => 50, 'high' => 120],
            'blood_pressure' => ['systolic_high' => 140, 'diastolic_high' => 90],
            'blood_glucose' => ['low' => 70, 'high' => 180]
        ];
        
        $query = "INSERT INTO device_configurations (device_id, alert_thresholds) 
                 VALUES (:device_id, :alert_thresholds)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':device_id', $device_id);
        $stmt->bindParam(':alert_thresholds', json_encode($default_thresholds));
        $stmt->execute();
    }
    
    private function createLowBatteryAlert($device_id, $battery_level) {
        // Get user_id from device
        $query = "SELECT user_id, nickname FROM devices WHERE device_id = :device_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':device_id', $device_id);
        $stmt->execute();
        $device = $stmt->fetch();
        
        if ($device) {
            $alert_query = "INSERT INTO alerts (user_id, device_id, alert_type, priority, title, message) 
                           VALUES (:user_id, :device_id, 'Device', 'Medium', 'Low Battery', :message)";
            
            $message = "{$device['nickname']} battery is at {$battery_level}% - charging recommended";
            
            $alert_stmt = $this->conn->prepare($alert_query);
            $alert_stmt->bindParam(':user_id', $device['user_id']);
            $alert_stmt->bindParam(':device_id', $device_id);
            $alert_stmt->bindParam(':message', $message);
            $alert_stmt->execute();
        }
    }
}

// Handle API requests
$method = $_SERVER['REQUEST_METHOD'];
$devicesAPI = new DevicesAPI();

switch ($method) {
    case 'POST':
        $action = $_GET['action'] ?? 'pair';
        $data = json_decode(file_get_contents('php://input'), true);
        
        if ($action === 'pair') {
            $devicesAPI->pairDevice($data);
        }
        break;
        
    case 'PUT':
        $device_id = $_GET['device_id'] ?? null;
        $action = $_GET['action'] ?? 'status';
        
        if (!$device_id) {
            ApiResponse::error('Device ID is required', 400);
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        if ($action === 'config') {
            $devicesAPI->updateConfiguration($device_id, $data);
        } else {
            $devicesAPI->updateDeviceStatus($device_id, $data);
        }
        break;
        
    case 'GET':
        $user_id = $_GET['user_id'] ?? null;
        if (!$user_id) {
            ApiResponse::error('User ID is required', 400);
        }
        $devicesAPI->getDevices($user_id);
        break;
        
    default:
        ApiResponse::error('Method not allowed', 405);
}
?>