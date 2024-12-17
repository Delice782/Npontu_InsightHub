<?php
session_start();
include 'db_connect.php';

// Check if user is already logged in and redirect to appropriate dashboard
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] == 'admin') {
        header("Location: dashboard_admin.php");
    } else {
        header("Location: customer_dashboard.php");
    }
    exit();
}

// Initialize variables
$successMsg = '';
$errorMsg = '';

// Check for signup success message
if (isset($_SESSION['signup_success'])) {
    $successMsg = $_SESSION['signup_success'];
    unset($_SESSION['signup_success']);
}

// Login processing
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize inputs
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validate inputs
    if (empty($email) || empty($password)) {
        $errorMsg = "Please enter both email and password.";
    } else {
        // Prepare SQL statement with parameterized query
        $stmt = $conn->prepare("SELECT user_id, name, email, password, role FROM InsightHub_Users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            // Verify password
            if (password_verify($password, $user['password'])) {
                // Login successful
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];

                // Redirect to specific dashboard based on role
                if ($user['role'] == 'admin') {
                    header("Location: dashboard_admin.php");
                } else {
                    header("Location: customer_dashboard.php");
                }
                exit();
            } else {
                $errorMsg = "Invalid email or password.";
            }
        } else {
            $errorMsg = "Invalid email or password.";
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Npontu InsightHub</title>
    <link rel="stylesheet" href="npontu_auth.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.0/feather.min.js"></script>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <a href="index.html" class="logo">Npontu InsightHub</a>
                <h1>Welcome Back!</h1>
                <p>Please login to your account</p>
            </div>

            <?php if (!empty($successMsg)): ?>
                <div class="success-banner"><?php echo htmlspecialchars($successMsg); ?></div>
            <?php endif; ?>

            <?php if (!empty($errorMsg)): ?>
                <div class="error-banner" id="errorBanner"><?php echo htmlspecialchars($errorMsg); ?></div>
            <?php endif; ?>

            <form id="loginForm" class="auth-form" method="POST" action="">
                <div class="form-group">
                    <label for="email">Email</label>
                    <div class="input-group">
                        <i data-feather="mail"></i>
                        <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($email ?? ''); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-group">
                        <i data-feather="lock"></i>
                        <input type="password" id="password" name="password" required>
                        <button type="button" class="toggle-password" id="togglePassword">
                            <i data-feather="eye"></i>
                        </button>
                    </div>
                </div>


                <button type="submit" class="btn-submit">Login</button>

                <p class="auth-redirect">
                    Don't have an account? <a href="npontu_signup.php">Sign Up</a>
                </p>
            </form>
        </div>
    </div>

    <script>
        feather.replace();

        // Password visibility toggle
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordField = document.getElementById('password');
            const type = passwordField.type === 'password' ? 'text' : 'password';
            passwordField.type = type;
            
            const icon = this.querySelector('i');
            icon.setAttribute('data-feather', type === 'password' ? 'eye' : 'eye-off');
            feather.replace();
        });

        // Clear error message when user starts typing
        document.querySelectorAll('input').forEach(input => {
            input.addEventListener('input', function() {
                // Clear the error message in the error banner
                const errorBanner = document.getElementById('errorBanner');
                if (errorBanner) {
                    errorBanner.textContent = ''; // Clear the error message
                }
            });
        });
    </script>
</body>
</html>