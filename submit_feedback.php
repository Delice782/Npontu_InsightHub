<?php
// Include the database connection file
require_once 'db_connect.php';
include 'session_check.php';

// Ensure only admin can access
if ($_SESSION['role'] !== 'customer') {
    // Redirect to an unauthorized access page or dashboard
    header('Location: unauthorized.php');
    exit(); // Prevent further script execution
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];

    // Handle form submission
    $rating = $_POST['rating'];
    $comment = trim($_POST['comment']);
    $category = $_POST['category'];

    // Comment length validation
    $min_comment_length = 10;  // Minimum 10 characters
    $max_comment_length = 500; // Maximum 500 characters

    // Basic form validation with comment length check
    $error = null;
    if (!isset($rating) || empty($comment) || !isset($category)) {
        $error = "Please fill in all fields.";
    } elseif (strlen($comment) < $min_comment_length) {
        $error = "Feedback must be at least $min_comment_length characters long.";
    } elseif (strlen($comment) > $max_comment_length) {
        $error = "Feedback cannot exceed $max_comment_length characters.";
    } else {
        // Insert feedback into the database
        try {
            $sql = "INSERT INTO InsightHub_Feedback (user_id, rating, comment, category, created_at) 
                    VALUES (?, ?, ?, ?, NOW())";
            
            $stmt = $conn->prepare($sql);
            if ($stmt === false) {
                throw new Exception("Failed to prepare SQL statement: " . $conn->error);
            }

            $stmt->bind_param('isss', $user_id, $rating, $comment, $category);

            $stmt->execute();

            // Success message
            $success = "Thank you for your feedback!";

        } catch (Exception $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}

// Count new responses for notification badge
try {
    $new_responses_query = "SELECT COUNT(*) as new_responses 
                             FROM InsightHub_FeedbackResponses 
                             WHERE user_id = ? AND is_read = 0";
    $stmt = $conn->prepare($new_responses_query);
    $stmt->bind_param('i', $_SESSION['user_id']);
    $stmt->execute();
    $new_responses_result = $stmt->get_result()->fetch_assoc();
    $new_responses = $new_responses_result['new_responses'];
} catch (Exception $e) {
    $new_responses = 0;
    error_log("Error counting new responses: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback Submission</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="submit_feedback.css">

</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-logo">InsightHub</div>
            <ul class="sidebar-menu">
                <li><a href="index.php" class="nav-btn nav-btn-primary"><i class="ri-home-line"></i> Home</a></li>
                <li><a href="customer_dashboard.php"><i class="ri-dashboard-line"></i> Dashboard</a></li>
                <li><a href="submit_feedback.php" class="active"><i class="ri-feedback-line"></i> Submit Feedback</a></li>
                <li>
                    <a href="view_my_feedback.php">
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
            <div class="feedback-wrapper">
                <div class="feedback-container">
                    <div class="feedback-header">
                        <h2 class="feedback-title">Share Your Feedback</h2>
                    </div>
                    
                    <?php if (isset($success)): ?>
                        <div class="alert alert-success">
                            <i class="ri-check-line"></i> <?= htmlspecialchars($success) ?>
                        </div>
                    <?php elseif (isset($error)): ?>   
                        <div class="alert alert-error">
                            <i class="ri-error-warning-line"></i> <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <form action="submit_feedback.php" method="POST">
                        <div class="form-group rating-container">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <input type="radio" id="rating<?= $i ?>" name="rating" value="<?= $i ?>" required>
                                <label for="rating<?= $i ?>">★</label>
                            <?php endfor; ?>
                        </div>

                        <div class="form-group">
                            <label for="comment">Your Feedback:</label>
                            <textarea 
                                id="comment" 
                                name="comment" 
                                rows="4" 
                                class="form-control" 
                                required 
                                placeholder="Tell us about your experience..."
                                minlength="10"
                                maxlength="500"
                            ></textarea>
                            <small style="color: #7f8c8d; display: block; margin-top: 5px;">
                                Feedback must be between 10 and 500 characters.
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="category">Feedback Category:</label>
                            <select id="category" name="category" class="form-control" required>
                                <option value="Usability">Usability</option>
                                <option value="Performance">Performance</option>
                                <option value="Design">Design</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <button type="submit" class="submit-btn">
                            <i class="ri-send-plane-line"></i> Submit Feedback
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>