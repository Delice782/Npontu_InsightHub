

<?php
require_once 'db_connect.php'; // Include your database connection
include 'session_check.php';

// Check if 'feedback_id' is missing or empty in the GET request
if (!isset($_GET['feedback_id']) || empty($_GET['feedback_id'])) {
    echo "<p>No feedback was selected. Please select feedback to respond.</p>";
    echo "<a href='dashboard_admin.php' style='font-size: 16px; color: blue;'>Return to Dashboard</a>";
    exit(); // Stop further script execution
}

$feedback_id = (int)$_GET['feedback_id'];

// Retrieve feedback details and associated user information
$sql = "SELECT 
            f.feedback_id, 
            f.rating, 
            f.comment, 
            f.category, 
            f.created_at, 
            f.feedback_status,
            u.name, 
            u.email 
        FROM InsightHub_Feedback AS f 
        LEFT JOIN InsightHub_Users AS u 
        ON f.user_id = u.user_id 
        WHERE f.feedback_id = $feedback_id";

$result = $conn->query($sql);

if ($result->num_rows === 0) {
    echo "Feedback not found.";
    exit;
}

$feedback = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize input
    $response = $conn->real_escape_string($_POST['response']);
    $new_status = $_POST['status'] ?? 'responded';

    // Prepare SQL to update feedback with response
    $update_sql = "UPDATE InsightHub_Feedback 
                   SET response = '$response', 
                       response_date = NOW(), 
                       feedback_status = '$new_status' 
                   WHERE feedback_id = $feedback_id";

    if ($conn->query($update_sql) === TRUE) {
        // Redirect with success message
        header("Location: dashboard_admin.php?success=1&message=Response+submitted+successfully");
        exit;
    } else {
        echo "Error: " . $conn->error;
        exit;
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Respond to Feedback</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="admin_respond_feedback.css">
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
            <li><a href="dashboard_admin.php">
                <i class="ri-dashboard-line"></i> Dashboard
            </a></li>
            <li><a href="display_feedback.php">
                <i class="ri-list-check" class="active"></i> All Feedback
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
        <div class="container-fluid py-4">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card feedback-card">
                        <div class="feedback-header">
                            <h2 class="text-center mb-0">Respond to Feedback</h2>
                            <!-- <a href="dashboard_admin.php" class="position-absolute top-0 end-0 mt-3 me-3 text-white" style="text-decoration: none;"> -->
                                <!-- <i class="ri-arrow-left-line me-1"></i>Go back to Dashboard -->
                            <!-- </a> -->
                        </div>
                        <div class="card-body p-4">
                            <div class="feedback-details mb-4">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="mb-2">
                                            <span class="detail-label">User:</span> 
                                            <span class="detail-value"><?= htmlspecialchars($feedback['name']) ?></span>
                                        </p>
                                        <p class="mb-2">
                                            <span class="detail-label">Email:</span> 
                                            <span class="detail-value"><?= htmlspecialchars($feedback['email']) ?></span>
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-2">
                                            <span class="detail-label">Rating:</span> 
                                            <span class="detail-value"><?= htmlspecialchars($feedback['rating']) ?></span>
                                        </p>
                                        <p class="mb-2">
                                            <span class="detail-label">Date:</span> 
                                            <span class="detail-value"><?= htmlspecialchars($feedback['created_at']) ?></span>
                                        </p>
                                    </div>
                                </div>
                                <hr>
                                <p class="mb-2">
                                    <span class="detail-label">Comment:</span> 
                                    <span class="detail-value"><?= htmlspecialchars($feedback['comment']) ?></span>
                                </p>
                                <p class="mb-2">
                                    <span class="detail-label">Category:</span> 
                                    <span class="detail-value"><?= htmlspecialchars($feedback['category']) ?></span>
                                </p>
                                <p class="mb-0">
                                    <span class="detail-label">Current Status:</span> 
                                    <span class="detail-value"><?= htmlspecialchars($feedback['feedback_status']) ?></span>
                                </p>
                            </div>

                            <form action="" method="POST">
                                <div class="mb-3">
                                    <label for="response" class="form-label">Your Response</label>
                                    <textarea 
                                        id="response" 
                                        name="response" 
                                        class="form-control" 
                                        rows="5" 
                                        placeholder="Write your detailed response here..."
                                        required
                                    > <?= htmlspecialchars($feedback['response']) ?></textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="status" class="form-label">Update Status</label>
                                    <select 
                                        name="status" 
                                        id="status" 
                                        class="form-select"
                                    >
                                        <option value="responded" <?= $feedback['feedback_status'] == 'responded' ? 'selected' : '' ?>>
                                            Responded
                                        </option>
                                        <option value="resolved" <?= $feedback['feedback_status'] == 'resolved' ? 'selected' : '' ?>>
                                            Resolved
                                        </option>
                                        <option value="pending" <?= $feedback['feedback_status'] == 'pending' ? 'selected' : '' ?>>
                                            Keep Pending
                                        </option>
                                    </select>
                                </div>

                                <button type="submit" class="btn btn-submit btn-primary w-100">
                                    Submit Response
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="quick-nav">
        <a href="dashboard_admin.php" class="quick-nav-btn" title="Back to Dashboard">
            <i class="ri-arrow-left-line"></i>
        </a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>



