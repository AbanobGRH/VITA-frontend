<?php
/**
 * VITA Dummy Data Generator
 * Sends high/abnormal health data to test alerts and monitoring
 * Replaces blood pressure with cholesterol calculated from glucose
 */

require_once '../config/database.php';

class DummyDataGenerator {
    private $api_base_url;
    private $serial_numbers;
    private $active = false;
    
    public function __construct($api_base_url = 'http://localhost') {
        $this->api_base_url = rtrim($api_base_url, '/');
        $this->serial_numbers = [
            'VITA-2024-001234',
            'VITA-2024-001235',
            'VITA-2024-001236'
        ];
    }
    
    public function activate() {
        $this->active = true;
        echo "🚨 DUMMY DATA GENERATOR ACTIVATED 🚨\n";
        echo "Sending HIGH/ABNORMAL health values for testing...\n\n";
    }
    
    public function deactivate() {
        $this->active = false;
        echo "✅ Dummy data generator deactivated\n";
    }
    
    public function isActive() {
        return $this->active;
    }
    
    public function generateHighHealthData() {
        if (!$this->active) {
            echo "❌ Generator not activated. Call activate() first.\n";
            return false;
        }
        
        // Generate HIGH/ABNORMAL values to trigger alerts
        $glucose = rand(200, 350); // Very high glucose (normal: 70-180)
        $cholesterol = $this->calculateCholesterol($glucose); // Calculate from glucose
        
        $data = [
            'heart_rate' => rand(140, 180), // High heart rate (normal: 50-120)
            'blood_oxygen' => rand(85, 92), // Low oxygen (normal: 95-100)
            'blood_glucose' => $glucose,
            'cholesterol' => $cholesterol,
            'temperature' => round(rand(1000, 1040) / 10, 1), // High fever (normal: 98.6)
            'activity_level' => 'high',
            'battery_level' => rand(5, 15), // Low battery
            'latitude' => 39.7392 + (rand(-100, 100) / 1000), // Random location
            'longitude' => -104.9903 + (rand(-100, 100) / 1000),
            'accuracy_meters' => rand(3, 10)
        ];
        
        return $data;
    }
    
    public function generateMediumHealthData() {
        if (!$this->active) {
            echo "❌ Generator not activated. Call activate() first.\n";
            return false;
        }
        
        // Generate MEDIUM concern values
        $glucose = rand(150, 199); // Moderately high glucose
        $cholesterol = $this->calculateCholesterol($glucose);
        
        $data = [
            'heart_rate' => rand(100, 139), // Elevated heart rate
            'blood_oxygen' => rand(93, 95), // Slightly low oxygen
            'blood_glucose' => $glucose,
            'cholesterol' => $cholesterol,
            'temperature' => round(rand(990, 1010) / 10, 1), // Slight fever
            'activity_level' => 'moderate',
            'battery_level' => rand(15, 25), // Medium battery
            'latitude' => 39.7392 + (rand(-50, 50) / 1000),
            'longitude' => -104.9903 + (rand(-50, 50) / 1000),
            'accuracy_meters' => rand(3, 8)
        ];
        
        return $data;
    }
    
    public function generateNormalHealthData() {
        // Generate NORMAL values (can be used without activation)
        $glucose = rand(80, 120); // Normal glucose
        $cholesterol = $this->calculateCholesterol($glucose);
        
        $data = [
            'heart_rate' => rand(60, 90), // Normal heart rate
            'blood_oxygen' => rand(96, 99), // Normal oxygen
            'blood_glucose' => $glucose,
            'cholesterol' => $cholesterol,
            'temperature' => round(rand(980, 990) / 10, 1), // Normal temp
            'activity_level' => 'low',
            'battery_level' => rand(70, 95), // Good battery
            'latitude' => 39.7392, // Home location
            'longitude' => -104.9903,
            'accuracy_meters' => rand(3, 5)
        ];
        
        return $data;
    }
    
    /**
     * Calculate cholesterol from glucose level
     * Using a realistic correlation formula
     */
    private function calculateCholesterol($glucose) {
        // Base cholesterol calculation
        // Normal cholesterol: 150-200 mg/dL
        // High cholesterol: >240 mg/dL
        
        // Formula: Higher glucose generally correlates with higher cholesterol
        $base_cholesterol = 160; // Base level
        $glucose_factor = ($glucose - 100) * 0.8; // Correlation factor
        $random_variation = rand(-20, 20); // Natural variation
        
        $cholesterol = $base_cholesterol + $glucose_factor + $random_variation;
        
        // Ensure realistic bounds
        $cholesterol = max(120, min(400, $cholesterol));
        
        return round($cholesterol);
    }
    
