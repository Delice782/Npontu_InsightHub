<?php
// Include database connection
require_once 'db_connect.php';
include 'session_check.php';
session_start();

// Ensure only admin can access
if ($_SESSION['role'] !== 'admin') {
    // Redirect to an unauthorized access page or dashboard
    header('Location: unauthorized.php');
    exit(); // Prevent further script execution
}

// Initialize variables
$user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;
$error = '';
$success = '';
$user = null;

// Fetch user detailsu
if ($user_id > 0) {
    try {
        $query = "SELECT user_id, name, email, role FROM InsightHub_Users WHERE user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        if (!$user) {
            $error = "User not found.";
        }
    } catch (Exception $e) {
        $error = "Database Error: " . $e->getMessage();
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_user'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];

    // Validate inputs
    if (empty($name) || empty($email)) {
        $error = "Name and email are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        try {
            // Check if email already exists (excluding current user)
            $check_email_query = "SELECT user_id FROM InsightHub_Users WHERE email = ? AND user_id != ?";
            $check_stmt = $conn->prepare($check_email_query);
            $check_stmt->bind_param("si", $email, $user_id);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            
            if ($check_result->num_rows > 0) {
                $error = "Email already exists.";
                $check_stmt->close();
            } else {
                $check_stmt->close();

                // Update user details
                $update_query = "UPDATE InsightHub_Users SET name = ?, email = ?, role = ? WHERE user_id = ?";
                $stmt = $conn->prepare($update_query);
                $stmt->bind_param("sssi", $name, $email, $role, $user_id);
                
                if ($stmt->execute()) {
                    $success = "User information updated successfully.";
                    // Refresh user data
                    $user = [
                        'name' => $name,
                        'email' => $email,
                        'role' => $role
                    ];
                } else {
                    $error = "Error updating user: " . $stmt->error;
                }
                $stmt->close();
            }
        } catch (Exception $e) {
            $error = "Database Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - InsightHub Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="edit_user.css">
    
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar (same as manage_users.php) -->
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
                    <i class="ri-list-check"></i> All Feedback
                </a></li>
                <li><a href="manage_users.php">
                    <i class="ri-user-settings-line"></i> Manage Users
                </a></li>
                <li><a href="profile.php"><i class="ri-user-line"></i> Profile</a></li>
                <li><a href="npontu_logout.php">
                    <i class="ri-logout-box-r-line"></i> Logout
                </a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="users-header">
                <h1>Edit User</h1>
                <a href="manage_users.php" class="btn-primary">
                    <i class="ri-arrow-left-line"></i> Back to Users
                </a>
            </div>

            <!-- Success/Error Messages -->
            <?php if (!empty($success)): ?>
                <div class="success-message">
                    <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="error-message">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <?php if ($user): ?>
                <div class="edit-form">
                    <form method="POST">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" id="name" name="name" 
                                   value="<?= htmlspecialchars($user['name']) ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" 
                                   value="<?= htmlspecialchars($user['email']) ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="role">Role</label>
                            <select id="role" name="role" required>
                                <option value="customer" <?= $user['role'] == 'customer' ? 'selected' : '' ?>>Customer</option>
                                <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                            </select>
                        </div>

                        <div class="form-actions">
                            <a href="manage_users.php" class="btn btn-warning">Cancel</a>
                            <button type="submit" name="update_user" class="btn btn-primary">
                                Update User
                            </button>
                        </div>
                    </form>
                </div>
            <?php else: ?>
                <div class="error-message">
                    No user selected or user does not exist.
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>