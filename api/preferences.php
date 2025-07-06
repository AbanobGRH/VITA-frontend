<?php
/**
 * User Preferences API Endpoints
 */

require_once '../config/database.php';

class PreferencesAPI {
    private $db;
    private $conn;
    
    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }
    
    public function updatePreferences($user_id, $data) {
        try {
            $fields = [];
            $params = [':user_id' => $user_id];
            
            $allowed_fields = [
                'dark_mode', 'large_text', 'voice_alerts', 'vibration', 'auto_sync',
                'heart_rate_alerts', 'blood_glucose_alerts', 'medication_reminders',
                'geofence_alerts', 'check_in_reminders', 'auto_emergency_call',
                'fall_detection', 'emergency_timeout_minutes'
            ];
            
            foreach ($data as $key => $value) {
                if (in_array($key, $allowed_fields)) {
                    $fields[] = "$key = :$key";
                    if (in_array($key, ['dark_mode', 'large_text', 'voice_alerts', 'vibration', 'auto_sync',
                                       'heart_rate_alerts', 'blood_glucose_alerts', 'medication_reminders',
                                       'geofence_alerts', 'check_in_reminders', 'auto_emergency_call', 'fall_detection'])) {
                        $params[":$key"] = $value ? 1 : 0;
                    } else {
                        $params[":$key"] = $value;
                    }
                }
            }
            
            if (empty($fields)) {
                ApiResponse::error('No valid fields to update', 400);
            }
            
            $query = "UPDATE user_preferences SET " . implode(', ', $fields) . " WHERE user_id = :user_id";
            $stmt = $this->conn->prepare($query);
            
            if ($stmt->execute($params)) {
                ApiResponse::success(null, 'Preferences updated successfully');
            }
        } catch (PDOException $e) {
            ApiResponse::error('Failed to update preferences: ' . $e->getMessage(), 500);
        }
    }
    
    public function getPreferences($user_id) {
        try {
            $query = "SELECT * FROM user_preferences WHERE user_id = :user_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            
            $preferences = $stmt->fetch();
            if ($preferences) {
                // Convert boolean fields
                $boolean_fields = [
                    'dark_mode', 'large_text', 'voice_alerts', 'vibration', 'auto_sync',
                    'heart_rate_alerts', 'blood_glucose_alerts', 'medication_reminders',
                    'geofence_alerts', 'check_in_reminders', 'auto_emergency_call', 'fall_detection'
                ];
                
                foreach ($boolean_fields as $field) {
                    if (isset($preferences[$field])) {
                        $preferences[$field] = (bool) $preferences[$field];
                    }
                }
                
                ApiResponse::success($preferences);
            } else {
                ApiResponse::error('Preferences not found', 404);
            }
        } catch (PDOException $e) {
            ApiResponse::error('Failed to fetch preferences: ' . $e->getMessage(), 500);
        }
    }
    
    public function updatePrivacySettings($user_id, $data) {
        try {
            $fields = [];
            $params = [':user_id' => $user_id];
            
            $allowed_fields = [
                'location_tracking', 'data_sharing_research', 'data_sharing_family',
                'health_data_retention_days', 'location_data_retention_days', 'two_factor_enabled'
            ];
            
            foreach ($data as $key => $value) {
                if (in_array($key, $allowed_fields)) {
                    $fields[] = "$key = :$key";
                    if (in_array($key, ['location_tracking', 'data_sharing_research', 'data_sharing_family', 'two_factor_enabled'])) {
                        $params[":$key"] = $value ? 1 : 0;
                    } else {
                        $params[":$key"] = $value;
                    }
                }
            }
            
            if (empty($fields)) {
                ApiResponse::error('No valid fields to update', 400);
            }
            
            $query = "UPDATE privacy_settings SET " . implode(', ', $fields) . " WHERE user_id = :user_id";
            $stmt = $this->conn->prepare($query);
            
            if ($stmt->execute($params)) {
                ApiResponse::success(null, 'Privacy settings updated successfully');
            }
        } catch (PDOException $e) {
            ApiResponse::error('Failed to update privacy settings: ' . $e->getMessage(), 500);
        }
    }
    
    public function getPrivacySettings($user_id) {
        try {
            $query = "SELECT * FROM privacy_settings WHERE user_id = :user_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            
            $settings = $stmt->fetch();
            if ($settings) {
                // Convert boolean fields
                $boolean_fields = ['location_tracking', 'data_sharing_research', 'data_sharing_family', 'two_factor_enabled'];
                
                foreach ($boolean_fields as $field) {
                    if (isset($settings[$field])) {
                        $settings[$field] = (bool) $settings[$field];
                    }
                }
                
                ApiResponse::success($settings);
            } else {
                ApiResponse::error('Privacy settings not found', 404);
            }
        } catch (PDOException $e) {
            ApiResponse::error('Failed to fetch privacy settings: ' . $e->getMessage(), 500);
        }
    }
}

// Handle API requests
$method = $_SERVER['REQUEST_METHOD'];
$preferencesAPI = new PreferencesAPI();

switch ($method) {
    case 'PUT':
        $user_id = $_GET['user_id'] ?? null;
        $type = $_GET['type'] ?? 'preferences';
        
        if (!$user_id) {
            ApiResponse::error('User ID is required', 400);
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        if ($type === 'privacy') {
            $preferencesAPI->updatePrivacySettings($user_id, $data);
        } else {
            $preferencesAPI->updatePreferences($user_id, $data);
        }
        break;
        
    case 'GET':
        $user_id = $_GET['user_id'] ?? null;
        $type = $_GET['type'] ?? 'preferences';
        
        if (!$user_id) {
            ApiResponse::error('User ID is required', 400);
        }
        
        if ($type === 'privacy') {
            $preferencesAPI->getPrivacySettings($user_id);
        } else {
            $preferencesAPI->getPreferences($user_id);
        }
        break;
        
    default:
        ApiResponse::error('Method not allowed', 405);
}
?>