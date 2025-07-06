-- VITA Health Platform Database Schema
-- Complete database structure for all platform features

-- Users Table (Enhanced)
CREATE TABLE users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    full_name VARCHAR(100) NOT NULL,
    date_of_birth DATE,
    patient_id VARCHAR(50) UNIQUE NOT NULL,
    phone VARCHAR(20),
    email VARCHAR(100) UNIQUE,
    address TEXT,
    blood_type VARCHAR(5),
    insurance_info VARCHAR(200),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Medical Conditions Table
CREATE TABLE medical_conditions (
    condition_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    condition_name VARCHAR(100),
    diagnosed_date DATE,
    status ENUM('Active', 'Inactive', 'Resolved') DEFAULT 'Active',
    notes TEXT,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Allergies Table
CREATE TABLE allergies (
    allergy_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    allergen VARCHAR(100),
    severity ENUM('Mild', 'Moderate', 'Severe') DEFAULT 'Moderate',
    reaction_description TEXT,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Healthcare Providers Table
CREATE TABLE healthcare_providers (
    provider_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    name VARCHAR(100),
    specialty VARCHAR(100),
    phone VARCHAR(20),
    email VARCHAR(100),
    address TEXT,
    is_primary BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Devices Table (Enhanced)
CREATE TABLE devices (
    device_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    device_type VARCHAR(50),
    serial_number VARCHAR(100) UNIQUE,
    nickname VARCHAR(50),
    status ENUM('Connected', 'Disconnected', 'Setup Required') DEFAULT 'Setup Required',
    battery_level INT DEFAULT 100,
    last_sync TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    firmware_version VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Device Configuration Table
CREATE TABLE device_configurations (
    config_id INT PRIMARY KEY AUTO_INCREMENT,
    device_id INT,
    measurement_frequency ENUM('Every 5 min', 'Every 15 min', 'Every 30 min', 'Hourly') DEFAULT 'Every 15 min',
    power_saving_mode BOOLEAN DEFAULT FALSE,
    auto_sync BOOLEAN DEFAULT TRUE,
    alert_thresholds JSON,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (device_id) REFERENCES devices(device_id) ON DELETE CASCADE
);

-- Health Metrics Table (Enhanced)
CREATE TABLE health_metrics (
    metric_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    device_id INT,
    heart_rate INT,
    blood_pressure_systolic INT,
    blood_pressure_diastolic INT,
    blood_oxygen INT,
    blood_glucose DECIMAL(5,2),
    temperature DECIMAL(4,1),
    activity_level VARCHAR(50),
    location_context VARCHAR(100),
    recorded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (device_id) REFERENCES devices(device_id) ON DELETE SET NULL
);

-- Medications Table (Enhanced)
CREATE TABLE medications (
    medication_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    name VARCHAR(100) NOT NULL,
    dosage VARCHAR(50),
    frequency VARCHAR(100),
    condition_for VARCHAR(100),
    instructions TEXT,
    start_date DATE,
    end_date DATE,
    reminder_enabled BOOLEAN DEFAULT TRUE,
    reminder_times JSON, -- Store multiple reminder times
    refill_reminder_days INT DEFAULT 7,
    current_stock INT DEFAULT 30,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Medication Schedule Table
CREATE TABLE medication_schedule (
    schedule_id INT PRIMARY KEY AUTO_INCREMENT,
    medication_id INT,
    scheduled_time TIME,
    taken_at TIMESTAMP NULL,
    status ENUM('Pending', 'Taken', 'Missed', 'Skipped') DEFAULT 'Pending',
    notes TEXT,
    scheduled_date DATE,
    FOREIGN KEY (medication_id) REFERENCES medications(medication_id) ON DELETE CASCADE
);

-- Location Tracking Table
CREATE TABLE location_tracking (
    location_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    accuracy_meters INT,
    address TEXT,
    location_type VARCHAR(50), -- 'Home', 'Safe Zone', 'Unknown', etc.
    recorded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Safe Zones Table
CREATE TABLE safe_zones (
    zone_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    name VARCHAR(100),
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    radius_meters INT DEFAULT 50,
    address TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Alerts Table (Enhanced)
CREATE TABLE alerts (
    alert_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    device_id INT,
    alert_type ENUM('Health', 'Medication', 'Device', 'Location', 'Emergency') NOT NULL,
    priority ENUM('Low', 'Medium', 'High', 'Critical') DEFAULT 'Medium',
    title VARCHAR(200),
    message TEXT,
    is_dismissed BOOLEAN DEFAULT FALSE,
    dismissed_at TIMESTAMP NULL,
    alert_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    metadata JSON, -- Additional alert-specific data
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (device_id) REFERENCES devices(device_id) ON DELETE SET NULL
);

-- Emergency Contacts Table (Enhanced)
CREATE TABLE emergency_contacts (
    contact_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    name VARCHAR(100) NOT NULL,
    relationship VARCHAR(50),
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(100),
    priority_level ENUM('Primary', 'Secondary', 'Emergency Only') DEFAULT 'Secondary',
    can_receive_health_data BOOLEAN DEFAULT FALSE,
    can_receive_location_data BOOLEAN DEFAULT FALSE,
    can_receive_alerts BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Family Access Table (Enhanced)
CREATE TABLE family_access (
    access_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    family_member_name VARCHAR(100),
    family_member_email VARCHAR(100),
    relationship VARCHAR(50),
    access_level ENUM('Full Access', 'Health Data Only', 'Emergency Only', 'Medical Access') DEFAULT 'Health Data Only',
    permissions JSON, -- Detailed permissions
    status ENUM('Active', 'Pending', 'Revoked') DEFAULT 'Pending',
    invited_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    activated_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- User Preferences Table (Enhanced)
CREATE TABLE user_preferences (
    preference_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    -- App Preferences
    dark_mode BOOLEAN DEFAULT FALSE,
    large_text BOOLEAN DEFAULT FALSE,
    voice_alerts BOOLEAN DEFAULT TRUE,
    vibration BOOLEAN DEFAULT TRUE,
    auto_sync BOOLEAN DEFAULT TRUE,
    -- Notification Preferences
    heart_rate_alerts BOOLEAN DEFAULT TRUE,
    blood_glucose_alerts BOOLEAN DEFAULT TRUE,
    medication_reminders BOOLEAN DEFAULT TRUE,
    geofence_alerts BOOLEAN DEFAULT TRUE,
    check_in_reminders BOOLEAN DEFAULT TRUE,
    -- Emergency Settings
    auto_emergency_call BOOLEAN DEFAULT TRUE,
    fall_detection BOOLEAN DEFAULT TRUE,
    emergency_timeout_minutes INT DEFAULT 5,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Privacy Settings Table (Enhanced)
CREATE TABLE privacy_settings (
    privacy_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    location_tracking BOOLEAN DEFAULT TRUE,
    data_sharing_research BOOLEAN DEFAULT FALSE,
    data_sharing_family BOOLEAN DEFAULT TRUE,
    health_data_retention_days INT DEFAULT 365,
    location_data_retention_days INT DEFAULT 90,
    two_factor_enabled BOOLEAN DEFAULT FALSE,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- System Logs Table
CREATE TABLE system_logs (
    log_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    action VARCHAR(100),
    details TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL
);

-- Create indexes for better performance
CREATE INDEX idx_users_patient_id ON users(patient_id);
CREATE INDEX idx_health_metrics_user_date ON health_metrics(user_id, recorded_at);
CREATE INDEX idx_alerts_user_type ON alerts(user_id, alert_type, alert_time);
CREATE INDEX idx_medications_user ON medications(user_id);
CREATE INDEX idx_location_tracking_user_date ON location_tracking(user_id, recorded_at);
CREATE INDEX idx_emergency_contacts_user ON emergency_contacts(user_id);
CREATE INDEX idx_devices_user ON devices(user_id);