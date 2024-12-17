<?php

// Include database connection
require_once 'db_connect.php';
include 'session_check.php';

// Ensure only admin can access
if ($_SESSION['role'] !== 'admin') {
    // Redirect to an unauthorized access page or dashboard
    header('Location: unauthorized.php');
    exit(); // Prevent further script execution
}

// Initialize variables
$users = [];
$error = '';
$success = '';

// Pagination
$results_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $results_per_page;

// Handle user actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Delete User
    if (isset($_POST['delete_user'])) {
        $user_id = (int)$_POST['user_id'];
        $delete_query = "DELETE FROM InsightHub_Users WHERE user_id = ?";
        $stmt = $conn->prepare($delete_query);
        $stmt->bind_param("i", $user_id);
        
        if ($stmt->execute()) {
            $success = "User successfully deleted.";
        } else {
            $error = "Error deleting user: " . $stmt->error;
        }
        $stmt->close();
    }

    // Update User Role
    if (isset($_POST['update_role'])) {
        $user_id = (int)$_POST['user_id'];
        $new_role = $_POST['current_role'] == 'admin' ? 'customer' : 'admin';
        
        $role_query = "UPDATE InsightHub_Users SET role = ? WHERE user_id = ?";
        $stmt = $conn->prepare($role_query);
        $stmt->bind_param("si", $new_role, $user_id);
        
        if ($stmt->execute()) {
            $success = "User role updated successfully.";
        } else {
            $error = "Error updating user role: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Fetch users with pagination
try {
    // Count total users for pagination
    $count_query = "SELECT COUNT(*) as total_users FROM InsightHub_Users";
    $count_result = $conn->query($count_query);
    $total_users = $count_result->fetch_assoc()['total_users'];
    $total_pages = ceil($total_users / $results_per_page);

    $logged_in_user_id = $_SESSION['user_id']; 

    // Fetch paginated users
    $users_query = "SELECT user_id, name, email, role, created_at 
                    FROM InsightHub_Users WHERE user_id != ?
                    ORDER BY created_at DESC 
                    LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($users_query);
    $stmt->bind_param("iii", $logged_in_user_id, $results_per_page, $offset);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    $stmt->close();

} catch (Exception $e) {
    $error = "Database Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - InsightHub Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="manage_users.css">
    
</head>
<body>
    
<div class="dashboard-container">
        <!-- Sidebar Added Here -->
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
                <li><a href="manage_users.php" class="active">
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

        <!-- Main Content -->
        <div class="main-content">
            <div class="users-header">
                <h1>Manage Users</h1>
                <a href="add_user.php" class="btn-primary">
                    <i class="ri-add-line"></i> Add User
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

            <!-- Users Table -->
            <div class="users-table">
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= htmlspecialchars($user['name']) ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td>
                                    <span class="role-badge role-<?= strtolower($user['role']) ?>">
                                        <?= htmlspecialchars(ucfirst($user['role'])) ?>
                                    </span>
                                </td>
                                <td><?= date('M d, Y', strtotime($user['created_at'])) ?></td>
                                <td>
                                    <div class="action-buttons" style="display: flex; gap: 8px; align-items: center;">
                                        <a href="#" 
                                            class="btn-edit view-user view-user"
                                            data-name="<?= htmlspecialchars($user['name']) ?>"
                                            data-email="<?= htmlspecialchars($user['email']) ?>"
                                            data-role="<?= htmlspecialchars($user['role']) ?>"
                                            data-created-at="<?= date('M d, Y h:i A', strtotime($user['created_at'])) ?>">
                                            <i class="ri-eye-line"></i> View
                                        </a>
                                        <a href="edit_user.php?user_id=<?= $user['user_id'] ?>" class="btn-edit">
                                            <i class="ri-edit-line"></i> Edit
                                        </a>
                                        <form method="POST" onsubmit="return confirm('Are you sure you want to change this user\'s role?');">
                                            <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">
                                            <input type="hidden" name="current_role" value="<?= $user['role'] ?>">
                                            <button type="submit" name="update_role" class="btn btn-warning">
                                                <?= $user['role'] == 'admin' ? 'Make Customer' : 'Make Admin' ?>
                                            </button>
                                        </form>
                                        <form method="POST" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                            <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">
                                            <button type="submit" name="delete_user" class="btn btn-danger">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <!-- Pagination -->
            <div class="pagination">
                <?php 
                // Only show Previous button if not on the first page and there are users
                if ($page > 1 && !empty($users)): ?>
                    <a href="?page=<?= $page - 1 ?>">
                        <i class="ri-arrow-left-line"></i> Previous
                    </a>
                <?php endif; ?>

                <?php 
                // Only show Next button if there are more pages and current page has users
                if ($page < $total_pages && !empty($users)): ?>
                    <a href="?page=<?= $page + 1 ?>">
                        Next <i class="ri-arrow-right-line"></i>
                    </a>
                <?php endif; ?>
            </div>

            

        </div>
    </div>
    <!-- Add this just before the closing </body> tag -->
    <div id="userModal" class="modal" style="display:none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); overflow: auto;">
        <div class="modal-content" style="background-color: #fefefe; margin: 10% auto; padding: 20px; border-radius: 10px; width: 80%; max-width: 600px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <div class="modal-header" style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eee; padding-bottom: 10px;">
                <h2 id="modalTitle">User Details</h2>
                <span id="closeModal" style="color: #aaa; font-size: 28px; font-weight: bold; cursor: pointer;">&times;</span>
            </div>
            <div id="modalBody" style="padding: 20px;">
                <!-- User details will be dynamically inserted here -->
            </div>
        </div>
    </div>

    
</body>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const viewButtons = document.querySelectorAll('.view-user');
        const modal = document.getElementById('userModal');
        const closeModal = document.getElementById('closeModal');
        const modalBody = document.getElementById('modalBody');

        viewButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Get user details from data attributes
                const name = this.getAttribute('data-name');
                const email = this.getAttribute('data-email');
                const role = this.getAttribute('data-role');
                const createdAt = this.getAttribute('data-created-at');

                // Populate modal content
                modalBody.innerHTML = `
                    <div style="display: grid; gap: 10px;">
                        <div><strong>Name:</strong> ${name}</div>
                        <div><strong>Email:</strong> ${email}</div>
                        <div><strong>Role:</strong> 
                            <span class="role-badge role-${role.toLowerCase()}">
                                ${role.charAt(0).toUpperCase() + role.slice(1)}
                            </span>
                        </div>
                        <div><strong>Created At:</strong> ${createdAt}</div>
                    </div>
                `;

                modal.style.display = 'block';
            });
        });

        // Close modal when clicking on close button or outside the modal
        closeModal.onclick = function() {
            modal.style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    });
</script>
</html>