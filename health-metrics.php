<?php
/**
 * VITA Health Platform - Health Metrics
 */

require_once 'includes/db_functions.php';

// Get current user and data
$user_id = getCurrentUserId();
$user = getCurrentUser();
$db = VitaDB::getInstance();

// Get period from URL parameter
$period = $_GET['period'] ?? 'week';
$valid_periods = ['day', 'week', 'month', 'year'];
if (!in_array($period, $valid_periods)) {
    $period = 'week';
}

// Get health metrics data
$metrics = $db->getHealthMetricsByPeriod($user_id, $period, 100);
$latest_metrics = $db->getLatestHealthMetrics($user_id);

// Calculate averages
$averages = [
    'heart_rate' => 0,
    'blood_oxygen' => 0,
    'blood_glucose' => 0
];

if (!empty($metrics)) {
    $totals = ['heart_rate' => 0, 'blood_oxygen' => 0, 'blood_glucose' => 0];
    $counts = ['heart_rate' => 0, 'blood_oxygen' => 0, 'blood_glucose' => 0];
    
    foreach ($metrics as $metric) {
        if ($metric['heart_rate']) {
            $totals['heart_rate'] += $metric['heart_rate'];
            $counts['heart_rate']++;
        }
        if ($metric['blood_oxygen']) {
            $totals['blood_oxygen'] += $metric['blood_oxygen'];
            $counts['blood_oxygen']++;
        }
        if ($metric['blood_glucose']) {
            $totals['blood_glucose'] += $metric['blood_glucose'];
            $counts['blood_glucose']++;
        }
    }
    
    foreach ($averages as $key => &$avg) {
        if ($counts[$key] > 0) {
            $avg = round($totals[$key] / $counts[$key]);
        }
    }
}

// Current values
$current_heart_rate = $latest_metrics['heart_rate'] ?? 72;
$current_oxygen = $latest_metrics['blood_oxygen'] ?? 98;
$current_glucose = $latest_metrics['blood_glucose'] ?? 95;

// Recent readings for display
$recent_readings = array_slice($metrics, 0, 10);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Health Metrics - VITA</title>
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
                <li><a href="health-metrics.php" class="nav-link active">
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
                <h1>Health Metrics</h1>
                <p>Comprehensive health monitoring and trends</p>
            </div>

            <!-- Time Period Selector -->
            <div class="period-selector">
                <a href="?period=day" class="period-btn <?php echo $period === 'day' ? 'active' : ''; ?>">Day</a>
                <a href="?period=week" class="period-btn <?php echo $period === 'week' ? 'active' : ''; ?>">Week</a>
                <a href="?period=month" class="period-btn <?php echo $period === 'month' ? 'active' : ''; ?>">Month</a>
                <a href="?period=year" class="period-btn <?php echo $period === 'year' ? 'active' : ''; ?>">Year</a>
            </div>

            <!-- Metrics Overview -->
            <div class="metrics-grid">
                <div class="metric-card">
                    <div class="metric-content">
                        <div class="metric-info">
                            <p class="metric-label">Heart Rate</p>
                            <p class="metric-value"><?php echo $current_heart_rate; ?> <span class="metric-unit">bpm</span></p>
                            <p class="metric-status normal">● Stable</p>
                            <p class="metric-average">Avg: <?php echo $averages['heart_rate'] ?: 75; ?> bpm</p>
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
                            <p class="metric-value"><?php echo $current_oxygen; ?> <span class="metric-unit">%</span></p>
                            <p class="metric-status improving">● Improving</p>
                            <p class="metric-average">Avg: <?php echo $averages['blood_oxygen'] ?: 97; ?>%</p>
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
                            <p class="metric-label">Blood Glucose</p>
                            <p class="metric-value"><?php echo $current_glucose; ?> <span class="metric-unit">mg/dL</span></p>
                            <p class="metric-status normal">● Stable <span class="beta-tag">BETA</span></p>
                            <p class="metric-average">Avg: <?php echo $averages['blood_glucose'] ?: 98; ?> mg/dL</p>
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

            <!-- Charts and Detailed View -->
            <div class="dashboard-grid">
                <!-- Heart Rate Chart -->
                <div class="card">
                    <div class="card-header">
                        <h2>Heart Rate Trends</h2>
                        <svg class="header-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                        </svg>
                    </div>
                    <div class="chart-placeholder">
                        <div class="chart-content">
                            <svg class="chart-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="22,12 18,12 15,21 9,3 6,12 2,12"></polyline>
                            </svg>
                            <p>Interactive heart rate chart</p>
                            <p class="chart-subtitle">Real-time data visualization</p>
                        </div>
                    </div>
                    <div class="chart-stats">
                        <span>Resting HR: 65 bpm</span>
                        <span>Max HR: 142 bpm</span>
                    </div>
                </div>

                <!-- Recent Readings -->
                <div class="card">
                    <div class="card-header">
                        <h2>Recent Readings</h2>
                        <svg class="header-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                    </div>
                    <div class="readings-list">
                        <?php if (empty($recent_readings)): ?>
                            <div class="reading-item">
                                <div class="reading-header">
                                    <span class="reading-time">No Data</span>
                                    <span class="reading-date">Available</span>
                                </div>
                                <div class="reading-metrics">
                                    <div class="reading-metric">
                                        <span class="metric-name">No recent readings available</span>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <?php foreach ($recent_readings as $reading): ?>
                                <div class="reading-item">
                                    <div class="reading-header">
                                        <span class="reading-time"><?php echo $db->formatTime($reading['recorded_at']); ?></span>
                                        <span class="reading-date"><?php echo $db->formatDate($reading['recorded_at']); ?></span>
                                    </div>
                                    <div class="reading-metrics">
                                        <?php if ($reading['heart_rate']): ?>
                                            <div class="reading-metric">
                                                <span class="metric-name">HR:</span>
                                                <span class="metric-val"><?php echo $reading['heart_rate']; ?> bpm</span>
                                            </div>
                                        <?php endif; ?>
                                        <?php if ($reading['blood_oxygen']): ?>
                                            <div class="reading-metric">
                                                <span class="metric-name">SpO2:</span>
                                                <span class="metric-val"><?php echo $reading['blood_oxygen']; ?>%</span>
                                            </div>
                                        <?php endif; ?>
                                        <?php if ($reading['blood_glucose']): ?>
                                            <div class="reading-metric">
                                                <span class="metric-name">Glucose:</span>
                                                <span class="metric-val"><?php echo $reading['blood_glucose']; ?> mg/dL</span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Health Insights -->
            <div class="card">
                <div class="card-header">
                    <h2>AI Health Insights</h2>
                </div>
                <div class="insights-grid">
                    <div class="insight-card positive">
                        <h3>Positive Trends</h3>
                        <ul>
                            <li>• Heart rate variability is within optimal range</li>
                            <li>• Blood oxygen levels consistently excellent</li>
                            <li>• Glucose levels well-controlled</li>
                        </ul>
                    </div>
                    <div class="insight-card recommendations">
                        <h3>Recommendations</h3>
                        <ul>
                            <li>• Continue current medication schedule</li>
                            <li>• Maintain regular walking routine</li>
                            <li>• Consider increasing water intake</li>
                            <li>• Monitor glucose after meals</li>
                        </ul>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Mobile Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <script src="assets/js/main.js"></script>
    <script src="assets/js/health-metrics.js"></script>
</body>
</html>