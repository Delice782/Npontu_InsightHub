<?php
require_once 'db_connect.php';
include 'session_check.php';

// Ensure only customer can access
if ($_SESSION['role'] !== 'customer') {
    // Redirect to an unauthorized access page or dashboard
    header('Location: unauthorized.php');
    exit(); // Prevent further script execution
}

$user_id = $_SESSION['user_id'];
$user_name = isset($_SESSION['name']) ? $_SESSION['name'] : 'Customer';

// Fetch user's feedback with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$items_per_page = 5;
$offset = ($page - 1) * $items_per_page;

$sql = "SELECT 
            feedback_id, 
            rating, 
            comment, 
            category, 
            created_at, 
            feedback_status,
            response,
            response_date
        FROM InsightHub_Feedback 
        WHERE user_id = $user_id
        ORDER BY created_at DESC
        LIMIT $items_per_page OFFSET $offset";

$result = $conn->query($sql);

// Count total feedbacks for pagination
$count_sql = "SELECT COUNT(*) as total FROM InsightHub_Feedback WHERE user_id = $user_id";
$count_result = $conn->query($count_sql);
$total_feedbacks = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_feedbacks / $items_per_page);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback Responses</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="view_response.css">

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

            <li><a href="customer_dashboard.php" ><i class="ri-dashboard-line"></i> Dashboard</a></li>

            <li><a href="submit_feedback.php">
                <i class="ri-add-circle-line"></i> Submit Feedback
            </a></li>

            <li><a href="view_my_feedback.php">
                <i class="ri-feedback-line"></i> My Feedback
            </a></li>

            <li>
                <a href="view_response.php">
                    <i class="ri-message-2-line" class="active"></i> Feedback Responses
                    <?php if ($new_responses > 0): ?>
                        <span class="notification-badge">
                            <?= $new_responses ?>
                        </span>
                    <?php endif; ?>
                </a>
            </li>
            
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
            <h1>Feedback Responses</h1>
        </div>

        <?php if ($result->num_rows > 0): ?>
            <?php while ($feedback = $result->fetch_assoc()): ?>
                <div class="feedback-item">
                    <div class="feedback-status 
                        status-<?= strtolower($feedback['feedback_status']) ?>">
                        <?= htmlspecialchars($feedback['feedback_status']) ?>
                    </div>

                    <p><strong>Category:</strong> <?= htmlspecialchars($feedback['category']) ?></p>
                    <p><strong>Rating:</strong> <?= htmlspecialchars($feedback['rating']) ?></p>
                    <p><strong>Your Comment:</strong> <?= htmlspecialchars($feedback['comment']) ?></p>
                    <p><strong>Submitted on:</strong> <?= htmlspecialchars($feedback['created_at']) ?></p>

                    <?php if ($feedback['feedback_status'] != 'pending'): ?>
                        <div class="admin-response">
                            <h3>Admin Response:</h3>
                            <?php if ($feedback['response']): ?>
                                <p><?= htmlspecialchars($feedback['response']) ?></p>
                                <small>Responded on: <?= htmlspecialchars($feedback['response_date']) ?></small>
                            <?php else: ?>
                                <p>No response yet.</p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>

            <!-- Pagination -->
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?= $page - 1 ?>" class="pagination-btn">
                        <i class="ri-arrow-left-line"></i> Previous
                    </a>
                <?php endif; ?>

                <?php if ($page < $total_pages): ?>
                    <a href="?page=<?= $page + 1 ?>" class="pagination-btn">
                        Next <i class="ri-arrow-right-line"></i>
                    </a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="no-feedback">
                <p>You have not submitted any feedback yet.</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>