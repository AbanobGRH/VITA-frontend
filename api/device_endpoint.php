<?php
/**
 * Device Data Endpoint - Receives data from VITA watches
 * This endpoint handles all incoming data from devices using serial number identification
 */

require_once '../config/database.php';

class DeviceEndpoint {
    private $db;
    private $conn;
    
    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }
    
    public function receiveDeviceData($data) {
        // Validation rules for device data
        $rules = [
            'serial_number' => ['required' => true, 'max_length' => 100],
            'timestamp' => ['required' => false],
            'battery_level' => ['required' => false],
            'heart_rate' => ['required' => false],
            'blood_pressure_systolic' => ['required' => false],
            'blood_pressure_diastolic' => ['required' => false],
            'blood_oxygen' => ['required' => false],
            'blood_glucose' => ['required' => false],
            'temperature' => ['required' => false],
            'activity_level' => ['required' => false],
            'latitude' => ['required' => false],
            'longitude' => ['required' => false],
            'accuracy_meters' => ['required' => false]
        ];
        
        $errors = ApiResponse::validate($data, $rules);
        if (!empty($errors)) {
            ApiResponse::error('Validation failed', 400, $errors);
        }
        
        try {
            // Find device and user by serial number
            $device_info = $this->getDeviceBySerial($data['serial_number']);
            if (!$device_info) {
                ApiResponse::error('Device not found or not paired', 404);
            }
            
            $user_id = $device_info['user_id'];
            $device_id = $device_info['device_id'];
            
            // Update device status and battery
            $this->updateDeviceStatus($device_id, $data);
            
            // Process health metrics if present
            if ($this->hasHealthMetrics($data)) {
                $this->recordHealthMetrics($user_id, $device_id, $data);
            }
            
            // Process location data if present
            if ($this->hasLocationData($data)) {
                $this->recordLocationData($user_id, $data);
            }
            
            // Log successful data reception
            $this->logDeviceData($user_id, $device_id, $data);
            
            ApiResponse::success([
                'device_id' => $device_id,
                'user_id' => $user_id,
                'processed_at' => date('Y-m-d H:i:s')
            ], 'Device data processed successfully');
            
        } catch (PDOException $e) {
            ApiResponse::error('Failed to process device data: ' . $e->getMessage(), 500);
        }
    }
    
    private function getDeviceBySerial($serial_number) {
        $query = "SELECT device_id, user_id, nickname FROM devices WHERE serial_number = :serial_number";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':serial_number', $serial_number);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    private function updateDeviceStatus($device_id, $data) {
        $fields = ['last_sync = NOW()'];
        $params = [':device_id' => $device_id];
        
        if (isset($data['battery_level'])) {
            $fields[] = 'battery_level = :battery_level';
            $params[':battery_level'] = $data['battery_level'];
        }
        
        // Always mark as connected when receiving data
        $fields[] = "status = 'Connected'";
        
        $query = "UPDATE devices SET " . implode(', ', $fields) . " WHERE device_id = :device_id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        
        // Check for low battery alert
        if (isset($data['battery_level']) && $data['battery_level'] <= 20) {
            $this->createLowBatteryAlert($device_id, $data['battery_level']);
        }
    }
    
    private function hasHealthMetrics($data) {
        $health_fields = ['heart_rate', 'blood_pressure_systolic', 'blood_pressure_diastolic', 
                         'blood_oxygen', 'blood_glucose', 'temperature'];
        
        foreach ($health_fields as $field) {
            if (isset($data[$field]) && !empty($data[$field])) {
                return true;
            }
        }
        return false;
    }
    
    private function hasLocationData($data) {
        return isset($data['latitude']) && isset($data['longitude']);
    }
    
    private function recordHealthMetrics($user_id, $device_id, $data) {
        $query = "INSERT INTO health_metrics 
                 (user_id, device_id, heart_rate, blood_pressure_systolic, blood_pressure_diastolic, 
                  blood_oxygen, blood_glucose, temperature, activity_level, recorded_at) 
                 VALUES (:user_id, :device_id, :heart_rate, :blood_pressure_systolic, :blood_pressure_diastolic, 
                         :blood_oxygen, :blood_glucose, :temperature, :activity_level, :recorded_at)";
        
        $recorded_at = isset($data['timestamp']) ? $data['timestamp'] : date('Y-m-d H:i:s');
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':device_id', $device_id);
        $stmt->bindParam(':heart_rate', $data['heart_rate'] ?? null);
        $stmt->bindParam(':blood_pressure_systolic', $data['blood_pressure_systolic'] ?? null);
        $stmt->bindParam(':blood_pressure_diastolic', $data['blood_pressure_diastolic'] ?? null);
        $stmt->bindParam(':blood_oxygen', $data['blood_oxygen'] ?? null);
        $stmt->bindParam(':blood_glucose', $data['blood_glucose'] ?? null);
        $stmt->bindParam(':temperature', $data['temperature'] ?? null);
        $stmt->bindParam(':activity_level', $data['activity_level'] ?? null);
        $stmt->bindParam(':recorded_at', $recorded_at);
        
        $stmt->execute();
        
        // Check for health anomalies
        $this->checkHealthAnomalies($user_id, $device_id, $data);
    }
    
    private function recordLocationData($user_id, $data) {
        // Determine location type based on safe zones
        $location_type = $this->determineLocationType($user_id, $data['latitude'], $data['longitude']);
        
        $query = "INSERT INTO location_tracking 
                 (user_id, latitude, longitude, accuracy_meters, location_type, recorded_at) 
                 VALUES (:user_id, :latitude, :longitude, :accuracy_meters, :location_type, :recorded_at)";
        
        $recorded_at = isset($data['timestamp']) ? $data['timestamp'] : date('Y-m-d H:i:s');
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':latitude', $data['latitude']);
        $stmt->bindParam(':longitude', $data['longitude']);
        $stmt->bindParam(':accuracy_meters', $data['accuracy_meters'] ?? null);
        $stmt->bindParam(':location_type', $location_type);
        $stmt->bindParam(':recorded_at', $recorded_at);
        
        $stmt->execute();
        
        // Check for geofence violations
        $this->checkGeofenceViolations($user_id, $data['latitude'], $data['longitude']);
    }
    
    private function checkHealthAnomalies($user_id, $device_id, $data) {
        $alerts = [];
        
        // Heart rate anomalies
        if (isset($data['heart_rate'])) {
            if ($data['heart_rate'] > 120 || $data['heart_rate'] < 50) {
                $priority = ($data['heart_rate'] > 140 || $data['heart_rate'] < 40) ? 'High' : 'Medium';
                $alerts[] = [
                    'type' => 'Health',
                    'priority' => $priority,
                    'title' => 'Heart Rate Anomaly',
                    'message' => "Heart rate of {$data['heart_rate']} bpm detected"
                ];
            }
        }
        
        // Blood pressure anomalies
        if (isset($data['blood_pressure_systolic']) && isset($data['blood_pressure_diastolic'])) {
            if ($data['blood_pressure_systolic'] > 140 || $data['blood_pressure_diastolic'] > 90) {
                $priority = ($data['blood_pressure_systolic'] > 160 || $data['blood_pressure_diastolic'] > 100) ? 'High' : 'Medium';
                $alerts[] = [
                    'type' => 'Health',
                    'priority' => $priority,
                    'title' => 'Blood Pressure Alert',
                    'message' => "Blood pressure {$data['blood_pressure_systolic']}/{$data['blood_pressure_diastolic']} mmHg detected"
                ];
            }
        }
        
        // Blood glucose anomalies
        if (isset($data['blood_glucose'])) {
            if ($data['blood_glucose'] > 180 || $data['blood_glucose'] < 70) {
                $priority = ($data['blood_glucose'] > 250 || $data['blood_glucose'] < 50) ? 'High' : 'Medium';
                $alerts[] = [
                    'type' => 'Health',
                    'priority' => $priority,
                    'title' => 'Blood Glucose Alert',
                    'message' => "Blood glucose level of {$data['blood_glucose']} mg/dL detected"
                ];
            }
        }
        
        // Blood oxygen anomalies
        if (isset($data['blood_oxygen'])) {
            if ($data['blood_oxygen'] < 95) {
                $priority = ($data['blood_oxygen'] < 90) ? 'High' : 'Medium';
                $alerts[] = [
                    'type' => 'Health',
                    'priority' => $priority,
                    'title' => 'Low Blood Oxygen',
                    'message' => "Blood oxygen level of {$data['blood_oxygen']}% detected"
                ];
            }
        }
        
        // Create alerts
        foreach ($alerts as $alert) {
            $this->createAlert($user_id, $device_id, $alert);
        }
    }
    
    private function determineLocationType($user_id, $latitude, $longitude) {
        $query = "SELECT name, latitude, longitude, radius_meters FROM safe_zones 
                 WHERE user_id = :user_id AND is_active = 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        
        $zones = $stmt->fetchAll();
        
        foreach ($zones as $zone) {
            $distance = $this->calculateDistance($latitude, $longitude, $zone['latitude'], $zone['longitude']);
            if ($distance <= $zone['radius_meters']) {
                return $zone['name'];
            }
        }
        
        return 'Unknown';
    }
    
    private function checkGeofenceViolations($user_id, $latitude, $longitude) {
        // Check user preferences for geofence alerts
        $pref_query = "SELECT geofence_alerts FROM user_preferences WHERE user_id = :user_id";
        $pref_stmt = $this->conn->prepare($pref_query);
        $pref_stmt->bindParam(':user_id', $user_id);
        $pref_stmt->execute();
        $preferences = $pref_stmt->fetch();
        
        if (!$preferences || !$preferences['geofence_alerts']) {
            return; // Geofence alerts disabled
        }
        
        // Check if user is outside all safe zones
        $zone_query = "SELECT * FROM safe_zones WHERE user_id = :user_id AND is_active = 1";
        $zone_stmt = $this->conn->prepare($zone_query);
        $zone_stmt->bindParam(':user_id', $user_id);
        $zone_stmt->execute();
        $zones = $zone_stmt->fetchAll();
        
        $in_safe_zone = false;
        foreach ($zones as $zone) {
            $distance = $this->calculateDistance($latitude, $longitude, $zone['latitude'], $zone['longitude']);
            if ($distance <= $zone['radius_meters']) {
                $in_safe_zone = true;
                break;
            }
        }
        
        if (!$in_safe_zone && !empty($zones)) {
            // Create geofence violation alert
            $this->createAlert($user_id, null, [
                'type' => 'Location',
                'priority' => 'Medium',
                'title' => 'Geofence Alert',
                'message' => 'User has left all safe zones'
            ]);
        }
    }
    
    private function calculateDistance($lat1, $lon1, $lat2, $lon2) {
        $earth_radius = 6371000; // Earth radius in meters
        
        $lat1_rad = deg2rad($lat1);
        $lat2_rad = deg2rad($lat2);
        $delta_lat = deg2rad($lat2 - $lat1);
        $delta_lon = deg2rad($lon2 - $lon1);
        
        $a = sin($delta_lat / 2) * sin($delta_lat / 2) +
             cos($lat1_rad) * cos($lat2_rad) *
             sin($delta_lon / 2) * sin($delta_lon / 2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        
        return $earth_radius * $c;
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
    
    private function createLowBatteryAlert($device_id, $battery_level) {
        // Get user_id from device
        $query = "SELECT user_id, nickname FROM devices WHERE device_id = :device_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':device_id', $device_id);
        $stmt->execute();
        $device = $stmt->fetch();
        
        if ($device) {
            $this->createAlert($device['user_id'], $device_id, [
                'type' => 'Device',
                'priority' => 'Medium',
                'title' => 'Low Battery',
                'message' => "{$device['nickname']} battery is at {$battery_level}% - charging recommended"
            ]);
        }
    }
    
    private function logDeviceData($user_id, $device_id, $data) {
        $query = "INSERT INTO system_logs (user_id, action, details, ip_address) 
                 VALUES (:user_id, 'DEVICE_DATA_RECEIVED', :details, :ip_address)";
        
        $details = "Device ID: {$device_id}, Data: " . json_encode($data);
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':details', $details);
        $stmt->bindParam(':ip_address', $_SERVER['REMOTE_ADDR'] ?? 'unknown');
        $stmt->execute();
    }
}

// Handle API requests
$method = $_SERVER['REQUEST_METHOD'];

if ($method !== 'POST') {
    ApiResponse::error('Only POST method allowed', 405);
}

$deviceEndpoint = new DeviceEndpoint();
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    ApiResponse::error('Invalid JSON data', 400);
}

$deviceEndpoint->receiveDeviceData($data);
?>