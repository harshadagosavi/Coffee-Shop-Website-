<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

include '../config.php';

// Get statistics
$user_count = $conn->query("SELECT COUNT(*) as count FROM users WHERE username != 'admin'")->fetch_assoc()['count'];
$product_count = $conn->query("SELECT COUNT(*) as count FROM products")->fetch_assoc()['count'];
$order_count = $conn->query("SELECT COUNT(*) as count FROM orders")->fetch_assoc()['count'];
$revenue = $conn->query("SELECT SUM(total) as revenue FROM orders WHERE status = 'confirmed'")->fetch_assoc()['revenue'];

// Get recent orders
$recent_orders = $conn->query("SELECT o.*, u.username FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.order_date DESC LIMIT 5");

// Get order status counts
$status_counts = $conn->query("SELECT status, COUNT(*) as count FROM orders GROUP BY status");

// Get top products
$top_products = $conn->query("SELECT p.name, SUM(oi.quantity) as total_sold FROM order_items oi JOIN products p ON oi.product_id = p.id GROUP BY p.id ORDER BY total_sold DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Coffee Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary: #667eea;
            --secondary: #764ba2;
            --dark: #343a40;
            --light: #f8f9fa;
        }
        
        body {
            background-color: #f5f7fb;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .sidebar {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            min-height: 100vh;
            color: white;
            position: fixed;
            width: 250px;
            transition: all 0.3s;
            z-index: 1000;
        }
        
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            margin: 5px 10px;
            border-radius: 10px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
        }
        
        .sidebar .nav-link:hover {
            background: rgba(255,255,255,0.1);
            color: white;
            transform: translateX(5px);
        }
        
        .sidebar .nav-link.active {
            background: rgba(255,255,255,0.2);
            color: white;
            font-weight: 600;
        }
        
        .sidebar .nav-link i {
            width: 25px;
            font-size: 1.1rem;
        }
        
        .main-content {
            margin-left: 250px;
            padding: 20px;
            transition: all 0.3s;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                margin-left: -250px;
            }
            .sidebar.active {
                margin-left: 0;
            }
            .main-content {
                margin-left: 0;
            }
            .main-content.active {
                margin-left: 250px;
            }
        }
        
        .stat-card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: all 0.3s;
            overflow: hidden;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        
        .stat-icon {
            font-size: 2.5rem;
            opacity: 0.8;
        }
        
        .navbar-toggler {
            border: none;
            font-size: 1.5rem;
        }
        
        .mobile-navbar {
            display: none;
        }
        
        @media (max-width: 768px) {
            .mobile-navbar {
                display: flex;
                background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
                color: white;
            }
            .stat-card {
                margin-bottom: 15px;
            }
        }
        
        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        
        .badge-status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
        }
    </style>
</head>
<body>
    <!-- Mobile Navbar -->
    <nav class="mobile-navbar navbar navbar-dark d-md-none p-3">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
            <span class="navbar-brand ms-2">Admin Panel</span>
        </div>
    </nav>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="d-flex flex-column h-100">
            <!-- Logo -->
            <div class="p-4 text-center border-bottom border-light border-opacity-25">
                <h3 class="mb-0">
                    <i class="fas fa-coffee me-2"></i>
                    Coffee Shop
                </h3>
                <small class="text-white-50">Admin Panel</small>
            </div>

            <!-- Navigation -->
        
<div class="flex-grow-1 p-3">
    <ul class="nav flex-column">
        <li class="nav-item mb-2">
            <a class="nav-link active" href="dashboard.php">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li class="nav-item mb-2">
            <a class="nav-link" href="manage_users.php">
                <i class="fas fa-users"></i>
                <span>Users</span>
            </a>
        </li>
        <li class="nav-item mb-2">
            <a class="nav-link" href="manage_products.php">
                <i class="fas fa-box"></i>
                <span>Products</span>
            </a>
        </li>
        <li class="nav-item mb-2">
            <a class="nav-link" href="manage_orders.php">
                <i class="fas fa-shopping-cart"></i>
                <span>Orders</span>
            </a>
        </li>
        <li class="nav-item mb-2">
            <a class="nav-link" href="reports.php">
                <i class="fas fa-chart-bar"></i>
                <span>Reports</span>
            </a>
        </li>
        <!-- MESSAGES LINK - ADD THIS -->
        <li class="nav-item mb-2">
            <a class="nav-link" href="messages.php">
                <i class="fas fa-envelope"></i>
                <span>Messages</span>
                <?php
                // Get unread message count
                $unread_count = 0;
                if (isset($conn) && $conn) {
                    $result = $conn->query("SELECT COUNT(*) as count FROM messages WHERE status='unread'");
                    if ($result) {
                        $unread_count = $result->fetch_assoc()['count'];
                    }
                }
                if ($unread_count > 0):
                ?>
                <span class="badge bg-danger ms-2"><?php echo $unread_count; ?></span>
                <?php endif; ?>
            </a>
        </li>
        <!-- END MESSAGES LINK -->
    </ul>
