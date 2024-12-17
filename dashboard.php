<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Npontu InsightHub</title>
    <link rel="stylesheet" href="npontu_auth.css">
    <!-- Feather Icons -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.0/feather.min.js"></script>
    <!-- Recharts for Graphs -->
    <script src="https://unpkg.com/recharts/umd/Recharts.js"></script>
</head>
<body>
    <!-- Customer Dashboard -->
    <div id="customer-dashboard" class="dashboard-container">
        <header class="dashboard-header">
            <div class="container">
                <nav class="nav">
                    <div class="logo">
                        <a href="index.html">Npontu InsightHub</a>
                    </div>
                    <div class="nav-links">
                        <div class="user-info">
                            <span class="user-name"><?php echo htmlspecialchars($name); ?></span>
                            <span class="user-role">Customer</span>
                        </div>
                        <a href="profile.php" class="nav-link">
                            <i data-feather="user"></i> Profile
                        </a>
                        <a href="npontu_logout.php" class="nav-link">
                            <i data-feather="log-out"></i> Logout
                        </a>
                    </div>
                </nav>
            </div>
        </header>

        <main class="dashboard-content">
            <div class="container">
                <div class="dashboard-grid">
                    <!-- Customer Feedback Overview -->
                    <div class="dashboard-section">
                        <h2>My Feedback Overview</h2>
                        <div class="feedback-stats">
                            <div class="stat-card">
                                <i data-feather="message-square"></i>
                                <div class="stat-content">
                                    <h3>Total Submissions</h3>
                                    <p class="stat-number">12</p>
                                </div>
                            </div>
                            <div class="stat-card">
                                <i data-feather="check-circle"></i>
                                <div class="stat-content">
                                    <h3>Resolved Issues</h3>
                                    <p class="stat-number">8</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Feedback Trend Graph -->
                    <div class="dashboard-section">
                        <h2>My Feedback Response Trend</h2>
                        <div id="feedback-trend-chart" class="chart-container">
                            <!-- Recharts Line Chart will be rendered here -->
                        </div>
                    </div>

                    <!-- Recent Feedback Status -->
                    <div class="dashboard-section">
                        <h2>Recent Feedback Status</h2>
                        <div class="feedback-list">
                            <div class="feedback-item">
                                <div class="feedback-header">
                                    <span class="feedback-source">Product Quality</span>
                                    <span class="feedback-status in-progress">In Progress</span>
                                </div>
                                <p>Suggestion for packaging improvement</p>
                                <div class="feedback-footer">
                                    <span>Submitted on: Jan 15, 2024</span>
                                    <button class="btn-small">View Details</button>
                                </div>
                            </div>
                            <div class="feedback-item">
                                <div class="feedback-header">
                                    <span class="feedback-source">Customer Service</span>
                                    <span class="feedback-status resolved">Resolved</span>
                                </div>
                                <p>Excellent support response</p>
                                <div class="feedback-footer">
                                    <span>Resolved on: Dec 20, 2023</span>
                                    <button class="btn-small">View Resolution</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="dashboard-section">
                        <h2>Quick Actions</h2>
                        <div class="actions-grid">
                            <button class="btn primary">
                                <i data-feather="plus-circle"></i> Submit New Feedback
                            </button>
                            <button class="btn secondary">
                                <i data-feather="search"></i> Track Existing Feedback
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Support Team Dashboard -->
    <div id="support-dashboard" class="dashboard-container">
        <header class="dashboard-header">
            <div class="container">
                <nav class="nav">
                    <div class="logo">
                        <a href="index.html">Npontu InsightHub</a>
                    </div>
                    <div class="nav-links">
                        <div class="user-info">
                            <span class="user-name"><?php echo htmlspecialchars($name); ?></span>
                            <span class="user-role">Support Team</span>
                        </div>
                        <a href="profile.php" class="nav-link">
                            <i data-feather="user"></i> Profile
                        </a>
                        <a href="npontu_logout.php" class="nav-link">
                            <i data-feather="log-out"></i> Logout
                        </a>
                    </div>
                </nav>
            </div>
        </header>

        <main class="dashboard-content">
            <div class="container">
                <div class="dashboard-grid">
                    <!-- Support Metrics -->
                    <div class="dashboard-section">
                        <h2>Support Performance</h2>
                        <div class="stats-grid">
                            <div class="stat-card">
                                <i data-feather="inbox"></i>
                                <div class="stat-content">
                                    <h3>Total Tickets</h3>
                                    <p class="stat-number">124</p>
                                </div>
                            </div>
                            <div class="stat-card">
                                <i data-feather="clock"></i>
                                <div class="stat-content">
                                    <h3>Avg. Response Time</h3>
                                    <p class="stat-number">2.3 hrs</p>
                                </div>
                            </div>
                            <div class="stat-card">
                                <i data-feather="check-square"></i>
                                <div class="stat-content">
                                    <h3>Resolved Tickets</h3>
                                    <p class="stat-number">98</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Feedback Resolution Trend -->
                    <div class="dashboard-section">
                        <h2>Ticket Resolution Trend</h2>
                        <div id="resolution-trend-chart" class="chart-container">
                            <!-- Recharts Bar Chart will be rendered here -->
                        </div>
                    </div>

                    <!-- Pending Tickets -->
                    <div class="dashboard-section">
                        <h2>Pending Tickets</h2>
                        <div class="ticket-list">
                            <div class="ticket-item urgent">
                                <div class="ticket-header">
                                    <span class="ticket-id">#1245</span>
                                    <span class="ticket-priority high">High Priority</span>
                                </div>
                                <p>Urgent product functionality issue</p>
                                <div class="ticket-footer">
                                    <span>Opened: 2 days ago</span>
                                    <button class="btn-small">Take Action</button>
                                </div>
                            </div>
                            <div class="ticket-item">
                                <div class="ticket-header">
                                    <span class="ticket-id">#1246</span>
                                    <span class="ticket-priority medium">Medium Priority</span>
                                </div>
                                <p>Customer service experience feedback</p>
                                <div class="ticket-footer">
                                    <span>Opened: 1 day ago</span>
                                    <button class="btn-small">Assign</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Support Actions -->
                    <div class="dashboard-section">
                        <h2>Quick Support Actions</h2>
                        <div class="actions-grid">
                            <button class="btn primary">
                                <i data-feather="users"></i> Assign Tickets
                            </button>
                            <button class="btn secondary">
                                <i data-feather="filter"></i> Filter Tickets
                            </button>
                            <button class="btn">
                                <i data-feather="trending-up"></i> Generate Report
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Feedback Trend Chart for Customer
        const { LineChart, Line, XAxis, YAxis, CartesianGrid, Tooltip, Legend } = Recharts;
        
        const customerFeedbackData = [
            { name: 'Jan', submissions: 3 },
            { name: 'Feb', submissions: 5 },
            { name: 'Mar', submissions: 4 },
            { name: 'Apr', submissions: 6 },
            { name: 'May', submissions: 5 },
            { name: 'Jun', submissions: 7 }
        ];

        const CustomerFeedbackTrendChart = () => (
            <LineChart width={600} height={300} data={customerFeedbackData}>
                <CartesianGrid strokeDasharray="3 3" />
                <XAxis dataKey="name" />
                <YAxis />
                <Tooltip />
                <Legend />
                <Line type="monotone" dataKey="submissions" stroke="#8884d8" activeDot={{ r: 8 }} />
            </LineChart>
        );

        // Resolution Trend Chart for Support Team
        const supportResolutionData = [
            { name: 'Jan', resolved: 15, unresolved: 5 },
            { name: 'Feb', resolved: 20, unresolved: 4 },
            { name: 'Mar', resolved: 25, unresolved: 3 },
            { name: 'Apr', resolved: 30, unresolved: 2 },
            { name: 'May', resolved: 35, unresolved: 2 },
            { name: 'Jun', resolved: 40, unresolved: 1 }
        ];

        const SupportResolutionTrendChart = () => (
            <BarChart width={600} height={300} data={supportResolutionData}>
                <CartesianGrid strokeDasharray="3 3" />
                <XAxis dataKey="name" />
                <YAxis />
                <Tooltip />
                <Legend />
                <Bar dataKey="resolved" fill="#82ca9d" />
                <Bar dataKey="unresolved" fill="#8884d8" />
            </BarChart>
        );

        // Render charts
        ReactDOM.render(
            <CustomerFeedbackTrendChart />,
            document.getElementById('feedback-trend-chart')
        );

        ReactDOM.render(
            <SupportResolutionTrendChart />,
            document.getElementById('resolution-trend-chart')
        );
    </script>
</body>
</html>