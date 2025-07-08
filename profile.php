<?php
/**
 * VITA Health Platform - User Profile
 */

require_once 'includes/db_functions.php';

// Get current user and data
$user_id = getCurrentUserId();
$user = getCurrentUser();
$db = VitaDB::getInstance();

// Get additional profile data
$medical_conditions = $db->getMedicalConditions($user_id);
$allergies = $db->getAllergies($user_id);
$healthcare_providers = $db->getHealthcareProviders($user_id);
$family_members = $db->getFamilyMembers($user_id);
$preferences = $db->getUserPreferences($user_id);
$privacy_settings = $db->getPrivacySettings($user_id);

// Calculate age
$age = 'Unknown';
if ($user && $user['date_of_birth']) {
    $birthDate = new DateTime($user['date_of_birth']);
    $today = new DateTime();
    $age = $today->diff($birthDate)->y;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - VITA</title>
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
                <li><a href="profile.php" class="nav-link active">
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
                <h1>Profile Settings</h1>
                <p>Manage your personal information and preferences</p>
            </div>

            <!-- Profile Overview -->
            <div class="card profile-overview">
                <div class="profile-header">
                    <div class="profile-avatar">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </div>
                    <div class="profile-info">
                        <h2><?php echo htmlspecialchars($user['full_name'] ?? 'User Name'); ?></h2>
                        <p>Age: <?php echo $age; ?> • Patient ID: <?php echo htmlspecialchars($user['patient_id'] ?? 'N/A'); ?></p>
                        <span class="profile-status active">Active Patient</span>
                    </div>
                    <button class="edit-profile-btn">Edit Profile</button>
                </div>
                
                <div class="profile-stats">
                    <div class="stat-item">
                        <span class="stat-label">Member Since</span>
                        <span class="stat-value"><?php echo $user ? date('M Y', strtotime($user['created_at'])) : 'N/A'; ?></span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Blood Type</span>
                        <span class="stat-value"><?php echo htmlspecialchars($user['blood_type'] ?? 'Not Set'); ?></span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Emergency Contacts</span>
                        <span class="stat-value"><?php echo count($db->getEmergencyContacts($user_id)); ?></span>
                    </div>
                </div>
            </div>

            <!-- Profile Information and Health Info -->
            <div class="dashboard-grid">
                <!-- Personal Information -->
                <div class="card">
                    <div class="card-header">
                        <h2>Personal Information</h2>
                        <button class="edit-btn">Edit</button>
                    </div>
                    
                    <div class="info-form">
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" value="<?php echo htmlspecialchars($user['full_name'] ?? ''); ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label>Date of Birth</label>
                            <input type="date" value="<?php echo $user['date_of_birth'] ?? ''; ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="tel" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label>Address</label>
                            <textarea readonly><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Health Information -->
                <div class="card">
                    <div class="card-header">
                        <h2>Health Information</h2>
                        <button class="edit-btn">Edit</button>
                    </div>
                    
                    <div class="health-info">
                        <div class="health-section">
                            <h3>Medical Conditions</h3>
                            <div class="condition-list">
                                <?php if (empty($medical_conditions)): ?>
                                    <span class="condition-tag">None reported</span>
                                <?php else: ?>
                                    <?php foreach ($medical_conditions as $condition): ?>
                                        <span class="condition-tag"><?php echo htmlspecialchars($condition['condition_name']); ?></span>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="health-section">
                            <h3>Allergies</h3>
                            <div class="allergy-list">
                                <?php if (empty($allergies)): ?>
                                    <span class="allergy-tag">None reported</span>
                                <?php else: ?>
                                    <?php foreach ($allergies as $allergy): ?>
                                        <span class="allergy-tag"><?php echo htmlspecialchars($allergy['allergen']); ?></span>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="health-section">
                            <h3>Emergency Information</h3>
                            <div class="emergency-info">
                                <p><strong>Blood Type:</strong> <?php echo htmlspecialchars($user['blood_type'] ?? 'Not specified'); ?></p>
                                <p><strong>Insurance:</strong> <?php echo htmlspecialchars($user['insurance_info'] ?? 'Not specified'); ?></p>
                                <?php if (!empty($healthcare_providers)): ?>
                                    <p><strong>Primary Doctor:</strong> 
                                        <?php 
                                        $primary = array_filter($healthcare_providers, function($p) { return $p['is_primary']; });
                                        echo !empty($primary) ? htmlspecialchars(array_values($primary)[0]['name']) : 'Not assigned';
                                        ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- App Preferences and Privacy Settings -->
            <div class="dashboard-grid">
                <!-- App Preferences -->
                <div class="card">
                    <div class="card-header">
                        <h2>App Preferences</h2>
                    </div>
                    
                    <div class="preferences-list">
                        <div class="preference-item">
                            <div class="preference-info">
                                <span>Dark Mode</span>
                                <p>Use dark theme for better visibility</p>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" <?php echo ($preferences && $preferences['dark_mode']) ? 'checked' : ''; ?>>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                        
                        <div class="preference-item">
                            <div class="preference-info">
                                <span>Large Text</span>
                                <p>Increase text size for better readability</p>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" <?php echo ($preferences && $preferences['large_text']) ? 'checked' : ''; ?>>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                        
                        <div class="preference-item">
                            <div class="preference-info">
                                <span>Voice Alerts</span>
                                <p>Enable spoken notifications</p>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" <?php echo ($preferences && $preferences['voice_alerts']) ? 'checked' : ''; ?>>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                        
                        <div class="preference-item">
                            <div class="preference-info">
                                <span>Vibration</span>
                                <p>Vibrate for important notifications</p>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" <?php echo ($preferences && $preferences['vibration']) ? 'checked' : ''; ?>>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Privacy Settings -->
                <div class="card">
                    <div class="card-header">
                        <h2>Privacy & Security</h2>
                    </div>
                    
                    <div class="privacy-settings">
                        <div class="privacy-item">
                            <div class="privacy-info">
                                <span>Location Tracking</span>
                                <p>Allow location monitoring for safety</p>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" <?php echo ($privacy_settings && $privacy_settings['location_tracking']) ? 'checked' : ''; ?>>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                        
                        <div class="privacy-item">
                            <div class="privacy-info">
                                <span>Data Sharing with Family</span>
                                <p>Share health data with family members</p>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" <?php echo ($privacy_settings && $privacy_settings['data_sharing_family']) ? 'checked' : ''; ?>>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                        
                        <div class="privacy-item">
                            <div class="privacy-info">
                                <span>Research Data Sharing</span>
                                <p>Contribute anonymized data to research</p>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" <?php echo ($privacy_settings && $privacy_settings['data_sharing_research']) ? 'checked' : ''; ?>>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                        
                        <div class="privacy-action">
                            <h3>Security Actions</h3>
                            <button class="security-btn">Change Password</button>
                            <button class="security-btn">Two-Factor Authentication</button>
                        </div>
                        
                        <div class="privacy-action">
                            <h3>Data Management</h3>
                            <button class="data-btn">Export My Data</button>
                            <button class="data-btn">Delete Account</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Family Access -->
            <div class="card">
                <div class="card-header">
                    <h2>Family Access</h2>
                    <button class="add-family-btn">+ Add Family Member</button>
                </div>
                
                <div class="family-list">
                    <?php if (empty($family_members)): ?>
                        <div class="family-member">
                            <div class="member-avatar">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                            </div>
                            <div class="member-info">
                                <h3>No Family Members</h3>
                                <p>Add family members to share health information</p>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($family_members as $member): ?>
                            <div class="family-member">
                                <div class="member-avatar">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="12" cy="7" r="4"></circle>
                                    </svg>
                                </div>
                                <div class="member-info">
                                    <h3><?php echo htmlspecialchars($member['family_member_name']); ?></h3>
                                    <p><?php echo htmlspecialchars($member['relationship']); ?> • <?php echo htmlspecialchars($member['access_level']); ?></p>
                                    <span class="member-email"><?php echo htmlspecialchars($member['family_member_email']); ?></span>
                                </div>
                                <div class="member-permissions">
                                    <?php if ($member['permissions']): ?>
                                        <?php $perms = json_decode($member['permissions'], true); ?>
                                        <?php foreach ($perms as $perm => $enabled): ?>
                                            <?php if ($enabled): ?>
                                                <span class="permission-tag"><?php echo ucfirst(str_replace('_', ' ', $perm)); ?></span>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                                <div class="member-actions">
                                    <button class="member-action-btn">Edit</button>
                                    <button class="member-action-btn">Remove</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <!-- Mobile Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <script src="assets/js/main.js"></script>
    <script src="assets/js/profile.js"></script>
</body>
</html>