<?php
/**
 * Database Helper Functions for PHP Pages
 */

require_once 'config/database.php';

class VitaDB {
    private static $instance = null;
    private $conn;
    
    private function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new VitaDB();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->conn;
    }
    
    // User functions
    public function getUserById($user_id) {
        $query = "SELECT * FROM users WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetch();
    }
    
    public function getUserByPatientId($patient_id) {
        $query = "SELECT * FROM users WHERE patient_id = :patient_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':patient_id', $patient_id);
        $stmt->execute();
        return $stmt->fetch();
    }
    
    // Health metrics functions
    public function getLatestHealthMetrics($user_id) {
        $query = "SELECT * FROM health_metrics 
                 WHERE user_id = :user_id 
                 ORDER BY recorded_at DESC 
                 LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetch();
    }
    
    public function getHealthMetricsByPeriod($user_id, $period = 'week', $limit = 100) {
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
        return $stmt->fetchAll();
    }
    
    // Device functions
    public function getUserDevices($user_id) {
        $query = "SELECT d.*, dc.measurement_frequency, dc.power_saving_mode, dc.auto_sync 
                 FROM devices d 
                 LEFT JOIN device_configurations dc ON d.device_id = dc.device_id 
                 WHERE d.user_id = :user_id 
                 ORDER BY d.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    // Alerts functions
    public function getRecentAlerts($user_id, $limit = 10) {
        $query = "SELECT * FROM alerts 
                 WHERE user_id = :user_id AND is_dismissed = 0 
                 ORDER BY alert_time DESC 
                 LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    // Medications functions
    public function getUserMedications($user_id) {
        $query = "SELECT * FROM medications 
                 WHERE user_id = :user_id 
                 ORDER BY created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        
        $medications = $stmt->fetchAll();
        
        // Decode JSON fields
        foreach ($medications as &$medication) {
            if ($medication['reminder_times']) {
                $medication['reminder_times'] = json_decode($medication['reminder_times'], true);
            }
        }
        
        return $medications;
    }
    
    public function getTodayMedicationSchedule($user_id) {
        $query = "SELECT ms.*, m.name, m.dosage, m.condition_for 
                 FROM medication_schedule ms 
                 JOIN medications m ON ms.medication_id = m.medication_id 
                 WHERE m.user_id = :user_id AND ms.scheduled_date = CURDATE() 
                 ORDER BY ms.scheduled_time";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    // Location functions
    public function getCurrentLocation($user_id) {
        $query = "SELECT * FROM location_tracking 
                 WHERE user_id = :user_id 
                 ORDER BY recorded_at DESC 
                 LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetch();
    }
    
    public function getSafeZones($user_id) {
        $query = "SELECT * FROM safe_zones 
                 WHERE user_id = :user_id AND is_active = 1 
                 ORDER BY created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function getLocationHistory($user_id, $limit = 20) {
        $query = "SELECT * FROM location_tracking 
                 WHERE user_id = :user_id 
                 ORDER BY recorded_at DESC 
                 LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    // Emergency contacts functions
    public function getEmergencyContacts($user_id) {
        $query = "SELECT * FROM emergency_contacts 
                 WHERE user_id = :user_id 
                 ORDER BY 
                     CASE priority_level 
                         WHEN 'Primary' THEN 1 
                         WHEN 'Secondary' THEN 2 
                         ELSE 3 
                     END, 
                     created_at ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    // Preferences functions
    public function getUserPreferences($user_id) {
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
        }
        
        return $preferences;
    }
    
    public function getPrivacySettings($user_id) {
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
        }
        
        return $settings;
    }
    
    // Family access functions
    public function getFamilyMembers($user_id) {
        $query = "SELECT * FROM family_access 
                 WHERE user_id = :user_id AND status = 'Active' 
                 ORDER BY invited_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        
        $members = $stmt->fetchAll();
        
        // Decode JSON permissions
        foreach ($members as &$member) {
            if ($member['permissions']) {
                $member['permissions'] = json_decode($member['permissions'], true);
            }
        }
        
        return $members;
    }
    
    // Medical conditions and allergies
    public function getMedicalConditions($user_id) {
        $query = "SELECT * FROM medical_conditions 
                 WHERE user_id = :user_id AND status = 'Active' 
                 ORDER BY diagnosed_date DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function getAllergies($user_id) {
        $query = "SELECT * FROM allergies WHERE user_id = :user_id ORDER BY allergen";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function getHealthcareProviders($user_id) {
        $query = "SELECT * FROM healthcare_providers 
                 WHERE user_id = :user_id 
                 ORDER BY is_primary DESC, name";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    // Utility functions
    public function formatTime($datetime) {
        return date('g:i A', strtotime($datetime));
    }
    
    public function formatDate($datetime) {
        return date('M j, Y', strtotime($datetime));
    }
    
    public function timeAgo($datetime) {
        $time = time() - strtotime($datetime);
        
        if ($time < 60) return 'just now';
        if ($time < 3600) return floor($time/60) . ' min ago';
        if ($time < 86400) return floor($time/3600) . ' hours ago';
        if ($time < 2592000) return floor($time/86400) . ' days ago';
        
        return $this->formatDate($datetime);
    }
}

// Session management for user authentication
function getCurrentUserId() {
    // For demo purposes, return a default user ID
    // In production, this would check session/authentication
    return 1;
}

function getCurrentUser() {
    $db = VitaDB::getInstance();
    return $db->getUserById(getCurrentUserId());
}
?>