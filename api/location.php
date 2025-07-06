<?php
/**
 * Location Tracking API Endpoints
 */

require_once '../config/database.php';

class LocationAPI {
    private $db;
    private $conn;
    
    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }
    
    public function updateLocation($data) {
        $rules = [
            'user_id' => ['required' => true],
            'latitude' => ['required' => true],
            'longitude' => ['required' => true],
            'accuracy_meters' => ['required' => false],
            'address' => ['required' => false]
        ];
        
        $errors = ApiResponse::validate($data, $rules);
        if (!empty($errors)) {
            ApiResponse::error('Validation failed', 400, $errors);
        }
        
        try {
            // Determine location type based on safe zones
            $location_type = $this->determineLocationType($data['user_id'], $data['latitude'], $data['longitude']);
            
            $query = "INSERT INTO location_tracking 
                     (user_id, latitude, longitude, accuracy_meters, address, location_type) 
                     VALUES (:user_id, :latitude, :longitude, :accuracy_meters, :address, :location_type)";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $data['user_id']);
            $stmt->bindParam(':latitude', $data['latitude']);
            $stmt->bindParam(':longitude', $data['longitude']);
            $stmt->bindParam(':accuracy_meters', $data['accuracy_meters'] ?? null);
            $stmt->bindParam(':address', $data['address'] ?? null);
            $stmt->bindParam(':location_type', $location_type);
            
            if ($stmt->execute()) {
                $location_id = $this->conn->lastInsertId();
                
                // Check for geofence violations
                $this->checkGeofenceViolations($data['user_id'], $data['latitude'], $data['longitude']);
                
                ApiResponse::success([
                    'location_id' => $location_id,
                    'location_type' => $location_type
                ], 'Location updated successfully');
            }
        } catch (PDOException $e) {
            ApiResponse::error('Failed to update location: ' . $e->getMessage(), 500);
        }
    }
    
    public function addSafeZone($data) {
        $rules = [
            'user_id' => ['required' => true],
            'name' => ['required' => true, 'max_length' => 100],
            'latitude' => ['required' => true],
            'longitude' => ['required' => true],
            'radius_meters' => ['required' => false],
            'address' => ['required' => false]
        ];
        
        $errors = ApiResponse::validate($data, $rules);
        if (!empty($errors)) {
            ApiResponse::error('Validation failed', 400, $errors);
        }
        
        try {
            $query = "INSERT INTO safe_zones (user_id, name, latitude, longitude, radius_meters, address) 
                     VALUES (:user_id, :name, :latitude, :longitude, :radius_meters, :address)";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $data['user_id']);
            $stmt->bindParam(':name', $data['name']);
            $stmt->bindParam(':latitude', $data['latitude']);
            $stmt->bindParam(':longitude', $data['longitude']);
            $stmt->bindParam(':radius_meters', $data['radius_meters'] ?? 50);
            $stmt->bindParam(':address', $data['address'] ?? null);
            
            if ($stmt->execute()) {
                $zone_id = $this->conn->lastInsertId();
                ApiResponse::success(['zone_id' => $zone_id], 'Safe zone added successfully');
            }
        } catch (PDOException $e) {
            ApiResponse::error('Failed to add safe zone: ' . $e->getMessage(), 500);
        }
    }
    
    public function getSafeZones($user_id) {
        try {
            $query = "SELECT * FROM safe_zones WHERE user_id = :user_id AND is_active = 1 ORDER BY created_at DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            
            $zones = $stmt->fetchAll();
            ApiResponse::success($zones);
        } catch (PDOException $e) {
            ApiResponse::error('Failed to fetch safe zones: ' . $e->getMessage(), 500);
        }
    }
    
    public function getLocationHistory($user_id, $limit = 50) {
        try {
            $query = "SELECT * FROM location_tracking 
                     WHERE user_id = :user_id 
                     ORDER BY recorded_at DESC 
                     LIMIT :limit";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            $history = $stmt->fetchAll();
            ApiResponse::success($history);
        } catch (PDOException $e) {
            ApiResponse::error('Failed to fetch location history: ' . $e->getMessage(), 500);
        }
    }
    
    public function getCurrentLocation($user_id) {
        try {
            $query = "SELECT * FROM location_tracking 
                     WHERE user_id = :user_id 
                     ORDER BY recorded_at DESC 
                     LIMIT 1";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            
            $location = $stmt->fetch();
            if ($location) {
                ApiResponse::success($location);
            } else {
                ApiResponse::error('No location data found', 404);
            }
        } catch (PDOException $e) {
            ApiResponse::error('Failed to fetch current location: ' . $e->getMessage(), 500);
        }
    }
    
    private function determineLocationType($user_id, $latitude, $longitude) {
        $query = "SELECT name FROM safe_zones 
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
            $alert_query = "INSERT INTO alerts (user_id, alert_type, priority, title, message) 
                           VALUES (:user_id, 'Location', 'Medium', 'Geofence Alert', 'User has left all safe zones')";
            
            $alert_stmt = $this->conn->prepare($alert_query);
            $alert_stmt->bindParam(':user_id', $user_id);
            $alert_stmt->execute();
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
}

// Handle API requests
$method = $_SERVER['REQUEST_METHOD'];
$locationAPI = new LocationAPI();

switch ($method) {
    case 'POST':
        $action = $_GET['action'] ?? 'update';
        $data = json_decode(file_get_contents('php://input'), true);
        
        if ($action === 'safe_zone') {
            $locationAPI->addSafeZone($data);
        } else {
            $locationAPI->updateLocation($data);
        }
        break;
        
    case 'GET':
        $user_id = $_GET['user_id'] ?? null;
        $action = $_GET['action'] ?? 'current';
        
        if (!$user_id) {
            ApiResponse::error('User ID is required', 400);
        }
        
        switch ($action) {
            case 'safe_zones':
                $locationAPI->getSafeZones($user_id);
                break;
            case 'history':
                $limit = $_GET['limit'] ?? 50;
                $locationAPI->getLocationHistory($user_id, $limit);
                break;
            default:
                $locationAPI->getCurrentLocation($user_id);
        }
        break;
        
    default:
        ApiResponse::error('Method not allowed', 405);
}
?>