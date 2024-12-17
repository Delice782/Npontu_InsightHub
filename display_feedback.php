<?php
require_once 'db_connect.php';
//include 'sesssion_check_admin.php';
include 'session_check.php';

// Ensure only admin can access
if ($_SESSION['role'] !== 'admin') {
    // Redirect to an unauthorized access page or dashboard
    header('Location: unauthorized.php');
    exit(); // Prevent further script execution
}

// Pagination setup
$results_per_page = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $results_per_page;

// Set up sorting
$sort_column = isset($_GET['sort']) ? $_GET['sort'] : 'created_at';
$order = isset($_GET['order']) && $_GET['order'] === 'asc' ? 'ASC' : 'DESC';

// Sanitize the sort column to avoid SQL injection
$allowed_columns = ['name', 'email', 'rating', 'category', 'created_at'];
if (!in_array($sort_column, $allowed_columns)) {
    $sort_column = 'created_at';
}

// Set up search
$search_query = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// Construct base search condition
$search_condition = $search_query ? 
    "WHERE (u.name LIKE '%$search_query%' 
            OR u.email LIKE '%$search_query%'
            OR f.comment LIKE '%$search_query%'
            OR f.category LIKE '%$search_query%')" 
    : '';

// Count total results for pagination
$count_sql = "SELECT COUNT(*) as total 
              FROM InsightHub_Feedback f
              JOIN InsightHub_Users u ON f.user_id = u.user_id
              $search_condition";
$count_result = $conn->query($count_sql);
$total_rows = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $results_per_page);

// SQL query to fetch feedback with search, sorting, and pagination
$sql = "SELECT f.feedback_id, f.rating, f.comment, f.category, f.created_at, f.response IS NOT NULL AS has_response, u.name, u.email
        FROM InsightHub_Feedback f
        JOIN InsightHub_Users u ON f.user_id = u.user_id
        $search_condition
        ORDER BY $sort_column $order
        LIMIT $results_per_page OFFSET $offset";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="display_feedback.css">
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-logo">
            InsightHub
        </div>
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

            <li><a href="profile.php"><i class="ri-user-line"></i> Profile</a></li>
            
            <li><a href="npontu_logout.php">
                <i class="ri-logout-box-r-line"></i> Logout
            </a></li>
        </ul>
    </div>


    <!-- Main Content -->
    <div class="main-content">
        <div class="header">
            <h2>Feedback Submissions</h2>
        </div>

        <div class="search-container">
            <form method="GET" class="search-form">
                <input type="text" name="search" class="search-input" 
                       placeholder="Search by name, email, comment, or category..."
                       value="<?= htmlspecialchars($search_query) ?>">
                <button type="submit" class="search-button">Search</button>
            </form>
        </div>

        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="feedback-item">
                    <div class="feedback-header">
                        <div class="feedback-user">
                            <i class="ri-user-line"></i>
                            <strong><?= htmlspecialchars($row['name']) ?></strong>
                            <span> (<?= htmlspecialchars($row['email']) ?>)</span>
                        </div>
                        <div class="feedback-rating">
                            Rating: <?= htmlspecialchars($row['rating']) ?>
                        </div>
                    </div>

                    <div class="feedback-details">
                    <div class="feedback-category">
                        <?= htmlspecialchars(ucwords(strtolower($row['category']))) ?>
                    </div>

                        <div class="feedback-comment">
                            <?= htmlspecialchars($row['comment']) ?>
                        </div>
                    </div>

                    <div class="feedback-footer">
                        <div class="feedback-date">
                            <i class="ri-calendar-line"></i> 
                            <?= htmlspecialchars($row['created_at']) ?>
                        </div>
                        <div class="feedback-actions">
                            <a href="#" 
                            class="view-feedback" 
                            data-name="<?= htmlspecialchars($row['name']) ?>"
                            data-email="<?= htmlspecialchars($row['email']) ?>"
                            data-rating="<?= htmlspecialchars($row['rating']) ?>"
                            data-category="<?= htmlspecialchars($row['category']) ?>"
                            data-comment="<?= htmlspecialchars($row['comment']) ?>"
                            data-created-at="<?= htmlspecialchars($row['created_at']) ?>">
                                View
                            </a>
                            <a href="delete_feedback.php?feedback_id=<?= $row['feedback_id'] ?>" 
                                class="delete-feedback" 
                                onclick="return confirm('Are you sure you want to delete this feedback?');">
                                    Delete
                            </a>
                            <a href="admin_respond_feedback.php?feedback_id=<?= $row['feedback_id'] ?>" 
                                class="<?= $row['has_response'] ? 'edit-response' : '' ?>">
                                <?= $row['has_response'] ? 'Edit Response' : 'Respond' ?>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>

            <!-- Pagination -->
            <!-- Pagination -->
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?= $page - 1 ?>">
                        <i class="ri-arrow-left-line"></i> Previous
                    </a>
                <?php endif; ?>

                <?php if ($page < $total_pages): ?>
                    <a href="?page=<?= $page + 1 ?>">
                        Next <i class="ri-arrow-right-line"></i>
                    </a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="no-feedback">
                No feedback submissions found. 
                <br>Users haven't submitted any feedback yet.
            </div>
        <?php endif; ?>
    </div>
</body>
<div id="viewFeedbackModal" class="modal" style="display:none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); overflow: auto;">
    <div class="modal-content" style="background-color: #fefefe; margin: 10% auto; padding: 20px; border-radius: 10px; width: 80%; max-width: 600px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <div class="modal-header" style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eee; padding-bottom: 10px;">
            <h2 id="modalTitle">Feedback Details</h2>
            <span id="closeModal" style="color: #aaa; font-size: 28px; font-weight: bold; cursor: pointer;">&times;</span>
        </div>
        <div id="modalBody" style="padding: 20px;">
            <!-- Feedback details will be dynamically inserted here -->
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const viewButtons = document.querySelectorAll('.view-feedback');
        const modal = document.getElementById('viewFeedbackModal');
        const closeModal = document.getElementById('closeModal');
        const modalBody = document.getElementById('modalBody');

        viewButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Get feedback details from data attributes
                const name = this.getAttribute('data-name');
                const email = this.getAttribute('data-email');
                const rating = this.getAttribute('data-rating');
                const category = this.getAttribute('data-category');
                const comment = this.getAttribute('data-comment');
                const createdAt = this.getAttribute('data-created-at');

                // Populate modal content
                modalBody.innerHTML = `
                    <div style="display: grid; gap: 10px;">
                        <div><strong>Name:</strong> ${name}</div>
                        <div><strong>Email:</strong> ${email}</div>
                        <div><strong>Rating:</strong> ${rating}</div>
                        <div><strong>Category:</strong> ${category}</div>
                        <div><strong>Submitted On:</strong> ${createdAt}</div>
                        <div>
                            <strong>Comment:</strong>
                            <p style="background-color: #f4f4f4; padding: 10px; border-radius: 5px;">${comment}</p>
                        </div>
                    </div>
                `;

                modal.style.display = 'block';
            });
        });

        // Close modal when clicking on close button or outside the modal
        closeModal.onclick = function() {
            modal.style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    });
</script>
</html>
<?php $conn->close(); ?>