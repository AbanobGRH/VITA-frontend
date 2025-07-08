<?php
/**
 * VITA Health Platform - Medication Manager
 */

require_once 'includes/db_functions.php';

// Get current user and data
$user_id = getCurrentUserId();
$user = getCurrentUser();
$db = VitaDB::getInstance();

// Get user's medications and today's schedule
$medications = $db->getUserMedications($user_id);
$today_schedule = $db->getTodayMedicationSchedule($user_id);

// Calculate adherence statistics
$total_scheduled = count($today_schedule);
$taken_count = 0;
$missed_count = 0;

foreach ($today_schedule as $schedule) {
    if ($schedule['status'] === 'Taken') {
        $taken_count++;
    } elseif ($schedule['status'] === 'Missed') {
        $missed_count++;
    }
}

$adherence_percentage = $total_scheduled > 0 ? round(($taken_count / $total_scheduled) * 100) : 100;

// Get refill reminders (medications with low stock)
$refill_reminders = array_filter($medications, function($med) {
    return $med['current_stock'] <= 7; // 7 days or less remaining
});
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medication Manager - VITA</title>
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
                <li><a href="medication.php" class="nav-link active">
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
                <h1>Medication Manager</h1>
                <p>Track medications, schedules, and adherence</p>
            </div>

            <!-- Today's Schedule -->
            <div class="card">
                <div class="card-header">
                    <h2>Today's Schedule</h2>
                    <button class="add-medication-btn">+ Add Medication</button>
                </div>
                
                <div class="schedule-timeline">
                    <?php if (empty($today_schedule)): ?>
                        <div class="schedule-item">
                            <div class="schedule-time">No Schedule</div>
                            <div class="schedule-content">
                                <div class="medication-info">
                                    <h3>No medications scheduled for today</h3>
                                    <p>Add medications to see your daily schedule</p>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($today_schedule as $schedule): ?>
                            <div class="schedule-item <?php echo $schedule['status'] === 'Taken' ? 'completed' : 'upcoming'; ?>">
                                <div class="schedule-time"><?php echo date('g:i A', strtotime($schedule['scheduled_time'])); ?></div>
                                <div class="schedule-content">
                                    <div class="medication-info">
                                        <h3><?php echo htmlspecialchars($schedule['name']); ?></h3>
                                        <p><?php echo htmlspecialchars($schedule['dosage']); ?> • <?php echo htmlspecialchars($schedule['condition_for']); ?></p>
                                    </div>
                                    <div class="schedule-status <?php echo $schedule['status'] === 'Taken' ? 'taken' : 'pending'; ?>">
                                        <?php if ($schedule['status'] === 'Taken'): ?>
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <polyline points="20,6 9,17 4,12"></polyline>
                                            </svg>
                                            <span>Taken</span>
                                        <?php else: ?>
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <circle cx="12" cy="12" r="10"></circle>
                                                <polyline points="12,6 12,12 16,14"></polyline>
                                            </svg>
                                            <span>Due</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Medication List and Adherence -->
            <div class="dashboard-grid">
                <!-- Active Medications -->
                <div class="card">
                    <div class="card-header">
                        <h2>Active Medications</h2>
                        <svg class="header-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4.5 16.5c-1.5 1.5-1.5 3.5 0 5s3.5 1.5 5 0l12-12c1.5-1.5 1.5-3.5 0-5s-3.5-1.5-5 0l-12 12z"></path>
                            <path d="M15 7l3 3"></path>
                        </svg>
                    </div>
                    
                    <div class="medications-list">
                        <?php if (empty($medications)): ?>
                            <div class="medication-item">
                                <div class="medication-icon glucose">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M4.5 16.5c-1.5 1.5-1.5 3.5 0 5s3.5 1.5 5 0l12-12c1.5-1.5 1.5-3.5 0-5s-3.5-1.5-5 0l-12 12z"></path>
                                    </svg>
                                </div>
                                <div class="medication-details">
                                    <h3>No Medications</h3>
                                    <p>Add medications to track your schedule</p>
                                </div>
                            </div>
                        <?php else: ?>
                            <?php foreach ($medications as $medication): ?>
                                <div class="medication-item">
                                    <div class="medication-icon <?php echo strpos(strtolower($medication['condition_for']), 'diabetes') !== false ? 'glucose' : 'cholesterol'; ?>">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M4.5 16.5c-1.5 1.5-1.5 3.5 0 5s3.5 1.5 5 0l12-12c1.5-1.5 1.5-3.5 0-5s-3.5-1.5-5 0l-12 12z"></path>
                                        </svg>
                                    </div>
                                    <div class="medication-details">
                                        <h3><?php echo htmlspecialchars($medication['name']); ?></h3>
                                        <p><?php echo htmlspecialchars($medication['dosage']); ?> • <?php echo htmlspecialchars($medication['frequency']); ?></p>
                                        <span class="medication-condition"><?php echo htmlspecialchars($medication['condition_for']); ?></span>
                                    </div>
                                    <div class="medication-actions">
                                        <button class="med-action-btn edit">Edit</button>
                                        <button class="med-action-btn refill">Refill</button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Adherence Stats -->
                <div class="card">
                    <div class="card-header">
                        <h2>Adherence Statistics</h2>
                        <svg class="header-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="18" y1="20" x2="18" y2="10"></line>
                            <line x1="12" y1="20" x2="12" y2="4"></line>
                            <line x1="6" y1="20" x2="6" y2="14"></line>
                        </svg>
                    </div>
                    
                    <div class="adherence-overview">
                        <div class="adherence-score">
                            <div class="score-circle">
                                <span class="score-value"><?php echo $adherence_percentage; ?>%</span>
                            </div>
                            <p>Overall Adherence</p>
                        </div>
                        
                        <div class="adherence-stats">
                            <div class="stat-row">
                                <span>Today</span>
                                <span class="stat-value <?php echo $adherence_percentage >= 80 ? 'good' : ''; ?>"><?php echo $adherence_percentage; ?>%</span>
                            </div>
                            <div class="stat-row">
                                <span>This Week</span>
                                <span class="stat-value good">96%</span>
                            </div>
                            <div class="stat-row">
                                <span>Missed Doses</span>
                                <span class="stat-value"><?php echo $missed_count; ?></span>
                            </div>
                            <div class="stat-row">
                                <span>On Time</span>
                                <span class="stat-value good">89%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Refill Reminders -->
            <?php if (!empty($refill_reminders)): ?>
            <div class="card">
                <div class="card-header">
                    <h2>Refill Reminders</h2>
                    <svg class="header-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                        <line x1="12" y1="9" x2="12" y2="13"></line>
                        <line x1="12" y1="17" x2="12.01" y2="17"></line>
                    </svg>
                </div>
                
                <div class="refill-list">
                    <?php foreach ($refill_reminders as $medication): ?>
                        <div class="refill-item <?php echo $medication['current_stock'] <= 3 ? 'urgent' : 'warning'; ?>">
                            <div class="refill-indicator"></div>
                            <div class="refill-info">
                                <h3><?php echo htmlspecialchars($medication['name']); ?></h3>
                                <p><?php echo $medication['current_stock']; ?> days remaining</p>
                            </div>
                            <button class="refill-btn <?php echo $medication['current_stock'] <= 3 ? 'urgent' : ''; ?>">Order Refill</button>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </main>
    </div>

    <!-- Mobile Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <script src="assets/js/main.js"></script>
    <script src="assets/js/medication.js"></script>
</body>
</html>