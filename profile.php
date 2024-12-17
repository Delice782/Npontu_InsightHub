


<?php
require_once 'db_connect.php';
include 'session_check.php';
session_start();

// Ensure only 'customer' or 'admin' can access
if ($_SESSION['role'] !== 'customer' && $_SESSION['role'] !== 'admin') {
    // Redirect to an unauthorized access page or dashboard
    header('Location: unauthorized.php');
    exit(); // Prevent further script execution
}


// Fetch user role
$role = $_SESSION['role'];

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Fetch current user profile
try {
    $profile_query = "SELECT name, email FROM InsightHub_Users WHERE user_id = ?";
    $stmt = $conn->prepare($profile_query);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $profile_result = $stmt->get_result()->fetch_assoc();

    // Handle profile update
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = trim($_POST['name'] ?? '');
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        $response = ['success' => false, 'message' => ''];

        // Validate inputs
        if (empty($name)) {
            $response['message'] = "Name cannot be empty.";
        } else {
            // Verify current password for any changes
            $verify_password_query = "SELECT password FROM InsightHub_Users WHERE user_id = ?";
            $stmt = $conn->prepare($verify_password_query);
            $stmt->bind_param('i', $user_id);
            $stmt->execute();
            $password_result = $stmt->get_result()->fetch_assoc();

            // Update name first
            $update_query = "UPDATE InsightHub_Users SET name = ? WHERE user_id = ?";
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param('si', $name, $user_id);
            
            if ($stmt->execute()) {
                // Update session name
                $_SESSION['name'] = $name;

                // Password change logic
                if (!empty($new_password)) {
                    // Verify current password
                    if (password_verify($current_password, $password_result['password'])) {
                        // Validate new password
                        if (strlen($new_password) < 8) {
                            $response['message'] = "New password must be at least 8 characters long.";
                        } elseif ($new_password !== $confirm_password) {
                            $response['message'] = "New passwords do not match.";
                        } else {
                            // Hash new password
                            $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
                            
                            $password_update_query = "UPDATE InsightHub_Users SET password = ? WHERE user_id = ?";
                            $stmt = $conn->prepare($password_update_query);
                            $stmt->bind_param('si', $hashed_new_password, $user_id);
                            
                            if ($stmt->execute()) {
                                $response['success'] = true;
                                $response['message'] = "Profile and password updated successfully.";
                            } else {
                                $response['message'] = "Failed to update password.";
                            }
                        }
                    } else {
                        $response['message'] = "Current password is incorrect.";
                    }
                } else {
                    $response['success'] = true;
                    $response['message'] = "Profile updated successfully.";
                }
            } else {
                $response['message'] = "Failed to update profile.";
            }
        }

        // If this is an AJAX request, return JSON response
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode($response);
            exit();
        }
    }
} catch (Exception $e) {
    $error = "Database Error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - InsightHub</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="profile.css">

</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <?php
        if ($role === 'admin') {
            include('sidebar_admin.php');
        } elseif ($role === 'customer') {
            include('sidebar_customer.php');
        }
        ?>

        <!-- Main Content -->
        <div class="main-content">
            <div class="profile-card">
                <div class="profile-header">
                    <i class="ri-user-line profile-icon"></i>
                    <div class="profile-title">My Profile</div>
                </div>

                <div id="message-container" class="message-container"></div>

                <form id="profile-form" method="POST" action="profile.php">
                    <div class="form-group">
                        <label class="form-label" for="email">Email Address</label>
                        <input type="email" id="email" class="form-input" 
                               value="<?= htmlspecialchars($profile_result['email']) ?>" 
                               readonly>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="name">Full Name</label>
                        <input type="text" id="name" name="name" class="form-input" 
                               value="<?= htmlspecialchars($profile_result['name']) ?>" 
                               required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Change Password (Optional)</label>
                        <input type="password" name="current_password" class="form-input" 
                               placeholder="Current Password" 
                               autocomplete="off">
                        <input type="password" name="new_password" class="form-input" 
                               placeholder="New Password" 
                               style="margin-top: 10px;" 
                               autocomplete="new-password">
                        <input type="password" name="confirm_password" class="form-input" 
                               placeholder="Confirm New Password" 
                               style="margin-top: 10px;" 
                               autocomplete="new-password">
                    </div>

                    <button type="submit" class="submit-btn">
                        <i class="ri-save-line"></i> Update Profile
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('profile-form');
        const messageContainer = document.getElementById('message-container');
        const nameInput = document.getElementById('name');

        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(form);

            fetch('profile.php', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Clear password fields
                    form.querySelector('input[name="current_password"]').value = '';
                    form.querySelector('input[name="new_password"]').value = '';
                    form.querySelector('input[name="confirm_password"]').value = '';

                    // Update username display if necessary using JavaScript
                    const usernameElements = document.querySelectorAll('.username-display');
                    usernameElements.forEach(el => {
                        el.textContent = nameInput.value;
                    });

                    // Show success message
                    messageContainer.innerHTML = `
                        <div class="success-message">
                            <i class="ri-checkbox-circle-line"></i> ${data.message}
                        </div>
                    `;
                } else {
                    // Show error message
                    messageContainer.innerHTML = `
                        <div class="error-message">
                            <i class="ri-error-warning-line"></i> ${data.message}
                        </div>
                    `;
                }
            })
            .catch(error => {
                messageContainer.innerHTML = `
                    <div class="error-message">
                        <i class="ri-error-warning-line"></i> An unexpected error occurred.
                    </div>
                `;
                console.error('Error:', error);
            });
        });
    });
    </script>
</body>
</html>
<?php $conn->close(); ?>