<?php
/**
 * VITA Health Platform - Device Setup
 */

require_once 'includes/db_functions.php';

// Get current user and data
$user_id = getCurrentUserId();
$user = getCurrentUser();
$db = VitaDB::getInstance();

// Get user's devices
$devices = $db->getUserDevices($user_id);
$preferences = $db->getUserPreferences($user_id);

// Count device statuses
$connected_devices = array_filter($devices, function($device) {
    return $device['status'] === 'Connected';
});

$disconnected_devices = array_filter($devices, function($device) {
    return $device['status'] !== 'Connected';
});
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Device Setup - VITA</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="app-container">
        <!-- Sidebar -->
        <nav class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <img style="width: 40px;" src="/assets/justlogo.png">
                    <span class="logo-text">VITA</span>
                </div>
                <button class="sidebar-close" id="sidebarClose">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            <ul class="nav-menu">
                <li><a href="index.php" class="nav-link">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="22,12 18,12 15,21 9,3 6,12 2,12"></polyline>
                    </svg>
                    Dashboard
                </a></li>
                <li><a href="health-metrics.php" class="nav-link">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                    </svg>
                    Health Metrics
                </a></li>
                <li><a href="location.php" class="nav-link">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                        <circle cx="12" cy="10" r="3"></circle>
                    </svg>
                    Location
                </a></li>
                <li><a href="medication.php" class="nav-link">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4.5 16.5c-1.5 1.5-1.5 3.5 0 5s3.5 1.5 5 0l12-12c1.5-1.5 1.5-3.5 0-5s-3.5-1.5-5 0l-12 12z"></path>
                        <path d="M15 7l3 3"></path>
                    </svg>
                    Medication
                </a></li>
                <li><a href="alerts.php" class="nav-link">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                        <line x1="12" y1="9" x2="12" y2="13"></line>
                        <line x1="12" y1="17" x2="12.01" y2="17"></line>
                    </svg>
                    Alerts
                </a></li>
                <li><a href="profile.php" class="nav-link">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                    Profile
                </a></li>
                <li><a href="device-setup.php" class="nav-link active">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="3"></circle>
                        <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1 1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                    </svg>
                    Device Setup
                </a></li>
            </ul>
        </nav>

        <!-- Mobile Header -->
        <header class="mobile-header">
            <button class="mobile-menu-btn" id="mobileMenuBtn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <line x1="3" y1="12" x2="21" y2="12"></line>
                    <line x1="3" y1="18" x2="21" y2="18"></line>
                </svg>
            </button>
            <div class="mobile-logo">
                <img style="width: 40px;" src="/assets/justlogo.png">
                <span class="logo-text">VITA</span>
            </div>
        </header>

        <!-- Main Content -->
        <main class="main-content">
            <div class="page-header">
                <h1>Device Setup</h1>
                <p>Configure and manage your VITA health monitoring devices</p>
            </div>

            <!-- Device Status Overview -->
            <div class="card device-overview">
                <div class="card-header">
                    <h2>Device Status</h2>
                    <div class="status-indicator <?php echo count($connected_devices) > 0 ? 'online' : 'offline'; ?>">
                        <div class="status-dot"></div>
                        <span><?php echo count($connected_devices); ?> of <?php echo count($devices); ?> Devices Connected</span>
                    </div>
                </div>
                
                <div class="device-grid">
                    <?php if (empty($devices)): ?>
                        <div class="device-card disconnected">
                            <div class="device-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                                    <line x1="8" y1="21" x2="16" y2="21"></line>
                                    <line x1="12" y1="17" x2="12" y2="21"></line>
                                </svg>
                            </div>
                            <div class="device-info">
                                <h3>No Devices</h3>
                                <p>No devices paired yet</p>
                                <span class="device-status disconnected">Setup Required</span>
                            </div>
                            <button class="setup-btn">Setup Device</button>
                        </div>
                    <?php else: ?>
                        <?php foreach ($devices as $device): ?>
                            <div class="device-card <?php echo strtolower($device['status']); ?>">
                                <div class="device-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                                        <line x1="8" y1="21" x2="16" y2="21"></line>
                                        <line x1="12" y1="17" x2="12" y2="21"></line>
                                    </svg>
                                </div>
                                <div class="device-info">
                                    <h3><?php echo htmlspecialchars($device['nickname'] ?: $device['device_type']); ?></h3>
                                    <p><?php echo htmlspecialchars($device['device_type']); ?></p>
                                    <span class="device-status <?php echo strtolower($device['status']); ?>">
                                        <?php echo $device['status']; ?>
                                    </span>
                                </div>
                                <?php if ($device['status'] === 'Connected'): ?>
                                    <div class="device-battery">
                                        <div class="battery-indicator">
                                            <div class="battery-bar">
                                                <div class="battery-fill" style="width: <?php echo $device['battery_level']; ?>%"></div>
                                            </div>
                                            <span><?php echo $device['battery_level']; ?>%</span>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <button class="setup-btn">Setup Device</button>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Device Configuration and Troubleshooting -->
            <div class="dashboard-grid">
                <!-- Device Configuration -->
                <div class="card">
                    <div class="card-header">
                        <h2>Device Configuration</h2>
                        <svg class="header-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="3"></circle>
                            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1 1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                        </svg>
                    </div>
                    
                    <div class="config-list">
                        <div class="config-item">
                            <div class="config-info">
                                <h3>Measurement Frequency</h3>
                                <p>How often devices take readings</p>
                            </div>
                            <select class="config-select">
                                <option <?php echo (!empty($devices) && $devices[0]['measurement_frequency'] === 'Every 5 min') ? 'selected' : ''; ?>>Every 5 minutes</option>
                                <option <?php echo (!empty($devices) && $devices[0]['measurement_frequency'] === 'Every 15 min') ? 'selected' : ''; ?>>Every 15 minutes</option>
                                <option <?php echo (!empty($devices) && $devices[0]['measurement_frequency'] === 'Every 30 min') ? 'selected' : ''; ?>>Every 30 minutes</option>
                                <option <?php echo (!empty($devices) && $devices[0]['measurement_frequency'] === 'Hourly') ? 'selected' : ''; ?>>Every hour</option>
                            </select>
                        </div>

                        <div class="config-item">
                            <div class="config-info">
                                <h3>Alert Thresholds</h3>
                                <p>Customize when to receive alerts</p>
                            </div>
                            <button class="config-btn">Configure</button>
                        </div>

                        <div class="config-item">
                            <div class="config-info">
                                <h3>Data Sync</h3>
                                <p>Automatic cloud synchronization</p>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" <?php echo (!empty($devices) && $devices[0]['auto_sync']) ? 'checked' : ''; ?>>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>

                        <div class="config-item">
                            <div class="config-info">
                                <h3>Power Saving Mode</h3>
                                <p>Extend battery life when needed</p>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" <?php echo (!empty($devices) && $devices[0]['power_saving_mode']) ? 'checked' : ''; ?>>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Troubleshooting -->
                <div class="card">
                    <div class="card-header">
                        <h2>Troubleshooting</h2>
                        <svg class="header-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                            <line x1="12" y1="17" x2="12.01" y2="17"></line>
                        </svg>
                    </div>
                    
                    <div class="troubleshoot-list">
                        <div class="troubleshoot-item">
                            <h3>Connection Issues</h3>
                            <p>Device not connecting or frequently disconnecting</p>
                            <div class="troubleshoot-actions">
                                <button class="troubleshoot-btn">Restart Device</button>
                                <button class="troubleshoot-btn">Reset Connection</button>
                            </div>
                        </div>

                        <div class="troubleshoot-item">
                            <h3>Battery Problems</h3>
                            <p>Device battery draining quickly</p>
                            <div class="troubleshoot-actions">
                                <button class="troubleshoot-btn">Check Settings</button>
                                <button class="troubleshoot-btn">Calibrate Battery</button>
                            </div>
                        </div>

                        <div class="troubleshoot-item">
                            <h3>Inaccurate Readings</h3>
                            <p>Health measurements seem incorrect</p>
                            <div class="troubleshoot-actions">
                                <button class="troubleshoot-btn">Calibrate Sensors</button>
                                <button class="troubleshoot-btn">Contact Support</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Device Setup Wizard -->
            <div class="card">
                <div class="card-header">
                    <h2>Add New Device</h2>
                    <svg class="header-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                </div>
                
                <div class="setup-wizard">
                    <div class="wizard-steps">
                        <div class="step active">
                            <div class="step-number">1</div>
                            <span>Select Device</span>
                        </div>
                        <div class="step">
                            <div class="step-number">2</div>
                            <span>Pair Device</span>
                        </div>
                        <div class="step">
                            <div class="step-number">3</div>
                            <span>Configure</span>
                        </div>
                        <div class="step">
                            <div class="step-number">4</div>
                            <span>Test</span>
                        </div>
                    </div>

                    <div class="device-selection">
                        <h3>Select Device Type</h3>
                        <div class="device-types">
                            <div class="device-type-card">
                                <div class="device-type-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                                        <line x1="8" y1="21" x2="16" y2="21"></line>
                                        <line x1="12" y1="17" x2="12" y2="21"></line>
                                    </svg>
                                </div>
                                <h4>VITA Watch Pro</h4>
                                <p>Heart rate, activity, cholesterol tracking</p>
                                <button class="select-device-btn">Select</button>
                            </div>

                            <div class="device-type-card">
                                <div class="device-type-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                        <polyline points="14,2 14,8 20,8"></polyline>
                                        <line x1="16" y1="13" x2="8" y2="13"></line>
                                        <line x1="16" y1="17" x2="8" y2="17"></line>
                                    </svg>
                                </div>
                                <h4>Glucose Meter</h4>
                                <p>Blood sugar monitoring</p>
                                <button class="select-device-btn">Select</button>
                            </div>

                            <div class="device-type-card">
                                <div class="device-type-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                                        <line x1="12" y1="9" x2="12" y2="13"></line>
                                        <line x1="12" y1="17" x2="12.01" y2="17"></line>
                                    </svg>
                                </div>
                                <h4>Emergency Button</h4>
                                <p>Wearable panic button</p>
                                <button class="select-device-btn">Select</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Support & Documentation -->
            <div class="card">
                <div class="card-header">
                    <h2>Support & Documentation</h2>
                    <svg class="header-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 11H5a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h4l3 3h4a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-4l-3-3z"></path>
                        <path d="M22 4L12 14l-3-3"></path>
                    </svg>
                </div>
                
                <div class="support-options">
                    <div class="support-item">
                        <div class="support-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14,2 14,8 20,8"></polyline>
                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                <line x1="16" y1="17" x2="8" y2="17"></line>
                            </svg>
                        </div>
                        <div class="support-info">
                            <h3>User Manual</h3>
                            <p>Complete guide for all devices</p>
                        </div>
                        <button class="support-btn">Download PDF</button>
                    </div>

                    <div class="support-item">
                        <div class="support-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polygon points="10,8 16,12 10,16 10,8"></polygon>
                            </svg>
                        </div>
                        <div class="support-info">
                            <h3>Video Tutorials</h3>
                            <p>Step-by-step setup guides</p>
                        </div>
                        <button class="support-btn">Watch Videos</button>
                    </div>

                    <div class="support-item">
                        <div class="support-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                            </svg>
                        </div>
                        <div class="support-info">
                            <h3>Technical Support</h3>
                            <p>24/7 phone and chat support</p>
                        </div>
                        <button class="support-btn">Contact Support</button>
                    </div>

                    <div class="support-item">
                        <div class="support-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                            </svg>
                        </div>
                        <div class="support-info">
                            <h3>Community Forum</h3>
                            <p>Connect with other users</p>
                        </div>
                        <button class="support-btn">Visit Forum</button>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Mobile Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <script src="assets/js/main.js"></script>
    <script src="assets/js/device-setup.js"></script>
</body>
</html>