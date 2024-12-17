<?php
require_once 'db_connect.php';

// Fetch total users count
$users_query = "SELECT COUNT(*) as total_users FROM InsightHub_Users";
$users_result = $conn->query($users_query);
$total_users = $users_result->fetch_assoc()['total_users'];

// Fetch total feedback count
$feedback_query = "SELECT COUNT(*) as total_feedback FROM InsightHub_Feedback";
$feedback_result = $conn->query($feedback_query);
$total_feedback = $feedback_result->fetch_assoc()['total_feedback'];

// Fetch pending feedback count
$pending_feedback_query = "SELECT COUNT(*) as total_pending FROM InsightHub_Feedback WHERE feedback_status = 'pending'";
$pending_feedback_result = $conn->query($pending_feedback_query);
$total_pending = $pending_feedback_result->fetch_assoc()['total_pending'];

// Fetch feedback categories with counts
$categories_query = "SELECT category, COUNT(*) as category_count 
                     FROM InsightHub_Feedback 
                     GROUP BY category 
                     ORDER BY category_count DESC";
$categories_result = $conn->query($categories_query);
$categories = [];
while ($row = $categories_result->fetch_assoc()) {
    $categories[] = $row;
}

// Fetch recent feedback
$recent_feedback_query = "SELECT f.feedback_id, f.rating, f.comment, f.category, f.created_at, u.name 
                          FROM InsightHub_Feedback f
                          JOIN InsightHub_Users u ON f.user_id = u.user_id
                          ORDER BY f.created_at DESC
                          LIMIT 5";
