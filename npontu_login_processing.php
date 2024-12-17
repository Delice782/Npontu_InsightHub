<?php
// Include password utility functions
include 'password_util.php';

// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "rewear_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $enteredPassword = trim($_POST['password']); // Trim whitespace

    // Fetch the user from the database
    $sql = "SELECT UserID, Password FROM User WHERE Email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Fetch the user's hashed password and ID
        $row = $result->fetch_assoc();
        $hashedPassword = $row['Password'];
        $userId = $row['UserID']; // Get the UserID

        // Verify the password
        if (verifyPassword($enteredPassword, $hashedPassword)) {
            session_start(); // Start a new session
            $_SESSION['user_email'] = $email; // Store user email in session
            $_SESSION['user_id'] = $userId; // Set user ID in session

            // Redirect to the dashboard
            header("Location: dashboard_rewear.php");
            exit; // Ensure no further code is executed after the redirect
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "No user found with that email.";
    }
}