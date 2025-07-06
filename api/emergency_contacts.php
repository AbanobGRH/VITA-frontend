<?php
/**
 * Emergency Contacts API Endpoints
 */

require_once '../config/database.php';

class EmergencyContactsAPI {
    private $db;
    private $conn;
    
    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }
    
    public function addContact($data) {
        $rules = [
            'user_id' => ['required' => true],
            'name' => ['required' => true, 'max_length' => 100],
            'relationship' => ['required' => true, 'max_length' => 50],
            'phone' => ['required' => true, 'type' => 'phone'],
            'email' => ['required' => false, 'type' => 'email'],
            'priority_level' => ['required' => false],
            'can_receive_health_data' => ['required' => false],
            'can_receive_location_data' => ['required' => false],
            'can_receive_alerts' => ['required' => false]
        ];
        
        $errors = ApiResponse::validate($data, $rules);
        if (!empty($errors)) {
            ApiResponse::error('Validation failed', 400, $errors);
        }
        
        try {
            $query = "INSERT INTO emergency_contacts 
                     (user_id, name, relationship, phone, email, priority_level, 
                      can_receive_health_data, can_receive_location_data, can_receive_alerts) 
                     VALUES (:user_id, :name, :relationship, :phone, :email, :priority_level, 
                             :can_receive_health_data, :can_receive_location_data, :can_receive_alerts)";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $data['user_id']);
            $stmt->bindParam(':name', $data['name']);
            $stmt->bindParam(':relationship', $data['relationship']);
            $stmt->bindParam(':phone', $data['phone']);
            $stmt->bindParam(':email', $data['email'] ?? null);
            $stmt->bindParam(':priority_level', $data['priority_level'] ?? 'Secondary');
            $stmt->bindParam(':can_receive_health_data', $data['can_receive_health_data'] ?? false, PDO::PARAM_BOOL);
            $stmt->bindParam(':can_receive_location_data', $data['can_receive_location_data'] ?? false, PDO::PARAM_BOOL);
            $stmt->bindParam(':can_receive_alerts', $data['can_receive_alerts'] ?? true, PDO::PARAM_BOOL);
            
            if ($stmt->execute()) {
                $contact_id = $this->conn->lastInsertId();
                ApiResponse::success(['contact_id' => $contact_id], 'Emergency contact added successfully');
            }
        } catch (PDOException $e) {
            ApiResponse::error('Failed to add emergency contact: ' . $e->getMessage(), 500);
        }
    }
    
    public function updateContact($contact_id, $data) {
        try {
            $fields = [];
            $params = [':contact_id' => $contact_id];
            
            $allowed_fields = ['name', 'relationship', 'phone', 'email', 'priority_level', 
                             'can_receive_health_data', 'can_receive_location_data', 'can_receive_alerts'];
            
            foreach ($data as $key => $value) {
                if (in_array($key, $allowed_fields)) {
                    $fields[] = "$key = :$key";
                    if (in_array($key, ['can_receive_health_data', 'can_receive_location_data', 'can_receive_alerts'])) {
                        $params[":$key"] = $value ? 1 : 0;
                    } else {
                        $params[":$key"] = $value;
                    }
                }
            }
            
            if (empty($fields)) {
                ApiResponse::error('No valid fields to update', 400);
            }
            
            $query = "UPDATE emergency_contacts SET " . implode(', ', $fields) . " WHERE contact_id = :contact_id";
            $stmt = $this->conn->prepare($query);
            
            if ($stmt->execute($params)) {
                ApiResponse::success(null, 'Emergency contact updated successfully');
            }
        } catch (PDOException $e) {
            ApiResponse::error('Failed to update emergency contact: ' . $e->getMessage(), 500);
        }
    }
    
    public function getContacts($user_id) {
        try {
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
            
            $contacts = $stmt->fetchAll();
            ApiResponse::success($contacts);
        } catch (PDOException $e) {
            ApiResponse::error('Failed to fetch emergency contacts: ' . $e->getMessage(), 500);
        }
    }
    
    public function deleteContact($contact_id) {
        try {
            $query = "DELETE FROM emergency_contacts WHERE contact_id = :contact_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':contact_id', $contact_id);
            
            if ($stmt->execute()) {
                if ($stmt->rowCount() > 0) {
                    ApiResponse::success(null, 'Emergency contact deleted successfully');
                } else {
                    ApiResponse::error('Emergency contact not found', 404);
                }
            }
        } catch (PDOException $e) {
            ApiResponse::error('Failed to delete emergency contact: ' . $e->getMessage(), 500);
        }
    }
}

// Handle API requests
$method = $_SERVER['REQUEST_METHOD'];
$contactsAPI = new EmergencyContactsAPI();

switch ($method) {
    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        $contactsAPI->addContact($data);
        break;
        
    case 'PUT':
        $contact_id = $_GET['contact_id'] ?? null;
        if (!$contact_id) {
            ApiResponse::error('Contact ID is required', 400);
        }
        $data = json_decode(file_get_contents('php://input'), true);
        $contactsAPI->updateContact($contact_id, $data);
        break;
        
    case 'GET':
        $user_id = $_GET['user_id'] ?? null;
        if (!$user_id) {
            ApiResponse::error('User ID is required', 400);
        }
        $contactsAPI->getContacts($user_id);
        break;
        
    case 'DELETE':
        $contact_id = $_GET['contact_id'] ?? null;
        if (!$contact_id) {
            ApiResponse::error('Contact ID is required', 400);
        }
        $contactsAPI->deleteContact($contact_id);
        break;
        
    default:
        ApiResponse::error('Method not allowed', 405);
}
?>