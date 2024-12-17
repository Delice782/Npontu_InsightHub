<?php
// Start the session to access session variables
session_start();

// Check if the user is logged in (you can change the condition based on how you store the login state)
$is_logged_in = isset($_SESSION['user_id']); // Adjust 'user_id' to your actual session variable for logged-in state
$user_role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Npontu InsightHub - Transform Customer Feedback Into Action</title>
    <link rel="stylesheet" href="index.css">
    <!-- Feather Icons -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.0/feather.min.js"></script>
</head>
<body>
    <!-- Navigation Header -->
    <header class="header">
        <div class="container">
            <nav class="nav">
                <div class="logo">
                    <a href="index.html">Npontu InsightHub</a>
                </div>
                <div class="nav-links">
                    <a href="#features">Features</a>
                    <a href="#testimonials">Testimonials</a>
                    <a href="#about">About</a>
                    <a href="#contact">Contact</a>
                </div>
                <div class="auth-buttons">
                    <?php if (!$is_logged_in): ?>
                        <a href="npontu_login.php">
                            <button class="btn-login">Login</button>
                        </a>
                        <a href="npontu_signup.php">
                            <button class="btn-signup">Sign Up</button>
                        </a>
                    <?php else: ?>

                        <?php if ($user_role == 'admin'): ?>
                            <a href="dashboard_admin.php">
                                <button class="btn-signup">Dashboard</button>
                            </a>
                        <?php elseif ($user_role == 'customer'): ?>
                            <a href="customer_dashboard.php">
                                <button class="btn-signup">Dashboard</button>
                            </a>
                        <?php endif; ?>


                        <a href="npontu_logout.php">
                            <button class="btn-signup">Logout</button>
                        </a>
                    <?php endif; ?>
                </div>  

                <button class="mobile-menu" aria-label="Menu">
                    <i data-feather="menu"></i>
                </button>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <div class="hero">
        <div class="container">
            <div class="hero-content">
                <h1>Transform Customer Feedback <span class="highlight">Into Action</span></h1>
                <p class="hero-text">
                    Empower your business with real-time customer insights. Collect, analyze, and act on feedback to deliver exceptional experiences.
                </p>
                <div class="cta-buttons">
                <a href="npontu_login.php">
                    <button class="btn primary">
                        Get Started 
                        <i data-feather="chevron-right"></i>
                    </button>
                </a>

                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div id="features" class="features">
        <div class="container">
            <div class="features-grid">
                <div class="feature-card">
                    <i data-feather="message-square"></i>
                    <h3>Real-time Feedback</h3>
                    <p>Instantly collect and process customer feedback to make data-driven decisions</p>
                </div>
                <div class="feature-card">
                    <i data-feather="trending-up"></i>
                    <h3>Live Dashboard</h3>
                    <p>Monitor trends and ratings in real-time with our intuitive analytics dashboard</p>
                </div>
                <div class="feature-card">
                    <i data-feather="users"></i>
                    <h3>Customer-Centric</h3>
                    <p>Provide personalized support and build stronger relationships with customers</p>
                </div>
                <div class="feature-card">
                    <i data-feather="zap"></i>
                    <h3>Smart Tagging</h3>
                    <p>Automatically identify common issues and improvement areas</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Testimonials Section -->
    <div id="testimonials" class="testimonials">
        <div class="container">
            <div class="testimonials-header">
                <h2>Trusted by Growing Businesses</h2>
                <div class="rating">
                    <i data-feather="star" class="star"></i>
                    <i data-feather="star" class="star"></i>
                    <i data-feather="star" class="star"></i>
                    <i data-feather="star" class="star"></i>
                    <i data-feather="star" class="star"></i>
                </div>
                <!-- <p>Average rating of 4.9/5 from over 1000+ customers</p> -->
            </div>
            <div class="testimonials-grid">
                <div class="testimonial-card">
                    <p>"InsightHub has revolutionized how we handle customer feedback. It's now easier than ever to understand our customers' needs."</p>
                    <div class="testimonial-author">
                        <div class="author-avatar">1</div>
                        
                        <div class="author-info">
                            <strong>Giru Jean</strong>
                            <div class="company-name">Founder of Girux Cosmetics Shop</div>
                        </div>

                    </div>
                </div>
                <div class="testimonial-card">
                    <p>"The real-time dashboard has helped us identify and fix issues faster than ever before. Great platform!"</p>
                    <div class="testimonial-author">
                        <div class="author-avatar">2</div>
                        <div class="author-info">
                            <strong>Dahlia Mackenzie</strong>
                            <div class="company-name">CEO of DM Tech Company</div>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card">
                    <p>"Smart tagging feature saves us hours of manual work. The insights we get are invaluable for our business."</p>
                    <div class="testimonial-author">
                        <div class="author-avatar">3</div>
                        <div class="author-info">
                            <strong>Betty Benimana</strong>
                            <div class="company-name">CTO of Beauty440</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- About Section -->
    <div id="about" class="about">
        <div class="container">
            <div class="about-content">
                <h2>About Npontu InsightHub</h2>
                <p>We help businesses like yours gather and analyze customer feedback efficiently. Our platform transforms raw feedback into actionable insights, enabling you to make informed decisions and deliver better experiences.</p>
            </div>
        </div>
    </div>

    <!-- Contact Section -->
    <div id="contact" class="contact">
        <div class="container">
            <div class="contact-content">
                <h2>Get in Touch</h2>
                <p>Have questions? We'd love to hear from you. Send us a message and we'll respond as soon as possible.</p>
                <form class="contact-form" action="process_contact.php" method="POST">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" required></textarea>
                    </div>
                    <button type="submit" class="btn primary">Send Message</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-logo">
                    <h3>Npontu InsightHub</h3>
                    <p>Transform Customer Feedback Into Action</p>
                </div>
                <div class="footer-links">
                    <div class="footer-section">
                        <h4>Product</h4>
                        <a href="#features">Features</a>
                        <a href="#testimonials">Testimonials</a>
                        
                    </div>
                    <div class="footer-section">
                        <h4>Company</h4>
                        <a href="#about">About</a>
                        <a href="#contact">Contact</a>
                
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 Npontu InsightHub. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="script.js"></script>
</body>
</html>