<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

include '../config.php';

// Handle order status update
if (isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $order_id);
    $stmt->execute();
    $message = "Order status updated successfully!";
}

// Handle order deletion
if (isset($_POST['delete_order'])) {
    $order_id = $_POST['id'];

    // Delete order items first
    $stmt = $conn->prepare("DELETE FROM order_items WHERE order_id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();

    // Delete order
    $stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $message = "Order deleted successfully!";
}

// Get all orders with user details
$orders = $conn->query("
    SELECT o.*, u.username, u.email
    FROM orders o
    JOIN users u ON o.user_id = u.id
    ORDER BY o.order_date DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders - Coffee Shop Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
        }
        
        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        
        .card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border: none;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
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
            <span class="navbar-brand ms-2">Order Management</span>
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
                        <a class="nav-link" href="dashboard.php">
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
                        <a class="nav-link active" href="manage_orders.php">
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
                    <li class="nav-item mb-2">
                        <a class="nav-link" href="messages.php">
                            <i class="fas fa-envelope"></i>
                            <span>Messages</span>
                            <?php
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
            <!-- Header -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="fw-bold">Order Management</h2>
                            <p class="text-muted mb-0">Track and manage customer orders</p>
                        </div>
                        <div class="d-none d-md-block">
                            <span class="badge bg-primary p-2"><?php echo date('F j, Y'); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (isset($message)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i><?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Orders Table -->
            <div class="card border-0 shadow">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0">All Orders</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Total</th>
                                    <th>Payment</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($order = $orders->fetch_assoc()): ?>
                                <tr>
                                    <td><strong>#<?php echo $order['id']; ?></strong></td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($order['username']); ?></strong><br>
                                        <small class="text-muted"><?php echo htmlspecialchars($order['email']); ?></small>
                                    </td>
                                    <td><span class="badge bg-success">₹<?php echo number_format($order['total'], 2); ?></span></td>
                                    <td><?php echo ucfirst($order['payment_method']); ?></td>
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
                                    <td><?php echo date('d M Y H:i', strtotime($order['order_date'])); ?></td>
                                    <td>
                                        <button class="btn btn-warning btn-sm" onclick="editOrder(<?php echo $order['id']; ?>, '<?php echo $order['status']; ?>')">
                                            <i class="fas fa-edit"></i> Status
                                        </button>
                                        <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this order?')">
                                            <input type="hidden" name="id" value="<?php echo $order['id']; ?>">
                                            <button type="submit" name="delete_order" class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Order Status Modal -->
    <div class="modal fade" id="editOrderModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Order Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" id="edit_order_id" name="order_id">
                        <div class="mb-3">
                            <label for="edit_status" class="form-label">Order Status</label>
                            <select class="form-control" id="edit_status" name="status" required>
                                <option value="pending">Pending</option>
                                <option value="confirmed">Confirmed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="update_status" class="btn btn-primary">Update Status</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            sidebar.classList.toggle('active');
            mainContent.classList.toggle('active');
        }

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

        function editOrder(id, status) {
            document.getElementById('edit_order_id').value = id;
            document.getElementById('edit_status').value = status;
            new bootstrap.Modal(document.getElementById('editOrderModal')).show();
        }
    </script>
</body>
</html>