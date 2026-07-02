<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

include '../config.php';

// Handle reply submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reply_message'])) {
    $message_id = $_POST['message_id'];
    $reply_text = mysqli_real_escape_string($conn, $_POST['reply_text']);
    
    // Get admin ID
    $admin_id = $_SESSION['admin_id'] ?? null;
    
    // Insert reply
    $sql = "INSERT INTO message_replies (message_id, admin_id, reply_text) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $message_id, $admin_id, $reply_text);
    
    if ($stmt->execute()) {
        // Update message status
        $sql = "UPDATE messages SET status = 'replied' WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $message_id);
        $stmt->execute();
        
        header("Location: messages.php?replied=1");
        exit();
    }
}

// Mark as read
if (isset($_GET['mark_read'])) {
    $message_id = $_GET['mark_read'];
    $sql = "UPDATE messages SET status = 'read' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $message_id);
    $stmt->execute();
    header("Location: messages.php");
    exit();
}

// Delete message
if (isset($_POST['delete_message'])) {
    $message_id = $_POST['message_id'];
    $sql = "DELETE FROM messages WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $message_id);
    $stmt->execute();
    header("Location: messages.php?deleted=1");
    exit();
}

// Get all messages with reply counts
$sql = "SELECT m.*, 
        COUNT(r.id) as reply_count,
        u.username as user_name 
        FROM messages m 
        LEFT JOIN users u ON m.user_id = u.id
        LEFT JOIN message_replies r ON m.id = r.message_id
        GROUP BY m.id 
        ORDER BY 
            CASE m.status 
                WHEN 'unread' THEN 1 
                WHEN 'read' THEN 2 
                ELSE 3 
            END, 
            m.created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Messages - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
        
        .message-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            border-left: 5px solid #6c757d;
        }
        
        .message-card.unread {
            border-left-color: #dc3545;
            background: #fff8f8;
        }
        
        .message-card.replied {
            border-left-color: #28a745;
        }
        
        .message-header {
            padding: 15px 20px;
            background: #f8f9fa;
            border-radius: 10px 10px 0 0;
            cursor: pointer;
        }
        
        .message-body {
            padding: 20px;
            display: none;
        }
        
        .message-body.show {
            display: block;
        }
        
        .reply-box {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
        }
        
        .reply-item {
            background: white;
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 10px;
            border-left: 3px solid var(--primary);
        }
        
        .badge-status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
        }
        
        .stats-card {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 25px;
        }
        
        .stat-card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: all 0.3s;
            overflow: hidden;
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
            <span class="navbar-brand ms-2">Messages</span>
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
                    <li class="nav-item mb-2">
                        <a class="nav-link active" href="messages.php">
                            <i class="fas fa-envelope"></i>
                            <span>Messages</span>
                            <?php
                            $unread_count = $conn->query("SELECT COUNT(*) as count FROM messages WHERE status='unread'")->fetch_assoc()['count'];
                            if($unread_count > 0):
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
                            <h2 class="fw-bold">Customer Messages</h2>
                            <p class="text-muted mb-0">Manage and reply to customer inquiries</p>
                        </div>
                        <div class="d-none d-md-block">
                            <span class="badge bg-primary p-2"><?php echo date('F j, Y'); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats -->
            <div class="row stats-card mb-4">
                <?php
                $unread = $conn->query("SELECT COUNT(*) as count FROM messages WHERE status='unread'")->fetch_assoc();
                $total = $conn->query("SELECT COUNT(*) as count FROM messages")->fetch_assoc();
                $replied = $conn->query("SELECT COUNT(*) as count FROM messages WHERE status='replied'")->fetch_assoc();
                ?>
                <div class="col-md-4">
                    <h3><?php echo $unread['count']; ?></h3>
                    <p>Unread Messages</p>
                </div>
                <div class="col-md-4">
                    <h3><?php echo $replied['count']; ?></h3>
                    <p>Replied</p>
                </div>
                <div class="col-md-4">
                    <h3><?php echo $total['count']; ?></h3>
                    <p>Total Messages</p>
                </div>
            </div>

            <!-- Success/Error Messages -->
            <?php if(isset($_GET['replied'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>Reply sent successfully!
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if(isset($_GET['deleted'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-trash me-2"></i>Message deleted successfully!
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Messages List -->
            <div class="row">
                <div class="col-12">
                    <?php if($result && $result->num_rows > 0): ?>
                        <?php while($msg = $result->fetch_assoc()): 
                            $statusClass = $msg['status'] == 'unread' ? 'unread' : ($msg['status'] == 'replied' ? 'replied' : '');
                        ?>
                        <div class="message-card <?php echo $statusClass; ?>" id="message-<?php echo $msg['id']; ?>">
                            <div class="message-header" onclick="toggleMessage(<?php echo $msg['id']; ?>)">
                                <div class="row align-items-center">
                                    <div class="col-md-4">
                                        <strong>
                                            <i class="fas fa-user me-2"></i>
                                            <?php echo htmlspecialchars($msg['name']); ?>
                                            <?php if($msg['user_name']): ?>
                                                <small class="text-muted">(@<?php echo $msg['user_name']; ?>)</small>
                                            <?php endif; ?>
                                        </strong>
                                    </div>
                                    <div class="col-md-3">
                                        <i class="fas fa-envelope me-2"></i>
                                        <?php echo htmlspecialchars($msg['email']); ?>
                                    </div>
                                    <div class="col-md-2">
                                        <span class="badge-status bg-<?php 
                                            echo $msg['status'] == 'unread' ? 'danger' : ($msg['status'] == 'replied' ? 'success' : 'secondary'); 
                                        ?>">
                                            <?php echo ucfirst($msg['status']); ?>
                                        </span>
                                    </div>
                                    <div class="col-md-2">
                                        <small><?php echo date('d M Y', strtotime($msg['created_at'])); ?></small>
                                    </div>
                                    <div class="col-md-1 text-end">
                                        <i class="fas fa-chevron-down"></i>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="message-body" id="msg-body-<?php echo $msg['id']; ?>">
                                <div class="mb-3">
                                    <h6>Subject: <?php echo htmlspecialchars($msg['subject'] ?: 'No Subject'); ?></h6>
                                    <div class="p-3 bg-light rounded">
                                        <?php echo nl2br(htmlspecialchars($msg['message'])); ?>
                                    </div>
                                </div>
                                
                                <!-- Previous Replies -->
                                <?php
                                $reply_sql = "SELECT r.*, u.username FROM message_replies r 
                                             LEFT JOIN users u ON r.admin_id = u.id 
                                             WHERE r.message_id = ? ORDER BY r.created_at ASC";
                                $reply_stmt = $conn->prepare($reply_sql);
                                $reply_stmt->bind_param("i", $msg['id']);
                                $reply_stmt->execute();
                                $replies = $reply_stmt->get_result();
                                
                                if($replies->num_rows > 0):
                                ?>
                                <div class="reply-box">
                                    <h6><i class="fas fa-reply me-2"></i>Previous Replies:</h6>
                                    <?php while($reply = $replies->fetch_assoc()): ?>
                                        <div class="reply-item">
                                            <strong>Admin <?php echo htmlspecialchars($reply['username'] ?: 'Admin'); ?>:</strong>
                                            <p class="mb-1"><?php echo nl2br(htmlspecialchars($reply['reply_text'])); ?></p>
                                            <small class="text-muted"><?php echo date('d M Y h:i A', strtotime($reply['created_at'])); ?></small>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                                <?php endif; ?>
                                
                                <!-- Reply Form -->
                                <div class="row mt-3">
                                    <div class="col-md-8">
                                        <form method="POST" action="">
                                            <input type="hidden" name="message_id" value="<?php echo $msg['id']; ?>">
                                            <div class="input-group">
                                                <textarea name="reply_text" class="form-control" rows="2" placeholder="Write your reply..." required></textarea>
                                                <button type="submit" name="reply_message" class="btn btn-primary">
                                                    <i class="fas fa-paper-plane"></i> Send
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <?php if($msg['status'] == 'unread'): ?>
                                            <a href="?mark_read=<?php echo $msg['id']; ?>" class="btn btn-sm btn-secondary">
                                                <i class="fas fa-check"></i> Mark as Read
                                            </a>
                                        <?php endif; ?>
                                        <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this message?')">
                                            <input type="hidden" name="message_id" value="<?php echo $msg['id']; ?>">
                                            <button type="submit" name="delete_message" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                            <h4>No Messages Yet</h4>
                            <p class="text-muted">When customers contact you, their messages will appear here.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
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

        // Toggle message body
        function toggleMessage(id) {
            document.getElementById('msg-body-' + id).classList.toggle('show');
        }
        
        // Auto-expand message if URL has hash
        if(window.location.hash) {
            const id = window.location.hash.replace('#message-', '');
            setTimeout(() => {
                const element = document.getElementById('msg-body-' + id);
                if(element) {
                    element.classList.add('show');
                    element.scrollIntoView({ behavior: 'smooth' });
                }
            }, 500);
        }
    </script>
</body>
</html>