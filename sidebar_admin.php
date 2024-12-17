<div class="sidebar">
    <div class="sidebar-logo">InsightHub</div>
    <ul class="sidebar-menu">
        <li><a href="index.php" class="<?= basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : '' ?>">
            <i class="ri-home-line"></i> Home
        </a></li>
        <li><a href="dashboard_admin.php" class="<?= basename($_SERVER['PHP_SELF']) === 'dashboard_admin.php' ? 'active' : '' ?>">
            <i class="ri-dashboard-line"></i> Dashboard
        </a></li>
        <li><a href="display_feedback.php" class="<?= basename($_SERVER['PHP_SELF']) === 'display_feedback.php' ? 'active' : '' ?>">
            <i class="ri-list-check"></i> All Feedback
        </a></li>
        <li><a href="manage_users.php" class="<?= basename($_SERVER['PHP_SELF']) === 'manage_users.php' ? 'active' : '' ?>">
            <i class="ri-user-settings-line"></i> Manage Users
        </a></li>
        <li><a href="profile.php" class="<?= basename($_SERVER['PHP_SELF']) === 'profile.php' ? 'active' : '' ?>">
            <i class="ri-user-line"></i> Profile
        </a></li>
        <li><a href="npontu_logout.php">
            <i class="ri-logout-box-r-line"></i> Logout
        </a></li>
    </ul>
</div>