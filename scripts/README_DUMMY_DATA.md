# VITA Dummy Data Generator

## Overview

The VITA Dummy Data Generator is a PHP script designed to send realistic test data to the VITA health monitoring system. It can generate normal, medium concern, and high-risk health values to test the platform's alerting and monitoring capabilities.

## Key Features

### 🚨 High Alert Data
- **Heart Rate**: 140-180 bpm (triggers high alerts)
- **Blood Oxygen**: 85-92% (dangerously low)
- **Blood Glucose**: 200-350 mg/dL (very high)
- **Cholesterol**: Calculated from glucose (often >240 mg/dL)
- **Temperature**: 100-104°F (fever)
- **Battery**: 5-15% (low battery alerts)

### ⚠️ Medium Concern Data
- **Heart Rate**: 100-139 bpm (elevated)
- **Blood Oxygen**: 93-95% (slightly low)
- **Blood Glucose**: 150-199 mg/dL (moderately high)
- **Cholesterol**: Calculated correlation
- **Temperature**: 99-101°F (slight fever)
- **Battery**: 15-25% (medium battery)

### ✅ Normal Data
- **Heart Rate**: 60-90 bpm (normal)
- **Blood Oxygen**: 96-99% (excellent)
- **Blood Glucose**: 80-120 mg/dL (normal)
- **Cholesterol**: 150-200 mg/dL (healthy)
- **Temperature**: 98-99°F (normal)
- **Battery**: 70-95% (good)

## Cholesterol Calculation

The system automatically calculates cholesterol from glucose levels using a medically-informed correlation:

```php
$base_cholesterol = 160; // Base level for elderly
$glucose_factor = ($glucose - 100) * 0.7; // Correlation coefficient
$age_factor = 10; // Elderly baseline adjustment
$variation = rand(-15, 15); // Natural variation

$cholesterol = $base_cholesterol + $glucose_factor + $age_factor + $variation;
```

**Medical Basis:**
- Higher glucose levels often correlate with higher cholesterol
- Elderly patients typically have slightly elevated baseline cholesterol
- Normal cholesterol: 150-200 mg/dL
- High cholesterol: >240 mg/dL (triggers alerts)

## Usage

### Basic Usage

```php
<?php
require_once 'scripts/send_dummy_data.php';

$generator = new DummyDataGenerator();

// Activate for high-risk data
$generator->activate();

// Send high alert data
$generator->sendBatchData(5, 'high');

// Send medium concern data
$generator->sendBatchData(3, 'medium');

// Send normal data (no activation required)
$generator->sendBatchData(2, 'normal');

$generator->deactivate();
?>
```

### Continuous Monitoring

```php
// Start continuous high-alert data every 30 seconds
$generator->activate();
$generator->startContinuousMode(30, 'high');
```

### Single Device Testing

```php
$generator->activate();
$generator->sendDataToDevice('VITA-2024-001234', 'high');
```

## Safety Features

### Activation Required
- High and medium alert data requires explicit activation
- Prevents accidental triggering of emergency alerts
- Normal data can be sent without activation

### Realistic Bounds
- All values are within medically realistic ranges
- Cholesterol calculation uses evidence-based correlations
- Temperature, heart rate, and other metrics follow clinical guidelines

### Device Identification
- Uses real device serial numbers from the system
- Automatically rotates between available devices
- Maintains device-specific tracking

## Command Line Interface

```bash
# Run the script
php scripts/send_dummy_data.php

# The script provides an interactive menu:
# - activate() - Enable high/medium data generation
# - sendBatchData(count, type) - Send multiple data points
# - startContinuousMode(interval, type) - Continuous sending
# - deactivate() - Disable high/medium data generation
```

## Alert Testing

The dummy data generator is specifically designed to test:

### Health Alerts
- **Heart Rate Anomalies**: Values outside 50-120 bpm
- **Blood Glucose Issues**: Values outside 70-180 mg/dL
- **Cholesterol Concerns**: Values above 240 mg/dL
- **Oxygen Saturation**: Values below 95%
- **Temperature Alerts**: Values outside 96-100.4°F

### Device Alerts
- **Low Battery**: Battery levels ≤20%
- **Connection Issues**: Simulated device offline scenarios

### Location Alerts
- **Geofence Violations**: Random locations outside safe zones
- **Emergency Location**: Rapid location changes

## Database Impact

### Data Storage
- All generated data is stored in the health_metrics table
- Location data is recorded in location_tracking
- Device status updates are logged
- System logs track all data generation

### Alert Generation
- Automatic alert creation for abnormal values
- Emergency contact notifications for high-priority alerts
- Real-time dashboard updates

## Configuration

### API Endpoint
```php
$generator = new DummyDataGenerator('http://your-domain.com');
```

### Device Serial Numbers
```php
$this->serial_numbers = [
    'VITA-2024-001234',
    'VITA-2024-001235',
    'VITA-2024-001236'
];
```

### Timing
- Default batch delay: 0.5 seconds between requests
- Default continuous interval: 30 seconds
- Configurable timeout: 30 seconds per request

## Medical Accuracy

### Cholesterol-Glucose Correlation
The correlation formula is based on medical research showing:
- Diabetic patients often have elevated cholesterol
- Glucose levels >126 mg/dL correlate with higher cardiovascular risk
- Elderly patients have naturally higher baseline cholesterol

### Realistic Ranges
- **Heart Rate**: Based on age-adjusted normal ranges
- **Blood Pressure**: Removed per requirements
- **Blood Glucose**: Follows ADA guidelines
- **Cholesterol**: Follows ATP III guidelines
- **Temperature**: Clinical fever thresholds

## Troubleshooting

### Common Issues
1. **Generator Not Activated**: High/medium data requires activation
2. **Network Errors**: Check API endpoint URL and connectivity
3. **Device Not Found**: Ensure devices are paired in the system
4. **Permission Errors**: Verify script has write access for logs

### Debug Mode
Add debug output to track data generation:
```php
$generator->sendDataToDevice('VITA-2024-001234', 'high');
// Displays detailed output of sent data
```

## Security Considerations

- Only generates test data for development/testing
- Requires explicit activation for alert-triggering data
- All data is logged for audit purposes
- Uses secure HTTPS connections to API endpoints

This dummy data generator provides a comprehensive testing framework for the VITA health monitoring platform, ensuring robust alert systems and accurate health tracking capabilities.