<?php
require_once 'db_connect.php';
include 'session_check.php';

// Ensure only admin can access
if ($_SESSION['role'] !== 'customer') {
    // Redirect to an unauthorized access page or dashboard
    header('Location: unauthorized.php');
    exit(); // Prevent further script execution
}

$user_id = $_SESSION['user_id'];
$name = $_SESSION['name'];
$error = '';

try {
    // Count new responses that the user hasn't viewed
    $new_responses_query = "SELECT COUNT(*) as new_responses 
                             FROM InsightHub_FeedbackResponses 
                             WHERE user_id = ? AND is_read = 0";
    $stmt = $conn->prepare($new_responses_query);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $new_responses_result = $stmt->get_result()->fetch_assoc();
    $new_responses = $new_responses_result['new_responses'];
} catch (Exception $e) {
    $new_responses = 0;
    // Log the error or handle it appropriately
    error_log("Error counting new responses: " . $e->getMessage());
}

try {
    // Fetch user's submitted feedback history
    $feedback_query = "SELECT * FROM InsightHub_Feedback 
                       WHERE user_id = ? 
                       ORDER BY created_at DESC 
                       LIMIT 5";
    $stmt = $conn->prepare($feedback_query);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $recent_feedback = $stmt->get_result();

    // Fetch user's total feedback count
    $total_feedback_query = "SELECT COUNT(*) as total_feedback 
                             FROM InsightHub_Feedback 
                             WHERE user_id = ?";
    $stmt = $conn->prepare($total_feedback_query);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $total_feedback_result = $stmt->get_result()->fetch_assoc();
    $total_feedback = $total_feedback_result['total_feedback'];

    // Fetch user's average rating
    $avg_rating_query = "SELECT AVG(rating) as avg_rating 
                         FROM InsightHub_Feedback 
                         WHERE user_id = ?";
    $stmt = $conn->prepare($avg_rating_query);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $avg_rating_result = $stmt->get_result()->fetch_assoc();
    $avg_rating = round($avg_rating_result['avg_rating'], 1);

    // Fetch feedback categories used by the user
    $category_query = "SELECT DISTINCT category, COUNT(*) as count 
                       FROM InsightHub_Feedback 
                       WHERE user_id = ? 
                       GROUP BY category";
    $stmt = $conn->prepare($category_query);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $categories = $stmt->get_result();

} catch (Exception $e) {
    $error = "Database Error: " . $e->getMessage();
}

// Add this right after the existing category query section
try {
    // Fetch recent feedback responses
    $responses_query = "SELECT fr.*, f.category 
                        FROM InsightHub_FeedbackResponses fr
                        JOIN InsightHub_Feedback f ON fr.feedback_id = f.feedback_id
                        WHERE fr.user_id = ? 
                        ORDER BY fr.created_at DESC 
                        LIMIT 5";
    $stmt = $conn->prepare($responses_query);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $recent_responses = $stmt->get_result();
} catch (Exception $e) {
    $error = "Database Error: " . $e->getMessage();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard - InsightHub</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="customer_dashboard.css">
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-logo">InsightHub</div>
            <ul class="sidebar-menu">
                <li><a href="index.php" class="nav-btn nav-btn-primary"><i class="ri-home-line"></i> Home</a></li>
                <li><a href="customer_dashboard.php" class="active"><i class="ri-dashboard-line"></i> Dashboard</a></li>
                <li><a href="submit_feedback.php"><i class="ri-feedback-line"></i> Submit Feedback</a></li>
                <li>
                    <a href="view_my_feedback.php">
                        <i class="ri-list-check"></i> My Feedback 
                        <?php if ($new_responses > 0): ?>
                            <span class="notification-badge pulse">
                                <?= $new_responses ?>
                            </span>
                        <?php endif; ?>
                    </a>
                </li>
                <li>
                    <a href="view_response.php">
                        <i class="ri-message-2-line"></i> Feedback Responses
                        <?php if ($new_responses > 0): ?>
                            <span class="notification-badge pulse">
                                <?= $new_responses ?>
                            </span>
                        <?php endif; ?>
                    </a>
                </li>
                <li><a href="profile.php"><i class="ri-user-line"></i> Profile</a></li>
                <li><a href="npontu_logout.php"><i class="ri-logout-box-r-line"></i> Logout</a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="dashboard-header">
                <h1>Customer Dashboard</h1>
                <div>Welcome, <?= htmlspecialchars($name ?? 'Customer') ?></div>
            </div>

            <!-- Metrics Grid -->
            <div class="metrics-grid">
                <div class="metric-card">
                    <i class="ri-message-3-line metric-icon"></i>
                    <div class="metric-content">
                        <div class="metric-title">Total Feedback Submitted</div>
                        <div class="metric-value"><?= number_format($total_feedback) ?></div>
                    </div>
                </div>

                <div class="metric-card">
                    <i class="ri-star-line metric-icon"></i>
                    <div class="metric-content">
                        <div class="metric-title">Average Rating</div>
                        <div class="metric-value"><?= $avg_rating ?> / 5</div>
                    </div>
                </div>

                <div class="metric-card">
                    <i class="ri-pie-chart-line metric-icon"></i>
                    <div class="metric-content">
                        <div class="metric-title">Feedback Categories</div>
                        <div class="category-list">
                            <?php 
                            if ($categories->num_rows > 0) {
                                while ($category = $categories->fetch_assoc()) {
                                    echo '<span class="category-badge">' . 
                                         htmlspecialchars($category['category']) . 
                                         ' (' . $category['count'] . ')' . 
                                         '</span>';
                                }
                            } else {
                                echo '<span>No categories yet</span>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Feedback Section -->
            <!-- Recent Feedback Section -->
            <div class="recent-feedback">
                <h2>Your Recent Feedback</h2>
                <?php if ($recent_feedback->num_rows > 0): ?>
                    <?php while($feedback = $recent_feedback->fetch_assoc()): ?>
                        <div class="feedback-item">
                            <div class="feedback-header">
                                <div class="feedback-metadata">
                                    <strong class="feedback-category"><?= htmlspecialchars($feedback['category']) ?></strong>
                                    <span class="feedback-rating">Rated <?= number_format($feedback['rating']) ?> ⭐</span>
                                </div>
                                <div class="feedback-date"><?= date('M j, Y', strtotime($feedback['created_at'])) ?></div>
                            </div>
                            <div class="feedback-content">
                                <?= htmlspecialchars(
                                    strlen($feedback['comment']) > 150 ? 
                                    substr($feedback['comment'], 0, 150) . '...' : 
                                    $feedback['comment']
                                ) ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>You haven't submitted any feedback yet.</p>
                    <a href="submit_feedback.php" class="view-all-btn">Submit Your First Feedback</a>
                <?php endif; ?>
                <a href="view_my_feedback.php" class="view-all-btn">View All My Feedback</a>

                <?php if ($new_responses > 0): ?>
                    <div class="notification-banner">
                        <div>
                            <strong>New Admin Responses</strong>
                            <p>You have <?= $new_responses ?> unread response<?= $new_responses > 1 ? 's' : '' ?> to your feedback.</p>
                        </div>
                        <div>
                            <a href="view_feedback_responses.php" class="clear-notifications-btn">
                                <i class="ri-eye-line"></i> View Responses
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Add this right after the closing </div> of the recent-feedback section -->
            

            <!-- Error Display -->
            <?php if (!empty($error)): ?>
                <div class="error-message" style="color: red; margin-top: 20px;">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>