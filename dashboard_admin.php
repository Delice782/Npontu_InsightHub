<?php
include 'session_check.php';
require_once 'db_connect.php';

// Ensure only admin can access
if ($_SESSION['role'] !== 'admin') {
    // Redirect to an unauthorized access page or dashboard
    header('Location: unauthorized.php');
    exit(); // Prevent further script execution
}

// Check if user is logged in and has a name
$admin_name = isset($_SESSION['name']) ? $_SESSION['name'] : 'admin';


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
$recent_feedback_query = "SELECT f.feedback_id, f.rating, f.comment, f.category, f.created_at, u.name, u.email 
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
    <title>Admin Dashboard - InsightHub</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="dashboard_admin.css">

</head>
<body>
    <div class="sidebar">
        <div class="sidebar-logo">
            InsightHub
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
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h1>Admin Dashboard</h1>
            <div style="font-size: 1.2rem; color: var(--primary-color);">
                Welcome, <?= htmlspecialchars($admin_name) ?>!
            </div>
        </div>

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
                        <div class="feedback-user">
                            <i class="ri-user-line"></i>
                            <strong><?= htmlspecialchars($row['name']) ?></strong>
                            <span> (<?= htmlspecialchars($row['email']) ?>)</span>
                        </div>
                        <div class="feedback-rating">
                            <?php 
                            $rating = intval($row['rating']);
                            for ($i = 1; $i <= 5; $i++) {
                                echo $i <= $rating 
                                    ? '<i class="ri-star-fill" style="color: #ffd700;"></i>' 
                                    : '<i class="ri-star-line" style="color: #cccccc;"></i>';
                            }
                            ?>
                            <span style="margin-left: 10px; color: var(--text-dark); opacity: 0.7;"><?= $rating ?>/5</span>
                        </div>
                        <span class="category-tag">Category: <?= htmlspecialchars($row['category']) ?></span>
                    </div>
                    <div class="feedback-comment"><?= htmlspecialchars(substr($row['comment'], 0, 100)) ?>...</div>
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