$recent_feedback_result = $conn->query($recent_feedback_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Feedback Hub</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Reuse the styles from display_feedback.php */
        :root {
            --primary-color: #3498db;
            --secondary-color: #2ecc71;
            --background-light: #f4f6f7;
            --text-dark: #2c3e50;
            --card-background: white;
            --card-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
            color: var(--text-dark);
            background-color: var(--background-light);
            display: flex;
        }
        .sidebar {
            width: 250px;
            background-color: var(--primary-color);
            color: white;
            padding: 20px;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
        }
        .main-content {
            margin-left: 250px;
            flex-grow: 1;
            padding: 30px;
            width: calc(100% - 250px);
            background-color: var(--background-light);
        }
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); 
            gap: 20px;
            width: 100%;
        }
        .dashboard-card {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            background-color: white;
            border-radius: 8px;
            box-shadow: var(--card-shadow);
            padding: 20px;
            min-height: 200px;
        }
        .card-header {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
        }
        .card-title-container {
            display: flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 0;
        }
        .card-value {
            font-size: 3rem;
            font-weight: bold;
            color: var(--primary-color);
            margin-top: 10px;
        }
        .card-icon {
            font-size: 1.2rem;
            color: var(--primary-color);
            margin-right: 3px;
        }
        .recent-feedback {
            margin-top: 20px;
        }
        .recent-feedback-item {
            background-color: white;
            border-radius: 8px;
            box-shadow: var(--card-shadow);
            padding: 15px;
            margin-bottom: 10px;
        }
        .recent-feedback-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .category-chart-container {
            max-height: 350px;
            height: 350px;
            display: flex;
            flex-direction: column;
            background-color: white;
            border-radius: 8px;
            align-items: center;
            justify-content: flex-start;
            box-shadow: var(--card-shadow);
            padding: 10px 20px;
        }
        .category-chart-container .card-header {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 5px;
            width: 100%;
            text-align: center;
        }
        #categoryChart {
            flex-grow: 1;
            width: 100% !important;
            height: 300% !important;
        }
        .sidebar-logo {
            text-align: center;
            margin-bottom: 30px;
            font-size: 1.5rem;
            font-weight: 700;
            color: white;
        }
        .sidebar-menu {
            list-style: none;
        }
        .sidebar-menu li {
            margin-bottom: 15px;
        }
        .sidebar-menu a {
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            padding: 10px;
            border-radius: 8px;
            transition: background-color 0.3s;
        }
        .sidebar-menu a:hover, .sidebar-menu a.active {
            background-color: rgba(255,255,255,0.2);
        }
        .sidebar-menu i {
            margin-right: 10px;
            font-size: 1.2rem;
        }
        .view-all-feedback {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }
        .view-all-btn {
            display: inline-flex;
            align-items: center;
            padding: 12px 24px;
            background-color: var(--primary-color);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }
        .view-all-btn:hover {
            background-color: #2980b9;
        }
        .view-all-btn i {
            margin-left: 10px;
        }

    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-logo">
            Feedback Hub
        </div>
        <ul class="sidebar-menu">
            <li><a href="index.php">
                <i class="ri-home-line"></i> Home
            </a></li>
            <li><a href="dashboard_admin.php" class="active">
                <i class="ri-dashboard-line"></i> Dashboard
            </a></li>
            <li><a href="display_feedback.php">
                <i class="ri-list-check"></i> All Feedback
            </a></li>
            <li><a href="manage_users.php">
                <i class="ri-user-settings-line"></i> Manage Users
            </a></li>
            <li><a href="profile.php">
                <i class="ri-user-line"></i> Profile
            </a></li>
            <li><a href="npontu_logout.php">
                <i class="ri-logout-box-r-line"></i> Logout
            </a></li>
        </ul>
    </div>

    <div class="main-content">
        <h1>Admin Dashboard</h1>

        <div class="dashboard-grid">
            <div class="dashboard-card">
                <div class="card-header">
                    <div class="card-title-container">
                        <i class="ri-user-line card-icon"></i>
        
                        <span>Total Users</span>
                    </div>
                    <div class="card-value"><?= $total_users ?></div>
                </div>
            </div>

            <div class="dashboard-card">
                <div class="card-header">
                    <div class="card-title-container">
                        <i class="ri-feedback-line card-icon"></i>
                        <span>Total Feedback</span>
                    </div>
                    <div class="card-value"><?= $total_feedback ?></div>
                </div>
            </div>

            <div class="dashboard-card">
                <div class="card-header">
                    <div class="card-title-container">
                        <i class="ri-time-line card-icon"></i>
                        <span>Pending Feedback</span>
                    </div>
                    <div class="card-value"><?= $total_pending ?></div>
                </div>
            </div>

            <div class="dashboard-card category-chart-container">
                <div class="card-header">
                    <div class="card-title-container">
                        <i class="ri-pie-chart-line card-icon"></i>
                        <span>Feedback Categories</span>
                    </div>
                </div>
                <canvas id="categoryChart"></canvas>
            </div>
        </div>

        <div class="recent-feedback">
            <h2>Recent Feedback</h2>
            <?php while ($row = $recent_feedback_result->fetch_assoc()): ?>
                <div class="recent-feedback-item">
                    <div class="recent-feedback-header">
                        <strong><?= htmlspecialchars($row['name']) ?></strong>
                        <span class="category-tag">Category: <?= htmlspecialchars($row['category']) ?></span>
                    </div>
                    <div><?= htmlspecialchars(substr($row['comment'], 0, 100)) ?>...</div>
                </div>
            <?php endwhile; ?>
        </div>

        <div class="view-all-feedback">
            <a href="display_feedback.php" class="view-all-btn">
                View All Feedback <i class="ri-arrow-right-line"></i>
            </a>
        </div>
        
    </div>

    <script>
        // Debug: Print out category data
        console.log('Category Labels:', <?= json_encode(array_column($categories, 'category')) ?>);
        console.log('Category Counts:', <?= json_encode(array_column($categories, 'category_count')) ?>);

        // Prepare data for chart
        const categoryLabels = <?= json_encode(array_column($categories, 'category')) ?>;
        const categoryCounts = <?= json_encode(array_column($categories, 'category_count')) ?>;

        // Ensure we have data before creating chart
        if (categoryLabels.length > 0 && categoryCounts.length > 0) {
            const ctx = document.getElementById('categoryChart').getContext('2d');
            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: categoryLabels,
                    datasets: [{
                        data: categoryCounts,
                        backgroundColor: [
                            '#3498db', '#2ecc71', '#e74c3c', 
                            '#f39c12', '#9b59b6', '#1abc9c'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                boxWidth: 20,
                                padding: 10
                            }
                        }
                    }
                }
            });
        } else {
            console.error('No category data available for chart');
            document.getElementById('categoryChart').innerHTML = 'No data available for chart';
        }
    </script>
</body>
</html>
<?php $conn->close(); ?>