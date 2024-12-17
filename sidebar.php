<?php
// Assuming $new_responses is defined in the main page or passed via a variable
$new_responses = isset($new_responses) ? $new_responses : 0;
?>
<aside class="sidebar">
    <div class="sidebar-logo">InsightHub</div>
    <ul class="sidebar-menu">
        <li><a href="index.php" class="nav-btn nav-btn-primary"><i class="ri-home-line"></i> Home</a></li>
        <li><a href="customer_dashboard.php" class="<?= basename($_SERVER['PHP_SELF']) == 'customer_dashboard.php' ? 'active' : '' ?>">
            <i class="ri-dashboard-line"></i> Dashboard</a>
        </li>
        <li><a href="submit_feedback.php"><i class="ri-feedback-line"></i> Submit Feedback</a></li>
        <li>
            <a href="view_my_feedback.php">
                <i class="ri-list-check"></i> My Feedback 
                <?php if ($new_responses > 0): ?>
                    <span class="notification-badge pulse">
                        <?= $new_responses ?>
                    </span>
                <?php endif; ?>
            </a>
        </li>
        <li>
            <a href="view_response.php" class="<?= basename($_SERVER['PHP_SELF']) == 'view_response.php' ? 'active' : '' ?>">
                <i class="ri-message-2-line"></i> Feedback Responses
                <?php if ($new_responses > 0): ?>
                    <span class="notification-badge pulse">
                        <?= $new_responses ?>
                    </span>
                <?php endif; ?>
            </a>
        </li>
        <li><a href="profile.php"><i class="ri-user-line"></i> Profile</a></li>
        <li><a href="npontu_logout.php"><i class="ri-logout-box-r-line"></i> Logout</a></li>
    </ul>
</aside>