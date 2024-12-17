<?php
// session_check_customer.php
require_once 'session_check.php';  // First include the base session check

// Additional check specifically for customer role
if ($_SESSION['role'] !== 'customer') {
    // Set an error message
    $_SESSION['error'] = "You do not have permission to access this page.";
    
    // Redirect to an appropriate page based on their role
    header('Location: unauthorized.php');
    exit();
}
?>