<?php
/**
 * Database Configuration for VITA Health Platform
 */

class Database {
    private $host = 'localhost';
    private $db_name = 'vita_health_platform';
    private $username = 'your_username';
    private $password = 'your_password';
    private $conn;

    public function getConnection() {
        $this->conn = null;
        
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password,
                array(
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
                )
            );
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        
        return $this->conn;
    }
}

/**
 * Response Helper Class
 */
class ApiResponse {
    public static function success($data = null, $message = "Success") {
        http_response_code(200);
        echo json_encode([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
        exit;
    }
    
    public static function error($message = "Error occurred", $code = 400, $details = null) {
        http_response_code($code);
        echo json_encode([
            'status' => 'error',
            'message' => $message,
            'details' => $details,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
        exit;
    }
    
    public static function validate($data, $rules) {
        $errors = [];
        
        foreach ($rules as $field => $rule) {
            if ($rule['required'] && (!isset($data[$field]) || empty($data[$field]))) {
                $errors[$field] = $field . ' is required';
            }
            
            if (isset($data[$field]) && !empty($data[$field])) {
                if (isset($rule['type'])) {
                    switch ($rule['type']) {
                        case 'email':
                            if (!filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
                                $errors[$field] = $field . ' must be a valid email';
                            }
                            break;
                        case 'phone':
                            if (!preg_match('/^[\+]?[1-9][\d]{0,15}$/', $data[$field])) {
                                $errors[$field] = $field . ' must be a valid phone number';
                            }
                            break;
                        case 'date':
                            if (!strtotime($data[$field])) {
                                $errors[$field] = $field . ' must be a valid date';
                            }
                            break;
                    }
                }
                
                if (isset($rule['max_length']) && strlen($data[$field]) > $rule['max_length']) {
                    $errors[$field] = $field . ' must be less than ' . $rule['max_length'] . ' characters';
                }
            }
        }
        
        return $errors;
    }
}

// Set headers for API responses
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}
?>