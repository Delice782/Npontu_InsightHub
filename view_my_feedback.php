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

// Pagination setup
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$items_per_page = 5;
$offset = ($page - 1) * $items_per_page;
$max_comment_length = 200; // Limit for display

try {
    // Count total number of feedback items
    $count_query = "SELECT COUNT(*) as total FROM InsightHub_Feedback WHERE user_id = ?";
    $count_stmt = $conn->prepare($count_query);
    $count_stmt->bind_param('i', $user_id);
    $count_stmt->execute();
    $count_result = $count_stmt->get_result()->fetch_assoc();
    $total_items = $count_result['total'];
    $total_pages = ceil($total_items / $items_per_page);

    // Fetch paginated feedback
    $feedback_query = "SELECT * FROM InsightHub_Feedback 
                       WHERE user_id = ? 
                       ORDER BY created_at DESC
                       LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($feedback_query);
    $stmt->bind_param('iii', $user_id, $items_per_page, $offset);
    $stmt->execute();
    $feedback_result = $stmt->get_result();
} catch (Exception $e) {
    $error = "Database Error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View My Feedback - InsightHub</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="view_my_feedback.css">

</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-logo">InsightHub</div>
            <ul class="sidebar-menu">
                <li><a href="index.php" class="nav-btn nav-btn-primary"><i class="ri-home-line"></i> Home</a></li>
                <li><a href="customer_dashboard.php"><i class="ri-dashboard-line"></i> Dashboard</a></li>
                <li><a href="submit_feedback.php" ><i class="ri-feedback-line"></i> Submit Feedback</a></li>
                <li>
                    <a href="view_my_feedback.php" class="active">
                        <i class="ri-list-check"></i> My Feedback 
                        <?php if ($new_responses > 0): ?>
                            <span class="notification-badge">
                                <?= $new_responses ?>
                            </span>
                        <?php endif; ?>
                    </a>
                </li>
                <li>
                    <a href="view_response.php">
                        <i class="ri-message-2-line"></i> Feedback Responses
                        <?php if ($new_responses > 0): ?>
                            <span class="notification-badge">
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
                <h1>My Feedback</h1>
                <div>Welcome, <?= htmlspecialchars($name ?? 'Customer') ?></div>
            </div>

            <!-- Feedback List -->
            <div class="feedback-list">
                <?php if ($feedback_result->num_rows > 0): ?>
                    <?php while($feedback = $feedback_result->fetch_assoc()): ?>
                        <div class="feedback-item">
                            <div class="feedback-header">
                                <span class="feedback-category">
                                    <?= htmlspecialchars($feedback['category']) ?>
                                </span>
                                <span class="feedback-rating">
                                    <?= str_repeat('⭐', $feedback['rating']) ?>
                                </span>
                            </div>
                            <div class="feedback-comment">
                                <?php 
                                // Truncate long comments
                                $comment = $feedback['comment'];
                                if (strlen($comment) > $max_comment_length) {
                                    $comment = substr($comment, 0, $max_comment_length) . '...';
                                }
                                echo htmlspecialchars($comment);
                                ?>
                            </div>
                            <div class="feedback-date" style="color: #7f8c8d; font-size: 0.8em; margin-top: 5px;">
                                <?= date('F j, Y, g:i a', strtotime($feedback['created_at'])) ?>
                            </div>
                        </div>
                    <?php endwhile; ?>

                    <!-- Pagination -->
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?= $page - 1 ?>" class="pagination-prev">
                                <i class="ri-arrow-left-line"></i> Previous
                            </a>
                        <?php endif; ?>

                        <?php if ($page < $total_pages): ?>
                            <a href="?page=<?= $page + 1 ?>" class="pagination-next">
                                Next <i class="ri-arrow-right-line"></i>
                            </a>
                        <?php endif; ?>
                    </div>

                <?php else: ?>
                    <p>You haven't submitted any feedback yet.</p>
                    <a href="submit_feedback.php" class="view-all-btn">Submit Your First Feedback</a>
                <?php endif; ?>
            </div>

            <!-- Error Display -->
            <?php if (!empty($error)): ?>
                <div class="error-message">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>