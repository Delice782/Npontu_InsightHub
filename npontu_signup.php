

<?php
// Include necessary files
include 'db_connect.php';

// Initialize variables
$errors = [];
$formData = []; // To preserve form data after validation

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Preserve form data
    $formData = [
        'fullName' => trim($_POST['fullName'] ?? ''),
        'email' => trim($_POST['email'] ?? '')
    ];

    // Sanitize and validate inputs
    $name = $formData['fullName'];
    $email = $formData['email'];
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';

    // Validate name
    if (empty($name)) {
        $errors['name'] = "Name is required.";
    }

    // Validate email with comprehensive checks
    // Validate email with comprehensive checks
    if (empty($email)) {
        $errors['email'] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format.";
    } elseif (strlen($email) < 5 || strlen($email) > 100) {
        $errors['email'] = "Email must be between 5 and 100 characters.";
    } else {
        // Check for existing email
        $stmt = $conn->prepare("SELECT user_id FROM InsightHub_Users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors['email'] = "Email already exists.";
        }
        $stmt->close();
    }

    // Validate password
    if (empty($password)) {
        $errors['password'] = "Password is required.";
    } elseif (strlen($password) < 8) {
        $errors['password'] = "Password must be at least 8 characters long.";
    } 

    // Validate confirm password
    if (empty($confirmPassword)) {
        $errors['confirmPassword'] = "Please confirm your password.";
    } elseif ($password !== $confirmPassword) {
        $errors['confirmPassword'] = "Passwords do not match.";
    }
    // If no errors, proceed with registration
    if (empty($errors)) {
        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Prepare insert statement
        $stmt = $conn->prepare("INSERT INTO InsightHub_Users (name, email, password, role, created_at) VALUES (?, ?, ?, ?, NOW())");
        $role = 'customer'; // Default role
        $stmt->bind_param("ssss", $name, $email, $hashedPassword, $role);

        // Execute the statement
        if ($stmt->execute()) {
            // Registration successful
            $_SESSION['signup_success'] = "Account created successfully. Please log in.";
            header("Location: npontu_login.php");
            exit();
        } else {
            $errors['general'] = "Registration failed. Please try again.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Npontu InsightHub</title>
    <link rel="stylesheet" href="npontu_auth.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.0/feather.min.js"></script>
    <style>
        .error-message {
            color: red;
            font-size: 0.8em;
            margin-top: 5px;
            display: block;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <a href="index.html" class="logo">Npontu InsightHub</a>
                <h1>Create Account</h1>
                <p>Join us to start collecting customer feedback</p>
            </div>

            <?php 
            // Display any general errors
            if (!empty($errors['general'])): ?>
                <div class="error-banner"><?php echo htmlspecialchars($errors['general']); ?></div>
            <?php endif; ?>

            <form id="signupForm" class="auth-form" method="POST" action="">
                <div class="form-group">
                    <label for="fullName">Full Name</label>
                    <div class="input-group">
                        <i data-feather="user"></i>
                        <input type="text" id="fullName" name="fullName" 
                               value="<?php echo htmlspecialchars($formData['fullName'] ?? ''); ?>" 
                               required>
                    </div>
                    <?php if (!empty($errors['name'])): ?>
                        <span class="error-message"><?php echo htmlspecialchars($errors['name']); ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <div class="input-group">
                        <i data-feather="mail"></i>
                        <input type="email" id="email" name="email" 
                               value="<?php echo htmlspecialchars($formData['email'] ?? ''); ?>" 
                               required>
                    </div>
                    <?php if (!empty($errors['email'])): ?>
                        <span class="error-message"><?php echo htmlspecialchars($errors['email']); ?></span>
                    <?php endif; ?>
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
                    <?php if (!empty($errors['password'])): ?>
                        <span class="error-message"><?php echo htmlspecialchars($errors['password']); ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="confirmPassword">Confirm Password</label>
                    <div class="input-group">
                        <i data-feather="lock"></i>
                        <input type="password" id="confirmPassword" name="confirmPassword" required>
                        <button type="button" class="toggle-password" id="toggleConfirmPassword">
                            <i data-feather="eye"></i>
                        </button>
                    </div>
                    <?php if (!empty($errors['confirmPassword'])): ?>
                        <span class="error-message"><?php echo htmlspecialchars($errors['confirmPassword']); ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-options">
                    <?php if (!empty($errors['terms'])): ?>
                        <span class="error-message"><?php echo htmlspecialchars($errors['terms']); ?></span>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn-submit">Create Account</button>

                <p class="auth-redirect">
                    Already have an account? <a href="npontu_login.php">Login</a>
                </p>
            </form>
        </div>
    </div>

    <script>
        feather.replace();

        // Password visibility toggle
        document.querySelectorAll('.toggle-password').forEach(button => {
            button.addEventListener('click', function() {
                const input = this.previousElementSibling;
                const type = input.type === 'password' ? 'text' : 'password';
                input.type = type;
                
                const icon = this.querySelector('i');
                icon.setAttribute('data-feather', type === 'password' ? 'eye' : 'eye-off');
                feather.replace();
            });
        });

         // Clear error messages when user starts typing
        document.querySelectorAll('input').forEach(input => {
            input.addEventListener('input', function() {
                const errorMessage = this.closest('.form-group').querySelector('.error-message');
                if (errorMessage) {
                    errorMessage.textContent = ''; // Clear the error message
                }
            });
        });
    </script>
</body>
</html>