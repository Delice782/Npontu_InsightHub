<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$servername = 'localhost';  
$username = 'delice.ishimwe';
$password = 'Delice@123';
$dbname = 'webtech_fall2024_delice_ishimwe';
$port = 3341;

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to sanitize and validate input
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize form data
    $name = sanitizeInput($_POST['name'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $message = sanitizeInput($_POST['message'] ?? '');

    // Validate inputs
    $errors = [];

    // Name validation
    if (empty($name)) {
        $errors[] = "Name is required";
    } elseif (strlen($name) < 2) {
        $errors[] = "Name must be at least 2 characters long";
    }

    // Email validation
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }

    // Message validation
    if (empty($message)) {
        $errors[] = "Message is required";
    } elseif (strlen($message) < 10) {
        $errors[] = "Message must be at least 10 characters long";
    }

    // If no validation errors, proceed with database insertion
    if (empty($errors)) {
        // Prepare SQL statement
        $stmt = $conn->prepare("INSERT INTO contact_submissions (name, email, message) VALUES (?, ?, ?)");
        
        if ($stmt === false) {
            die("Prepare failed: " . $conn->error);
        }

        // Bind parameters
        $stmt->bind_param("sss", $name, $email, $message);

        // Execute the statement
        if ($stmt->execute()) {
            // Optional: Send confirmation email
            $to = "deliceishimwe95@gmail.com";
            $subject = "New Contact Form Submission";
            $email_body = "Name: $name\n";
            $email_body .= "Email: $email\n\n";
            $email_body .= "Message:\n$message";
            
            $headers = "From: noreply@customerinsighthub.com\r\n";
            
            @mail($to, $subject, $email_body, $headers);

            // Redirect with success message
            header("Location: index.php?status=success&message=Message sent successfully");
            exit();
        } else {
            // Database insertion failed
            $errors[] = "Database error: " . $stmt->error;
        }

        // Close statement
        $stmt->close();
    }

    // If there are errors, store them in session to display on redirect
    if (!empty($errors)) {
        session_start();
        $_SESSION['form_errors'] = $errors;
        $_SESSION['form_data'] = $_POST;
        header("Location: index.php?status=error");
        exit();
    }
}

// Close connection
$conn->close();
?>