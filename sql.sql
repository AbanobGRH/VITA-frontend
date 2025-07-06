-- Users Table
CREATE TABLE users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    full_name VARCHAR(100),
    date_of_birth DATE,
    patient_id VARCHAR(50) UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Devices Table
CREATE TABLE devices (
    device_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    device_type VARCHAR(50),
    serial_number VARCHAR(100) UNIQUE,
    nickname VARCHAR(50),
    status ENUM('Connected', 'Disconnected', 'Setup Required') DEFAULT 'Setup Required',
    last_check_in TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- Health Metrics Table
CREATE TABLE health_metrics (
    metric_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    heart_rate INT,
    blood_glucose DECIMAL(5,2),
    recorded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- Alerts Table
CREATE TABLE alerts (
    alert_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    device_id INT,
    alert_type VARCHAR(50), -- e.g., Battery Low, Heart Rate Anomaly
    message TEXT,
    is_dismissed BOOLEAN DEFAULT FALSE,
    alert_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (device_id) REFERENCES devices(device_id)
);

-- Medications Table
CREATE TABLE medications (
    medication_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    name VARCHAR(100),
    dosage VARCHAR(50),
    frequency VARCHAR(100),
    start_date DATE,
    end_date DATE,
    reminder_enabled BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- Preferences Table
CREATE TABLE preferences (
    preference_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    auto_sync BOOLEAN DEFAULT TRUE,
    voice_alerts BOOLEAN DEFAULT TRUE,
    vibration BOOLEAN DEFAULT TRUE,
    large_text BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- Privacy Settings Table
CREATE TABLE privacy_settings (
    privacy_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    location_tracking BOOLEAN DEFAULT TRUE,
    data_sharing BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- Emergency Contacts Table
CREATE TABLE emergency_contacts (
    contact_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    name VARCHAR(100),
    relationship VARCHAR(50),
    phone VARCHAR(20),
    email VARCHAR(100),
    is_primary BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- Family Access Table
CREATE TABLE family_access (
    access_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    family_member_id INT,
    access_level ENUM('View Only', 'Full Access'),
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (family_member_id) REFERENCES users(user_id)
);