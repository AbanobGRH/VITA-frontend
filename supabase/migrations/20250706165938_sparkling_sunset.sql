-- Remove blood pressure columns and add cholesterol
-- This script updates the existing schema to remove blood pressure and add cholesterol

-- Add cholesterol column to health_metrics table
ALTER TABLE health_metrics 
ADD COLUMN cholesterol INT AFTER blood_glucose,
DROP COLUMN blood_pressure_systolic,
DROP COLUMN blood_pressure_diastolic;

-- Update device configurations to remove blood pressure thresholds
UPDATE device_configurations 
SET alert_thresholds = JSON_REPLACE(
    COALESCE(alert_thresholds, '{}'),
    '$.cholesterol', JSON_OBJECT('low', 150, 'high', 240)
)
WHERE alert_thresholds IS NOT NULL;

-- Remove blood pressure from existing alert thresholds
UPDATE device_configurations 
SET alert_thresholds = JSON_REMOVE(alert_thresholds, '$.blood_pressure')
WHERE JSON_EXTRACT(alert_thresholds, '$.blood_pressure') IS NOT NULL;

-- Update any existing alerts that mention blood pressure
UPDATE alerts 
SET message = REPLACE(message, 'blood pressure', 'cholesterol'),
    title = REPLACE(title, 'Blood Pressure', 'Cholesterol')
WHERE message LIKE '%blood pressure%' OR title LIKE '%Blood Pressure%';

-- Add index for cholesterol queries
CREATE INDEX idx_health_metrics_cholesterol ON health_metrics(cholesterol);

-- Update user preferences to replace blood pressure alerts with cholesterol alerts
ALTER TABLE user_preferences 
ADD COLUMN cholesterol_alerts BOOLEAN DEFAULT TRUE AFTER blood_glucose_alerts,
DROP COLUMN blood_pressure_alerts;