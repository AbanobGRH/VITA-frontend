<?php
/**
 * Medications API Endpoints
 */

require_once '../config/database.php';

class MedicationsAPI {
    private $db;
    private $conn;
    
    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }
    
    public function addMedication($data) {
        $rules = [
            'user_id' => ['required' => true],
            'name' => ['required' => true, 'max_length' => 100],
            'dosage' => ['required' => true, 'max_length' => 50],
            'frequency' => ['required' => true, 'max_length' => 100],
            'condition_for' => ['required' => false, 'max_length' => 100],
            'instructions' => ['required' => false],
            'start_date' => ['required' => true, 'type' => 'date'],
            'end_date' => ['required' => false, 'type' => 'date'],
            'reminder_times' => ['required' => false],
            'current_stock' => ['required' => false]
        ];
        
        $errors = ApiResponse::validate($data, $rules);
        if (!empty($errors)) {
            ApiResponse::error('Validation failed', 400, $errors);
        }
        
        try {
            $reminder_times = isset($data['reminder_times']) ? json_encode($data['reminder_times']) : null;
            
            $query = "INSERT INTO medications 
                     (user_id, name, dosage, frequency, condition_for, instructions, start_date, end_date, 
                      reminder_times, current_stock) 
                     VALUES (:user_id, :name, :dosage, :frequency, :condition_for, :instructions, :start_date, 
                             :end_date, :reminder_times, :current_stock)";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $data['user_id']);
            $stmt->bindParam(':name', $data['name']);
            $stmt->bindParam(':dosage', $data['dosage']);
            $stmt->bindParam(':frequency', $data['frequency']);
            $stmt->bindParam(':condition_for', $data['condition_for'] ?? null);
            $stmt->bindParam(':instructions', $data['instructions'] ?? null);
            $stmt->bindParam(':start_date', $data['start_date']);
            $stmt->bindParam(':end_date', $data['end_date'] ?? null);
            $stmt->bindParam(':reminder_times', $reminder_times);
            $stmt->bindParam(':current_stock', $data['current_stock'] ?? 30);
            
            if ($stmt->execute()) {
                $medication_id = $this->conn->lastInsertId();
                
                // Create medication schedule
                if (isset($data['reminder_times']) && is_array($data['reminder_times'])) {
                    $this->createMedicationSchedule($medication_id, $data['reminder_times']);
                }
                
                ApiResponse::success(['medication_id' => $medication_id], 'Medication added successfully');
            }
        } catch (PDOException $e) {
            ApiResponse::error('Failed to add medication: ' . $e->getMessage(), 500);
        }
    }
    
    public function updateMedication($medication_id, $data) {
        try {
            $fields = [];
            $params = [':medication_id' => $medication_id];
            
            $allowed_fields = ['name', 'dosage', 'frequency', 'condition_for', 'instructions', 
                             'end_date', 'reminder_enabled', 'current_stock'];
            
            foreach ($data as $key => $value) {
                if (in_array($key, $allowed_fields)) {
                    $fields[] = "$key = :$key";
                    $params[":$key"] = $value;
                }
            }
            
            if (isset($data['reminder_times'])) {
                $fields[] = "reminder_times = :reminder_times";
                $params[':reminder_times'] = json_encode($data['reminder_times']);
            }
            
            if (empty($fields)) {
                ApiResponse::error('No valid fields to update', 400);
            }
            
            $query = "UPDATE medications SET " . implode(', ', $fields) . " WHERE medication_id = :medication_id";
            $stmt = $this->conn->prepare($query);
            
            if ($stmt->execute($params)) {
                ApiResponse::success(null, 'Medication updated successfully');
            }
        } catch (PDOException $e) {
            ApiResponse::error('Failed to update medication: ' . $e->getMessage(), 500);
        }
    }
    
    public function getMedications($user_id) {
        try {
            $query = "SELECT * FROM medications WHERE user_id = :user_id ORDER BY created_at DESC";
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
            
            ApiResponse::success($medications);
        } catch (PDOException $e) {
            ApiResponse::error('Failed to fetch medications: ' . $e->getMessage(), 500);
        }
    }
    
    public function markAsTaken($schedule_id) {
        try {
            $query = "UPDATE medication_schedule 
                     SET status = 'Taken', taken_at = NOW() 
                     WHERE schedule_id = :schedule_id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':schedule_id', $schedule_id);
            
            if ($stmt->execute()) {
                ApiResponse::success(null, 'Medication marked as taken');
            }
        } catch (PDOException $e) {
            ApiResponse::error('Failed to mark medication as taken: ' . $e->getMessage(), 500);
        }
    }
    
    public function getTodaySchedule($user_id) {
        try {
            $query = "SELECT ms.*, m.name, m.dosage, m.condition_for 
                     FROM medication_schedule ms 
                     JOIN medications m ON ms.medication_id = m.medication_id 
                     WHERE m.user_id = :user_id AND ms.scheduled_date = CURDATE() 
                     ORDER BY ms.scheduled_time";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            
            $schedule = $stmt->fetchAll();
            ApiResponse::success($schedule);
        } catch (PDOException $e) {
            ApiResponse::error('Failed to fetch schedule: ' . $e->getMessage(), 500);
        }
    }
    
    private function createMedicationSchedule($medication_id, $reminder_times) {
        foreach ($reminder_times as $time) {
            $query = "INSERT INTO medication_schedule (medication_id, scheduled_time, scheduled_date) 
                     VALUES (:medication_id, :scheduled_time, CURDATE())";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':medication_id', $medication_id);
            $stmt->bindParam(':scheduled_time', $time);
            $stmt->execute();
        }
    }
}

// Handle API requests
$method = $_SERVER['REQUEST_METHOD'];
$medicationsAPI = new MedicationsAPI();

switch ($method) {
    case 'POST':
        $action = $_GET['action'] ?? 'add';
        $data = json_decode(file_get_contents('php://input'), true);
        
        if ($action === 'mark_taken') {
            $schedule_id = $data['schedule_id'] ?? null;
            if (!$schedule_id) {
                ApiResponse::error('Schedule ID is required', 400);
            }
            $medicationsAPI->markAsTaken($schedule_id);
        } else {
            $medicationsAPI->addMedication($data);
        }
        break;
        
    case 'PUT':
        $medication_id = $_GET['medication_id'] ?? null;
        if (!$medication_id) {
            ApiResponse::error('Medication ID is required', 400);
        }
        $data = json_decode(file_get_contents('php://input'), true);
        $medicationsAPI->updateMedication($medication_id, $data);
        break;
        
    case 'GET':
        $user_id = $_GET['user_id'] ?? null;
        $action = $_GET['action'] ?? 'list';
        
        if (!$user_id) {
            ApiResponse::error('User ID is required', 400);
        }
        
        if ($action === 'schedule') {
            $medicationsAPI->getTodaySchedule($user_id);
        } else {
            $medicationsAPI->getMedications($user_id);
        }
        break;
        
    default:
        ApiResponse::error('Method not allowed', 405);
}
?>