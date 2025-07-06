# VITA Health Platform - Backend API

This is the complete backend API system for the VITA Health Platform, providing comprehensive endpoints for managing elderly care monitoring.

## Features

- **User Management**: Complete user profile management with medical information
- **Health Metrics**: Real-time health data recording and analysis
- **Medication Management**: Medication tracking, scheduling, and reminders
- **Device Management**: Device pairing, configuration, and monitoring
- **Location Tracking**: GPS tracking with safe zones and geofencing
- **Alerts System**: Comprehensive alerting with emergency notifications
- **Emergency Contacts**: Emergency contact management with notification preferences
- **User Preferences**: Customizable app and privacy settings

## Database Schema

The system uses MySQL with the following main tables:
- `users` - User profiles and basic information
- `health_metrics` - Health data recordings
- `medications` - Medication information and schedules
- `devices` - Device management and configuration
- `location_tracking` - GPS location history
- `safe_zones` - Geofenced safe areas
- `alerts` - System alerts and notifications
- `emergency_contacts` - Emergency contact information
- `user_preferences` - App and privacy settings

## API Endpoints

### Users API (`/api/users.php`)
- `POST` - Create new user
- `PUT` - Update user profile
- `GET` - Get user information

### Health Metrics API (`/api/health_metrics.php`)
- `POST` - Record health metrics
- `GET` - Retrieve health data with period filtering

### Medications API (`/api/medications.php`)
- `POST` - Add medication or mark as taken
- `PUT` - Update medication information
- `GET` - Get medications list or today's schedule

### Devices API (`/api/devices.php`)
- `POST` - Pair new device
- `PUT` - Update device status or configuration
- `GET` - Get user's devices

### Location API (`/api/location.php`)
- `POST` - Update location or add safe zone
- `GET` - Get current location, history, or safe zones

### Alerts API (`/api/alerts.php`)
- `POST` - Create alert or trigger emergency
- `PUT` - Dismiss alerts
- `GET` - Get user alerts

### Emergency Contacts API (`/api/emergency_contacts.php`)
- `POST` - Add emergency contact
- `PUT` - Update contact information
- `GET` - Get emergency contacts
- `DELETE` - Remove emergency contact

### Preferences API (`/api/preferences.php`)
- `PUT` - Update user preferences or privacy settings
- `GET` - Get user preferences or privacy settings

## Setup Instructions

1. **Database Setup**:
   ```sql
   CREATE DATABASE vita_health_platform;
   USE vita_health_platform;
   SOURCE database/schema.sql;
   ```

2. **Configuration**:
   - Update database credentials in `config/database.php`
   - Configure web server to point to the API directory

3. **API Usage**:
   - All endpoints return JSON responses
   - Use appropriate HTTP methods (GET, POST, PUT, DELETE)
   - Include `Content-Type: application/json` header for POST/PUT requests

## Example API Calls

### Create User
```bash
curl -X POST http://your-domain/api/users.php \
  -H "Content-Type: application/json" \
  -d '{
    "full_name": "Margaret Thompson",
    "date_of_birth": "1952-03-15",
    "phone": "(555) 123-4567",
    "email": "margaret@email.com"
  }'
```

### Record Health Metrics
```bash
curl -X POST http://your-domain/api/health_metrics.php \
  -H "Content-Type: application/json" \
  -d '{
    "user_id": 1,
    "heart_rate": 72,
    "blood_oxygen": 98,
    "blood_glucose": 95
  }'
```

### Add Medication
```bash
curl -X POST http://your-domain/api/medications.php \
  -H "Content-Type: application/json" \
  -d '{
    "user_id": 1,
    "name": "Lisinopril",
    "dosage": "10mg",
    "frequency": "Once daily",
    "start_date": "2024-01-01",
    "reminder_times": ["08:00"]
  }'
```

## Security Features

- Input validation and sanitization
- SQL injection prevention using prepared statements
- Error handling and logging
- API response standardization
- Emergency notification system

## Error Handling

All endpoints return standardized JSON responses:

**Success Response:**
```json
{
  "status": "success",
  "message": "Operation completed successfully",
  "data": {...},
  "timestamp": "2024-01-15 10:30:00"
}
```

**Error Response:**
```json
{
  "status": "error",
  "message": "Error description",
  "details": {...},
  "timestamp": "2024-01-15 10:30:00"
}
```

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- PDO extension enabled
- JSON extension enabled

## License

This project is part of the VITA Health Platform system.