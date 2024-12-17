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
$error = '';
$success = '';

// Handle form submission to add a new user
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Validate form data
    if (empty($name) || empty($email) || empty($password) || empty($role)) {
        $error = "All fields are required.";
    } else {
        // Check if the email already exists
        $check_query = "SELECT * FROM InsightHub_Users WHERE email = ?";
        $stmt = $conn->prepare($check_query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "Email is already taken by another user.";
        } else {
            // Hash the password before inserting it into the database
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert new user into the database
            $insert_query = "INSERT INTO InsightHub_Users (name, email, password, role) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_query);
            $stmt->bind_param("ssss", $name, $email, $hashed_password, $role);

            if ($stmt->execute()) {
                $success = "New user added successfully.";
            } else {
                $error = "Error adding user: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User - InsightHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="add_user.css">
    
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 sidebar">
                <div class="sidebar-logo mb-4">
                    <h3>InsightHub</h3>
                </div>
                <ul class="sidebar-menu">
                    <li><a href="index.php"><i class="bi bi-house"></i>Home</a></li>
                    <li><a href="dashboard_admin.php"><i class="bi bi-speedometer2"></i>Dashboard</a></li>
                    <li><a href="display_feedback.php"><i class="bi bi-chat-left-text"></i>All Feedback</a></li>
                    <li><a href="manage_users.php" class="active"><i class="bi bi-people"></i>Manage Users</a></li>
                    <li><a href="profile.php"><i class="bi bi-person"></i>Profile</a></li>
                    <li><a href="npontu_logout.php"><i class="bi bi-box-arrow-right"></i>Logout</a></li>
                </ul>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 ms-sm-auto">
                <div class="container py-5">
                    <div class="row justify-content-center">
                        <div class="col-md-8">
                            <div class="add-user-container">
                                <div class="form-header text-center">
                                    <h2 class="mb-0">Add New User</h2>
                                </div>

                                <?php if (!empty($success)): ?>
                                    <div class="alert alert-success">
                                        <?= htmlspecialchars($success) ?>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($error)): ?>
                                    <div class="alert alert-danger">
                                        <?= htmlspecialchars($error) ?>
                                    </div>
                                <?php endif; ?>

                                <form action="add_user.php" method="POST">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Full Name</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                                            <input 
                                                type="text" 
                                                name="name" 
                                                id="name" 
                                                class="form-control" 
                                                required
                                                placeholder="Enter full name"
                                            >
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email Address</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                            <input 
                                                type="email" 
                                                name="email" 
                                                id="email" 
                                                class="form-control" 
                                                required
                                                placeholder="Enter email address"
                                            >
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="password" class="form-label">Password</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                            <input 
                                                type="password" 
                                                name="password" 
                                                id="password" 
                                                class="form-control" 
                                                required
                                                placeholder="Enter password"
                                            >
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="role" class="form-label">User Role</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                                            <select 
                                                name="role" 
                                                id="role" 
                                                class="form-select" 
                                                required
                                            >
                                                <option value="">Select Role</option>
                                                <option value="customer">Customer</option>
                                                <option value="admin">Admin</option>
                                            </select>
                                        </div>
                                    </div>
                                    <button 
                                        type="submit" 
                                        class="btn btn-add-user btn-primary w-100"
                                    >
                                        <i class="bi bi-plus-circle me-2"></i>Add User
                                    </button>
                                </form>

                                <div class="text-center mt-3">
                                    <a href="manage_users.php" class="text-muted">
                                        <i class="bi bi-arrow-left me-2"></i>Back to User Management
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>