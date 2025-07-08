<?php
/**
 * VITA Health Platform - Alerts & Notifications
 */

require_once 'includes/db_functions.php';

// Get current user and data
$user_id = getCurrentUserId();
$user = getCurrentUser();
$db = VitaDB::getInstance();

// Get alerts and emergency contacts
$recent_alerts = $db->getRecentAlerts($user_id, 10);
$emergency_contacts = $db->getEmergencyContacts($user_id);
$preferences = $db->getUserPreferences($user_id);

// Count alert priorities
$high_priority_alerts = array_filter($recent_alerts, function($alert) {
    return $alert['priority'] === 'High' || $alert['priority'] === 'Critical';
});
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alerts & Notifications - VITA</title>
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
                <li><a href="alerts.php" class="nav-link active">
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
                <h1>Alerts & Notifications</h1>
                <p>Emergency contacts and notification settings</p>
            </div>

            <!-- Emergency Status -->
            <div class="card emergency-status">
                <div class="card-header">
                    <h2>Emergency Status</h2>
                    <div class="status-indicator <?php echo count($high_priority_alerts) > 0 ? 'emergency' : 'online'; ?>">
                        <div class="status-dot"></div>
                        <span><?php echo count($high_priority_alerts) > 0 ? count($high_priority_alerts) . ' High Priority Alerts' : 'All Systems Active'; ?></span>
                    </div>
                </div>
                
                <div class="emergency-actions">
                    <button class="emergency-btn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                            <line x1="12" y1="9" x2="12" y2="13"></line>
                            <line x1="12" y1="17" x2="12.01" y2="17"></line>
                        </svg>
                        <span>Emergency Alert</span>
                    </button>
                    <button class="test-btn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="22,12 18,12 15,21 9,3 6,12 2,12"></polyline>
                        </svg>
                        <span>Test System</span>
                    </button>
                </div>
            </div>

            <!-- Recent Alerts and Emergency Contacts -->
            <div class="dashboard-grid">
                <!-- Recent Alerts -->
                <div class="card">
                    <div class="card-header">
                        <h2>Recent Alerts</h2>
                        <?php if (!empty($recent_alerts)): ?>
                            <button class="clear-alerts-btn">Clear All</button>
                        <?php endif; ?>
                    </div>
                    
                    <div class="alerts-list">
                        <?php if (empty($recent_alerts)): ?>
                            <div class="alert-item low">
                                <div class="alert-indicator"></div>
                                <div class="alert-content">
                                    <div class="alert-header">
                                        <span class="alert-type">System</span>
                                        <span class="alert-time">Now</span>
                                    </div>
                                    <p class="alert-message">No recent alerts - all systems normal</p>
                                </div>
                            </div>
                        <?php else: ?>
                            <?php foreach ($recent_alerts as $alert): ?>
                                <div class="alert-item <?php echo strtolower($alert['priority']); ?>">
                                    <div class="alert-indicator"></div>
                                    <div class="alert-content">
                                        <div class="alert-header">
                                            <span class="alert-type"><?php echo htmlspecialchars($alert['alert_type']); ?></span>
                                            <span class="alert-time"><?php echo $db->timeAgo($alert['alert_time']); ?></span>
                                        </div>
                                        <p class="alert-message"><?php echo htmlspecialchars($alert['message']); ?></p>
                                        <div class="alert-actions">
                                            <?php if ($alert['alert_type'] === 'Medication'): ?>
                                                <button class="alert-action-btn">Mark as Taken</button>
                                                <button class="alert-action-btn">Snooze</button>
                                            <?php elseif ($alert['alert_type'] === 'Health'): ?>
                                                <button class="alert-action-btn">View Details</button>
                                                <button class="alert-action-btn">Dismiss</button>
                                            <?php else: ?>
                                                <button class="alert-action-btn">Dismiss</button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Emergency Contacts -->
                <div class="card">
                    <div class="card-header">
                        <h2>Emergency Contacts</h2>
                        <button class="add-contact-btn">+ Add Contact</button>
                    </div>
                    
                    <div class="contacts-list">
                        <?php if (empty($emergency_contacts)): ?>
                            <div class="contact-item">
                                <div class="contact-avatar">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="12" cy="7" r="4"></circle>
                                    </svg>
                                </div>
                                <div class="contact-info">
                                    <h3>No Emergency Contacts</h3>
                                    <p>Add emergency contacts for safety</p>
                                </div>
                            </div>
                        <?php else: ?>
                            <?php foreach ($emergency_contacts as $contact): ?>
                                <div class="contact-item <?php echo $contact['priority_level'] === 'Primary' ? 'primary' : ''; ?>">
                                    <div class="contact-avatar">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                            <circle cx="12" cy="7" r="4"></circle>
                                        </svg>
                                    </div>
                                    <div class="contact-info">
                                        <h3><?php echo htmlspecialchars($contact['name']); ?></h3>
                                        <p><?php echo htmlspecialchars($contact['relationship']); ?> • <?php echo htmlspecialchars($contact['priority_level']); ?></p>
                                        <span class="contact-phone"><?php echo htmlspecialchars($contact['phone']); ?></span>
                                    </div>
                                    <div class="contact-actions">
                                        <button class="contact-action-btn call <?php echo $contact['phone'] === '911' ? 'emergency' : ''; ?>">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                                            </svg>
                                        </button>
                                        <?php if ($contact['phone'] !== '911'): ?>
                                            <button class="contact-action-btn edit">Edit</button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Notification Settings -->
            <div class="card">
                <div class="card-header">
                    <h2>Notification Settings</h2>
                    <svg class="header-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                    </svg>
                </div>
                
                <div class="notification-settings">
                    <div class="setting-group">
                        <h3>Health Alerts</h3>
                        <div class="setting-item">
                            <div class="setting-info">
                                <span>Heart Rate Anomalies</span>
                                <p>Alert when heart rate is outside normal range</p>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" <?php echo ($preferences && $preferences['heart_rate_alerts']) ? 'checked' : ''; ?>>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                        <div class="setting-item">
                            <div class="setting-info">
                                <span>Cholesterol Alerts</span>
                                <p>Notify for high cholesterol readings</p>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" <?php echo ($preferences && $preferences['cholesterol_alerts']) ? 'checked' : ''; ?>>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                        <div class="setting-item">
                            <div class="setting-info">
                                <span>Blood Glucose Alerts</span>
                                <p>Alert for abnormal glucose levels</p>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" <?php echo ($preferences && $preferences['blood_glucose_alerts']) ? 'checked' : ''; ?>>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                    </div>

                    <div class="setting-group">
                        <h3>Medication Reminders</h3>
                        <div class="setting-item">
                            <div class="setting-info">
                                <span>Dose Reminders</span>
                                <p>Remind when it's time to take medication</p>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" <?php echo ($preferences && $preferences['medication_reminders']) ? 'checked' : ''; ?>>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                    </div>

                    <div class="setting-group">
                        <h3>Emergency Settings</h3>
                        <div class="setting-item">
                            <div class="setting-info">
                                <span>Auto Emergency Call</span>
                                <p>Automatically call emergency contacts if no response</p>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" <?php echo ($preferences && $preferences['auto_emergency_call']) ? 'checked' : ''; ?>>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                        <div class="setting-item">
                            <div class="setting-info">
                                <span>Fall Detection</span>
                                <p>Alert contacts if a fall is detected</p>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" <?php echo ($preferences && $preferences['fall_detection']) ? 'checked' : ''; ?>>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Mobile Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <script src="assets/js/main.js"></script>
    <script src="assets/js/alerts.js"></script>
</body>
</html>