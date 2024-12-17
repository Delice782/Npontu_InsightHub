<?php
// session_check_admin.php
require_once 'session_check.php';  // First include the base session check

// Additional check specifically for admin role
if ($_SESSION['role'] !== 'admin') {
    // Set an error message
    $_SESSION['error'] = "Administrator access required.";
    
    // Redirect to an appropriate page based on their role
    header('Location: unauthorized.php');
    exit();
}
?>