</div>

            <!-- Logout -->
            <div class="p-3 border-top border-light border-opacity-25">
                <a href="logout.php" class="nav-link text-danger">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
                <div class="text-white-50 small mt-2">
                    <i class="fas fa-user me-1"></i>
                    <?php echo $_SESSION['admin_username'] ?? 'Admin'; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <div class="container-fluid">
            <!-- Welcome Header -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="fw-bold">Dashboard</h2>
                            <p class="text-muted mb-0">Welcome back, <?php echo $_SESSION['admin_username'] ?? 'Admin'; ?>!</p>
                        </div>
                        <div class="d-none d-md-block">
                            <span class="badge bg-primary p-2"><?php echo date('F j, Y'); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="stat-card bg-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-1">Total Users</h6>
                                    <h3 class="mb-0"><?php echo $user_count; ?></h3>
                                </div>
                                <div class="stat-icon text-primary">
                                    <i class="fas fa-users"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="stat-card bg-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-1">Total Products</h6>
                                    <h3 class="mb-0"><?php echo $product_count; ?></h3>
                                </div>
                                <div class="stat-icon text-success">
                                    <i class="fas fa-box"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="stat-card bg-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-1">Total Orders</h6>
                                    <h3 class="mb-0"><?php echo $order_count; ?></h3>
                                </div>
                                <div class="stat-icon text-warning">
                                    <i class="fas fa-shopping-cart"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="stat-card bg-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-1">Total Revenue</h6>
                                    <h3 class="mb-0">₹<?php echo $revenue ? number_format($revenue, 2) : '0.00'; ?></h3>
                                </div>
                                <div class="stat-icon text-info">
                                    <i class="fas fa-rupee-sign"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts and Tables -->
            <div class="row">
                <!-- Recent Orders -->
                <div class="col-xl-8 mb-4">
                    <div class="card border-0 shadow">
                        <div class="card-header bg-white border-0">
                            <h5 class="mb-0">Recent Orders</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Customer</th>
                                            <th>Total</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while($order = $recent_orders->fetch_assoc()): ?>
                                        <tr>
                                            <td>#<?php echo $order['id']; ?></td>
                                            <td><?php echo htmlspecialchars($order['username']); ?></td>
                                            <td>₹<?php echo number_format($order['total'], 2); ?></td>
                                            <td>
                                                <span class="badge-status 
                                                    <?php
                                                    if ($order['status'] == 'confirmed') echo 'bg-success';
                                                    elseif ($order['status'] == 'pending') echo 'bg-warning';
                                                    else echo 'bg-danger';
                                                    ?>">
                                                    <?php echo ucfirst($order['status']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('M d, Y', strtotime($order['order_date'])); ?></td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Top Products -->
                <div class="col-xl-4 mb-4">
                    <div class="card border-0 shadow">
                        <div class="card-header bg-white border-0">
                            <h5 class="mb-0">Top Products</h5>
                        </div>
                        <div class="card-body">
                            <div class="list-group list-group-flush">
                                <?php 
                                $counter = 1;
                                while($product = $top_products->fetch_assoc()): 
                                ?>
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0 border-0">
                                    <div>
                                        <span class="badge bg-primary me-2"><?php echo $counter++; ?></span>
                                        <span><?php echo htmlspecialchars($product['name']); ?></span>
                                    </div>
                                    <span class="badge bg-light text-dark"><?php echo $product['total_sold']; ?> sold</span>
                                </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Toggle sidebar on mobile
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            
            sidebar.classList.toggle('active');
            mainContent.classList.toggle('active');
        }

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const isMobile = window.innerWidth <= 768;
            
            if (isMobile && sidebar.classList.contains('active')) {
                if (!sidebar.contains(event.target) && !event.target.closest('.navbar-toggler')) {
                    sidebar.classList.remove('active');
                    mainContent.classList.remove('active');
                }
            }
        });
    </script>
</body>
</html>