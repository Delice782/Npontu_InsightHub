<?php
session_start();
require_once 'db_connect.php';
include 'session_check.php';

// Ensure only admin can access
if ($_SESSION['role'] !== 'admin') {
    // Redirect to an unauthorized access page or dashboard
    header('Location: unauthorized.php');
    exit(); // Prevent further script execution
}

// Check if 'feedback_id' is missing in the GET request
if (!isset($_GET['feedback_id']) || empty($_GET['feedback_id'])) {
    echo "<p style='color: red; font-size: 16px;'>No feedback was selected for deletion. Deletion cannot proceed.</p>";
    echo "<a href='dashboard_admin.php' style='font-size: 16px; color: blue;'>Go back to Dashboard</a>";
    exit(); // Stop further script execution
}


$feedback_id = intval($_GET['feedback_id']);

try {
    // Begin transaction for safer deletion
    $conn->begin_transaction();

    // Prepare and execute delete statement with error handling
    $delete_sql = "DELETE FROM InsightHub_Feedback WHERE feedback_id = ?";
    $stmt = $conn->prepare($delete_sql);
    
    if (!$stmt) {
        throw new Exception("Prepare statement failed: " . $conn->error);
    }
    
    $stmt->bind_param("i", $feedback_id);
    
    if (!$stmt->execute()) {
        throw new Exception("Delete execution failed: " . $stmt->error);
    }

    // Commit transaction
    $conn->commit();

    // Set success message
    $_SESSION['success'] = "Feedback successfully deleted.";
    
    header("Location: display_feedback.php");
    exit();

} catch (Exception $e) {
    // Rollback transaction in case of error
    $conn->rollback();
    
    // Log the error (you can replace this with your logging mechanism)
    error_log($e->getMessage());
    
    // Set error message
    $_SESSION['error'] = "An error occurred while deleting feedback.";
    
    header("Location: display_feedback.php");
    exit();
} finally {
    // Close statement and connection
    if (isset($stmt)) {
        $stmt->close();
    }
    $conn->close();
}