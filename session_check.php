<?php
// session_check.php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Set an error message
    $_SESSION['error'] = "Please log in to access this page.";
    
    // Redirect to login page
    header('Location: npontu_login.php');
    exit();
}
?>