    public function sendDataToDevice($serial_number, $data_type = 'high') {
        if (!$this->active && $data_type !== 'normal') {
            echo "❌ Generator not activated. Only normal data can be sent.\n";
            return false;
        }
        
        switch ($data_type) {
            case 'high':
                $health_data = $this->generateHighHealthData();
                break;
            case 'medium':
                $health_data = $this->generateMediumHealthData();
                break;
            case 'normal':
            default:
                $health_data = $this->generateNormalHealthData();
                break;
        }
        
        if (!$health_data) {
            return false;
        }
        
        $payload = array_merge([
            'serial_number' => $serial_number,
            'timestamp' => date('Y-m-d H:i:s')
        ], $health_data);
        
        $result = $this->sendToEndpoint('/api/device_endpoint.php', $payload);
        
        if ($result) {
            $this->displayDataSent($serial_number, $health_data, $data_type);
        }
        
        return $result;
    }
    
    public function sendBatchData($count = 5, $data_type = 'high') {
        if (!$this->active && $data_type !== 'normal') {
            echo "❌ Generator not activated. Only normal data can be sent.\n";
            return false;
        }
        
        echo "📊 Sending {$count} {$data_type} data points...\n\n";
        
        $success_count = 0;
        
        for ($i = 0; $i < $count; $i++) {
            $serial = $this->serial_numbers[array_rand($this->serial_numbers)];
            
            if ($this->sendDataToDevice($serial, $data_type)) {
                $success_count++;
            }
            
            // Small delay between requests
            usleep(500000); // 0.5 seconds
        }
        
        echo "\n✅ Successfully sent {$success_count}/{$count} data points\n\n";
        return $success_count;
    }
    
    public function startContinuousMode($interval_seconds = 30, $data_type = 'high') {
        if (!$this->active && $data_type !== 'normal') {
            echo "❌ Generator not activated. Only normal data can be sent.\n";
            return false;
        }
        
        echo "🔄 Starting continuous mode (every {$interval_seconds} seconds)\n";
        echo "Press Ctrl+C to stop\n\n";
        
        while (true) {
            $serial = $this->serial_numbers[array_rand($this->serial_numbers)];
            $this->sendDataToDevice($serial, $data_type);
            
            echo "⏰ Waiting {$interval_seconds} seconds...\n\n";
            sleep($interval_seconds);
        }
    }
    
    private function sendToEndpoint($endpoint, $data) {
        $url = $this->api_base_url . $endpoint;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen(json_encode($data))
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($http_code === 200) {
            return json_decode($response, true);
        } else {
            echo "❌ HTTP Error {$http_code}: {$response}\n";
            return false;
        }
    }
    
    private function displayDataSent($serial, $data, $type) {
        $type_emoji = [
            'high' => '🚨',
            'medium' => '⚠️',
            'normal' => '✅'
        ];
        
        echo "{$type_emoji[$type]} Data sent to {$serial} ({$type} values):\n";
        echo "   Heart Rate: {$data['heart_rate']} bpm\n";
        echo "   Blood Oxygen: {$data['blood_oxygen']}%\n";
        echo "   Blood Glucose: {$data['blood_glucose']} mg/dL\n";
        echo "   Cholesterol: {$data['cholesterol']} mg/dL\n";
        echo "   Temperature: {$data['temperature']}°F\n";
        echo "   Battery: {$data['battery_level']}%\n";
        echo "   Location: {$data['latitude']}, {$data['longitude']}\n";
        echo "---\n";
    }
    
    public function showMenu() {
        echo "\n";
        echo "🏥 VITA DUMMY DATA GENERATOR\n";
        echo "============================\n";
        echo "Status: " . ($this->active ? "🟢 ACTIVE" : "🔴 INACTIVE") . "\n\n";
        echo "Commands:\n";
        echo "  activate()           - Activate generator (required for high/medium data)\n";
        echo "  deactivate()         - Deactivate generator\n";
        echo "  sendDataToDevice(serial, type) - Send single data point\n";
        echo "  sendBatchData(count, type)      - Send multiple data points\n";
        echo "  startContinuousMode(interval, type) - Continuous sending\n\n";
        echo "Data Types:\n";
        echo "  'high'   - 🚨 High/abnormal values (triggers alerts)\n";
        echo "  'medium' - ⚠️  Medium concern values\n";
        echo "  'normal' - ✅ Normal healthy values\n\n";
        echo "Available Devices:\n";
        foreach ($this->serial_numbers as $serial) {
            echo "  - {$serial}\n";
        }
        echo "\n";
    }
}

// Initialize the generator
$generator = new DummyDataGenerator();

// Show menu
$generator->showMenu();

// Example usage (uncomment to run):
/*
// Activate for high data
$generator->activate();

// Send high abnormal data
$generator->sendBatchData(3, 'high');

// Send medium concern data
$generator->sendBatchData(2, 'medium');

// Send normal data (doesn't require activation)
$generator->sendBatchData(1, 'normal');

// Start continuous mode (uncomment to run)
// $generator->startContinuousMode(30, 'high');

$generator->deactivate();
*/

echo "💡 Usage Examples:\n";
echo "\$generator->activate();\n";
echo "\$generator->sendBatchData(5, 'high');\n";
echo "\$generator->startContinuousMode(30, 'high');\n";
echo "\$generator->deactivate();\n\n";
?>