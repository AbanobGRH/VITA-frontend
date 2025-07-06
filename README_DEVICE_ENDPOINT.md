# VITA Device Endpoint Documentation

## Device Data Endpoint

The VITA platform includes a comprehensive device endpoint (`/api/device_endpoint.php`) that receives and processes data from VITA watches and other monitoring devices.

### Endpoint URL
```
POST /api/device_endpoint.php
```

### Authentication
The device is identified by its unique serial number. No additional authentication is required as the serial number serves as the device identifier.

### Request Format

#### Headers
```
Content-Type: application/json
```

#### Request Body
```json
{
  "serial_number": "VITA-2024-001234",
  "timestamp": "2024-01-15 14:30:00",
  "battery_level": 85,
  "heart_rate": 72,
  "blood_pressure_systolic": 120,
  "blood_pressure_diastolic": 80,
  "blood_oxygen": 98,
  "blood_glucose": 95.5,
  "temperature": 98.6,
  "activity_level": "moderate",
  "latitude": 39.7392,
  "longitude": -104.9903,
  "accuracy_meters": 5
}
```

#### Required Fields
- `serial_number`: Unique device identifier (must be paired in system)

#### Optional Fields
- `timestamp`: ISO datetime string (defaults to current time)
- `battery_level`: Battery percentage (0-100)
- `heart_rate`: Heart rate in BPM
- `blood_pressure_systolic`: Systolic blood pressure in mmHg
- `blood_pressure_diastolic`: Diastolic blood pressure in mmHg
- `blood_oxygen`: Blood oxygen saturation percentage
- `blood_glucose`: Blood glucose in mg/dL
- `temperature`: Body temperature in Fahrenheit
- `activity_level`: Activity level (low, moderate, high)
- `latitude`: GPS latitude coordinate
- `longitude`: GPS longitude coordinate
- `accuracy_meters`: GPS accuracy in meters

### Response Format

#### Success Response (200 OK)
```json
{
  "status": "success",
  "message": "Device data processed successfully",
  "data": {
    "device_id": 123,
    "user_id": 456,
    "processed_at": "2024-01-15 14:30:15"
  },
  "timestamp": "2024-01-15 14:30:15"
}
```

#### Error Responses

**Device Not Found (404)**
```json
{
  "status": "error",
  "message": "Device not found or not paired",
  "timestamp": "2024-01-15 14:30:15"
}
```

**Validation Error (400)**
```json
{
  "status": "error",
  "message": "Validation failed",
  "details": {
    "serial_number": "serial_number is required"
  },
  "timestamp": "2024-01-15 14:30:15"
}
```

### Data Processing

When data is received, the endpoint performs the following operations:

1. **Device Validation**: Verifies the device exists and is paired
2. **Device Status Update**: Updates device status, battery level, and last sync time
3. **Health Metrics Recording**: Stores health data in the database
4. **Location Tracking**: Records GPS coordinates and determines location type
5. **Anomaly Detection**: Checks for health anomalies and creates alerts
6. **Geofence Monitoring**: Checks if user is within safe zones
7. **Alert Generation**: Creates alerts for low battery, health anomalies, etc.

### Automatic Alert Generation

The system automatically generates alerts for:

#### Health Anomalies
- **Heart Rate**: Outside 50-120 BPM range
- **Blood Pressure**: Systolic >140 or Diastolic >90 mmHg
- **Blood Glucose**: Outside 70-180 mg/dL range
- **Blood Oxygen**: Below 95%

#### Device Issues
- **Low Battery**: Battery level ≤20%
- **Connection Issues**: Device offline for extended periods

#### Location Alerts
- **Geofence Violations**: User outside all safe zones
- **Emergency Location**: Rapid location changes or unusual patterns

### Example Device Integration

#### Python Example
```python
import requests
import json
from datetime import datetime

def send_device_data(serial_number, health_data, location_data=None):
    url = "https://your-domain.com/api/device_endpoint.php"
    
    payload = {
        "serial_number": serial_number,
        "timestamp": datetime.now().isoformat(),
        **health_data
    }
    
    if location_data:
        payload.update(location_data)
    
    headers = {
        "Content-Type": "application/json"
    }
    
    response = requests.post(url, json=payload, headers=headers)
    return response.json()

# Example usage
health_data = {
    "battery_level": 85,
    "heart_rate": 72,
    "blood_oxygen": 98,
    "blood_glucose": 95.5
}

location_data = {
    "latitude": 39.7392,
    "longitude": -104.9903,
    "accuracy_meters": 5
}

result = send_device_data("VITA-2024-001234", health_data, location_data)
print(result)
```

#### Arduino/ESP32 Example
```cpp
#include <WiFi.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>

void sendDeviceData(String serialNumber, float heartRate, int batteryLevel) {
    HTTPClient http;
    http.begin("https://your-domain.com/api/device_endpoint.php");
    http.addHeader("Content-Type", "application/json");
    
    DynamicJsonDocument doc(1024);
    doc["serial_number"] = serialNumber;
    doc["heart_rate"] = heartRate;
    doc["battery_level"] = batteryLevel;
    doc["timestamp"] = "2024-01-15 14:30:00"; // Use RTC for actual timestamp
    
    String jsonString;
    serializeJson(doc, jsonString);
    
    int httpResponseCode = http.POST(jsonString);
    
    if (httpResponseCode > 0) {
        String response = http.getString();
        Serial.println("Response: " + response);
    }
    
    http.end();
}
```

### Data Retention

- **Health Metrics**: Retained according to user privacy settings (default: 365 days)
- **Location Data**: Retained according to user privacy settings (default: 90 days)
- **Device Logs**: Retained for 30 days for troubleshooting
- **Alert History**: Retained for 1 year

### Security Considerations

1. **HTTPS Only**: All communications must use HTTPS
2. **Rate Limiting**: Implement rate limiting to prevent abuse
3. **Data Validation**: All input is validated and sanitized
4. **Privacy Compliance**: Data handling complies with healthcare privacy regulations
5. **Audit Logging**: All device communications are logged for security auditing

### Troubleshooting

#### Common Issues

1. **Device Not Found**: Ensure device is properly paired in the system
2. **Invalid Data**: Check that all numeric values are within valid ranges
3. **Network Issues**: Implement retry logic with exponential backoff
4. **Battery Alerts**: Ensure battery level is reported accurately

#### Debug Mode

Add `debug=1` parameter to get detailed processing information:
```
POST /api/device_endpoint.php?debug=1
```

This will return additional information about the processing steps and any warnings.