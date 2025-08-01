<?php
/**
 * VITA Health Platform - Dashboard
 */

require_once 'includes/db_functions.php';

// Get current user and data
$user_id = getCurrentUserId();
$user = getCurrentUser();
$db = VitaDB::getInstance();

// Get dashboard data
$latest_metrics = $db->getLatestHealthMetrics($user_id);
$recent_alerts = $db->getRecentAlerts($user_id, 5);
$devices = $db->getUserDevices($user_id);
$current_location = $db->getCurrentLocation($user_id);

// Default values if no data
$heart_rate = $latest_metrics['heart_rate'] ?? 72;
$blood_oxygen = $latest_metrics['blood_oxygen'] ?? 98;
$blood_glucose = $latest_metrics['blood_glucose'] ?? 95;
$cholesterol = $latest_metrics['cholesterol'] ?? 180;

// Device status
$device_status = 'Disconnected';
$battery_level = 0;
if (!empty($devices)) {
    $main_device = $devices[0];
    $device_status = $main_device['status'];
    $battery_level = $main_device['battery_level'] ?? 0;
}

// Location info
$location_info = 'Unknown';
if ($current_location) {
    $location_info = $current_location['location_type'] . ' • ' . $db->timeAgo($current_location['recorded_at']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VITA - Smart Elderly Care Platform</title>
    <meta name="description" content="VITA: Innovative health-tech solution for elderly care with real-time monitoring, AI alerts, and family connectivity.">
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
                <li><a href="index.php" class="nav-link active">
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
                <li><a href="device-setup.php" class="nav-link">
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
                <h1>Health Dashboard</h1>
                <p>Real-time monitoring for <?php echo htmlspecialchars($user['full_name'] ?? 'User'); ?></p>
            </div>

            <!-- Vital Signs Cards -->
            <div class="metrics-grid">
                <div class="metric-card">
                    <div class="metric-content">
                        <div class="metric-info">
                            <p class="metric-label">Heart Rate</p>
                            <p class="metric-value"><?php echo $heart_rate; ?> <span class="metric-unit">bpm</span></p>
                            <p class="metric-status normal">● Normal</p>
                        </div>
                        <div class="metric-icon heart">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="metric-card">
                    <div class="metric-content">
                        <div class="metric-info">
                            <p class="metric-label">Blood Oxygen (SpO2)</p>
                            <p class="metric-value"><?php echo $blood_oxygen; ?> <span class="metric-unit">%</span></p>
                            <p class="metric-status normal">● Normal</p>
                        </div>
                        <div class="metric-icon oxygen">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M7 19a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"></path>
                                <path d="M17 19a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"></path>
                                <path d="M12 19a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"></path>
                                <path d="M7 13a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"></path>
                                <path d="M17 13a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="metric-card">
                    <div class="metric-content">
                        <div class="metric-info">
                            <p class="metric-label">Cholesterol</p>
                            <p class="metric-value"><?php echo $cholesterol; ?> <span class="metric-unit">mg/dL</span></p>
                            <p class="metric-status normal">● Normal</p>
                        </div>
                        <div class="metric-icon cholesterol">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="metric-card">
                    <div class="metric-content">
                        <div class="metric-info">
                            <p class="metric-label">Blood Glucose</p>
                            <p class="metric-value"><?php echo $blood_glucose; ?> <span class="metric-unit">mg/dL</span></p>
                            <p class="metric-status normal">● Normal <span class="beta-tag">BETA</span></p>
                        </div>
                        <div class="metric-icon glucose">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14,2 14,8 20,8"></polyline>
                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                <line x1="16" y1="17" x2="8" y2="17"></line>
                                <polyline points="10,9 9,9 8,9"></polyline>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Dashboard Grid -->
            <div class="dashboard-grid">
                <!-- Recent Alerts -->
                <div class="card">
                    <div class="card-header">
                        <h2>Recent Alerts</h2>
                        <svg class="header-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                            <line x1="12" y1="9" x2="12" y2="13"></line>
                            <line x1="12" y1="17" x2="12.01" y2="17"></line>
                        </svg>
                    </div>
                    <div class="alerts-list">
                        <?php if (empty($recent_alerts)): ?>
                            <div class="alert-item low">
                                <div class="alert-indicator"></div>
                                <div class="alert-content">
                                    <p class="alert-message">No recent alerts</p>
                                    <p class="alert-time">All systems normal</p>
                                </div>
                            </div>
                        <?php else: ?>
                            <?php foreach ($recent_alerts as $alert): ?>
                                <div class="alert-item <?php echo strtolower($alert['priority']); ?>">
                                    <div class="alert-indicator"></div>
                                    <div class="alert-content">
                                        <p class="alert-message"><?php echo htmlspecialchars($alert['message']); ?></p>
                                        <p class="alert-time"><?php echo $db->timeAgo($alert['alert_time']); ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Device Status -->
                <div class="card">
                    <div class="card-header">
                        <h2>Device Status</h2>
                        <div class="status-indicator <?php echo $device_status === 'Connected' ? 'online' : 'offline'; ?>">
                            <div class="status-dot"></div>
                            <span><?php echo $device_status; ?></span>
                        </div>
                    </div>
                    <div class="device-status-list">
                        <div class="device-item">
                            <div class="device-info">
                                <svg class="device-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="1" y="3" width="15" height="13"></rect>
                                    <polygon points="16,3 16,13 22,8 16,3"></polygon>
                                </svg>
                                <span>Battery Level</span>
                            </div>
                            <div class="battery-indicator">
                                <div class="battery-bar">
                                    <div class="battery-fill" style="width: <?php echo $battery_level; ?>%"></div>
                                </div>
                                <span><?php echo $battery_level; ?>%</span>
                            </div>
                        </div>
                        <div class="device-item">
                            <div class="device-info">
                                <svg class="device-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M5 12.55a11 11 0 0 1 14.08 0"></path>
                                    <path d="M1.42 9a16 16 0 0 1 21.16 0"></path>
                                    <path d="M8.53 16.11a6 6 0 0 1 6.95 0"></path>
                                    <line x1="12" y1="20" x2="12.01" y2="20"></line>
                                </svg>
                                <span>Connection</span>
                            </div>
                            <span class="connection-status"><?php echo $device_status === 'Connected' ? 'Strong' : 'Disconnected'; ?></span>
                        </div>
                        <div class="device-item">
                            <div class="device-info">
                                <svg class="device-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                    <circle cx="12" cy="10" r="3"></circle>
                                </svg>
                                <span>Last Location</span>
                            </div>
                            <span class="location-info"><?php echo htmlspecialchars($location_info); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h2>Quick Actions</h2>
                </div>
                <div class="quick-actions">
                    <button class="action-btn emergency">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                            <line x1="12" y1="9" x2="12" y2="13"></line>
                            <line x1="12" y1="17" x2="12.01" y2="17"></line>
                        </svg>
                        <span>Emergency Alert</span>
                    </button>
                    <button class="action-btn medication">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4.5 16.5c-1.5 1.5-1.5 3.5 0 5s3.5 1.5 5 0l12-12c1.5-1.5 1.5-3.5 0-5s-3.5-1.5-5 0l-12 12z"></path>
                            <path d="M15 7l3 3"></path>
                        </svg>
                        <span>Add Medication</span>
                    </button>
                    <button class="action-btn location">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                            <circle cx="12" cy="10" r="3"></circle>
                        </svg>
                        <span>Set Safe Zone</span>
                    </button>
                    <button class="action-btn health">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                        </svg>
                        <span>Health Report</span>
                    </button>
                </div>
            </div>
        </main>
    </div>

    <!-- Mobile Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <script src="assets/js/main.js"></script>
</body>
</html>