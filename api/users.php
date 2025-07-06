<?php
/**
 * User Management API Endpoints
 */

require_once '../config/database.php';

class UserAPI {
    private $db;
    private $conn;
    
    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }
    
    public function createUser($data) {
        // Validation rules
        $rules = [
            'full_name' => ['required' => true, 'max_length' => 100],
            'date_of_birth' => ['required' => true, 'type' => 'date'],
            'phone' => ['required' => false, 'type' => 'phone'],
            'email' => ['required' => false, 'type' => 'email'],
            'address' => ['required' => false, 'max_length' => 500],
            'blood_type' => ['required' => false, 'max_length' => 5],
            'insurance_info' => ['required' => false, 'max_length' => 200]
        ];
        
        $errors = ApiResponse::validate($data, $rules);
        if (!empty($errors)) {
            ApiResponse::error('Validation failed', 400, $errors);
        }
        
        try {
            // Generate unique patient ID
            $patient_id = 'VT-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            
            $query = "INSERT INTO users (full_name, date_of_birth, patient_id, phone, email, address, blood_type, insurance_info) 
                     VALUES (:full_name, :date_of_birth, :patient_id, :phone, :email, :address, :blood_type, :insurance_info)";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':full_name', $data['full_name']);
            $stmt->bindParam(':date_of_birth', $data['date_of_birth']);
            $stmt->bindParam(':patient_id', $patient_id);
            $stmt->bindParam(':phone', $data['phone'] ?? null);
            $stmt->bindParam(':email', $data['email'] ?? null);
            $stmt->bindParam(':address', $data['address'] ?? null);
            $stmt->bindParam(':blood_type', $data['blood_type'] ?? null);
            $stmt->bindParam(':insurance_info', $data['insurance_info'] ?? null);
            
            if ($stmt->execute()) {
                $user_id = $this->conn->lastInsertId();
                
                // Create default preferences
                $this->createDefaultPreferences($user_id);
                
                // Log the action
                $this->logAction($user_id, 'USER_CREATED', 'User account created');
                
                ApiResponse::success([
                    'user_id' => $user_id,
                    'patient_id' => $patient_id
                ], 'User created successfully');
            }
        } catch (PDOException $e) {
            ApiResponse::error('Failed to create user: ' . $e->getMessage(), 500);
        }
    }
    
    public function updateUser($user_id, $data) {
        $rules = [
            'full_name' => ['required' => false, 'max_length' => 100],
            'phone' => ['required' => false, 'type' => 'phone'],
            'email' => ['required' => false, 'type' => 'email'],
            'address' => ['required' => false, 'max_length' => 500],
            'blood_type' => ['required' => false, 'max_length' => 5],
            'insurance_info' => ['required' => false, 'max_length' => 200]
        ];
        
        $errors = ApiResponse::validate($data, $rules);
        if (!empty($errors)) {
            ApiResponse::error('Validation failed', 400, $errors);
        }
        
        try {
            $fields = [];
            $params = [':user_id' => $user_id];
            
            foreach ($data as $key => $value) {
                if (in_array($key, ['full_name', 'phone', 'email', 'address', 'blood_type', 'insurance_info'])) {
                    $fields[] = "$key = :$key";
                    $params[":$key"] = $value;
                }
            }
            
            if (empty($fields)) {
                ApiResponse::error('No valid fields to update', 400);
            }
            
            $query = "UPDATE users SET " . implode(', ', $fields) . " WHERE user_id = :user_id";
            $stmt = $this->conn->prepare($query);
            
            if ($stmt->execute($params)) {
                $this->logAction($user_id, 'USER_UPDATED', 'User profile updated');
                ApiResponse::success(null, 'User updated successfully');
            }
        } catch (PDOException $e) {
            ApiResponse::error('Failed to update user: ' . $e->getMessage(), 500);
        }
    }
    
    public function getUser($user_id) {
        try {
            $query = "SELECT * FROM users WHERE user_id = :user_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            
            $user = $stmt->fetch();
            if ($user) {
                // Remove sensitive data
                unset($user['created_at']);
                ApiResponse::success($user);
            } else {
                ApiResponse::error('User not found', 404);
            }
        } catch (PDOException $e) {
            ApiResponse::error('Failed to fetch user: ' . $e->getMessage(), 500);
        }
    }
    
    private function createDefaultPreferences($user_id) {
        $query = "INSERT INTO user_preferences (user_id) VALUES (:user_id)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        
        $query = "INSERT INTO privacy_settings (user_id) VALUES (:user_id)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
    }
    
    private function logAction($user_id, $action, $details) {
        $query = "INSERT INTO system_logs (user_id, action, details, ip_address, user_agent) 
                 VALUES (:user_id, :action, :details, :ip_address, :user_agent)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':action', $action);
        $stmt->bindParam(':details', $details);
        $stmt->bindParam(':ip_address', $_SERVER['REMOTE_ADDR'] ?? 'unknown');
        $stmt->bindParam(':user_agent', $_SERVER['HTTP_USER_AGENT'] ?? 'unknown');
        $stmt->execute();
    }
}

// Handle API requests
$method = $_SERVER['REQUEST_METHOD'];
$userAPI = new UserAPI();

switch ($method) {
    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        $userAPI->createUser($data);
        break;
        
    case 'PUT':
        $user_id = $_GET['user_id'] ?? null;
        if (!$user_id) {
            ApiResponse::error('User ID is required', 400);
        }
        $data = json_decode(file_get_contents('php://input'), true);
        $userAPI->updateUser($user_id, $data);
        break;
        
    case 'GET':
        $user_id = $_GET['user_id'] ?? null;
        if (!$user_id) {
            ApiResponse::error('User ID is required', 400);
        }
        $userAPI->getUser($user_id);
        break;
        
    default:
        ApiResponse::error('Method not allowed', 405);
}
?>