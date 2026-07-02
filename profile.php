<?php include 'includes/header.php'; ?>
<?php include 'config.php'; ?>

<?php
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// IMPORTANT: Mark all user's messages as 'read' when they view their profile
$mark_read_sql = "UPDATE messages SET status = 'read' WHERE user_id = ? AND status = 'unread'";
$mark_read_stmt = $conn->prepare($mark_read_sql);
$mark_read_stmt->bind_param("i", $user_id);
$mark_read_stmt->execute();

// Get user messages with replies
$sql = "SELECT m.*, 
        (SELECT COUNT(*) FROM message_replies WHERE message_id = m.id) as reply_count 
        FROM messages m 
        WHERE m.user_id = ? 
        ORDER BY m.created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$messages = $stmt->get_result();

// Get orders
$orders_sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC";
$orders_stmt = $conn->prepare($orders_sql);
$orders_stmt->bind_param("i", $user_id);
$orders_stmt->execute();
$orders = $orders_stmt->get_result();
?>

<style>
    .profile-container {
        max-width: 1200px;
        margin: 40px auto;
        padding: 0 20px;
    }
    
    .profile-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 30px;
        border-radius: 15px;
        margin-bottom: 30px;
    }
    
    .tab-container {
        display: flex;
        gap: 10px;
        margin-bottom: 30px;
        border-bottom: 2px solid #e0e0e0;
        padding-bottom: 10px;
    }
    
    .tab-btn {
        background: none;
        border: none;
        padding: 10px 20px;
        font-size: 1rem;
        font-weight: 500;
        color: #666;
        cursor: pointer;
        border-radius: 8px;
        transition: all 0.3s;
    }
    
    .tab-btn:hover {
        background: #f0f0f0;
    }
    
    .tab-btn.active {
        background: #667eea;
        color: white;
    }
    
    .message-card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        margin-bottom: 20px;
        border-left: 5px solid #667eea;
    }
    
    .message-card.replied {
        border-left-color: #28a745;
    }
    
    .message-card.read {
        border-left-color: #6c757d;
    }
    
    .message-header {
        padding: 15px 20px;
        background: #f8f9fa;
        border-radius: 10px 10px 0 0;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .message-body {
        padding: 20px;
        display: none;
    }
    
    .message-body.show {
        display: block;
    }
    
    .message-subject {
        font-weight: 600;
        color: #333;
    }
    
    .message-date {
        color: #999;
        font-size: 0.9rem;
    }
    
    .reply-item {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 15px;
        border-left: 3px solid #28a745;
    }
    
    .reply-item.admin {
        border-left-color: #667eea;
    }
    
    .reply-header {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
        color: #666;
        font-size: 0.9rem;
    }
    
    .reply-name {
        font-weight: 600;
        color: #28a745;
    }
    
    .reply-name.admin {
        color: #667eea;
    }
    
    .badge-status {
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 500;
    }
    
    .badge-success {
        background: #d4edda;
        color: #155724;
    }
    
    .badge-warning {
        background: #fff3cd;
        color: #856404;
    }
    
    .badge-secondary {
        background: #e2e3e5;
        color: #383d41;
    }
    
    .badge-info {
        background: #d1ecf1;
        color: #0c5460;
    }
    
    .order-table {
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    }
    
    .order-table th {
        background: #667eea;
        color: white;
        font-weight: 500;
        padding: 15px;
    }
    
    .order-table td {
        padding: 12px 15px;
        border-bottom: 1px solid #e0e0e0;
    }
    
    .order-table tr:hover {
        background: #f8f9fa;
    }
    
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    }
    
    .empty-state i {
        font-size: 4rem;
        color: #ccc;
        margin-bottom: 20px;
    }
    
    .empty-state h4 {
        color: #333;
        margin-bottom: 10px;
    }
    
    .empty-state p {
        color: #999;
        margin-bottom: 20px;
    }
    
    .btn {
        padding: 8px 20px;
        border-radius: 5px;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s;
    }
    
    .btn-primary {
        background: #667eea;
        color: white;
        border: none;
    }
    
    .btn-primary:hover {
        background: #5a67d8;
    }
    
    .btn-sm {
        padding: 5px 12px;
        font-size: 0.9rem;
    }
    
    .btn-outline-primary {
        border: 1px solid #667eea;
        color: #667eea;
        background: none;
    }
    
    .btn-outline-primary:hover {
        background: #667eea;
        color: white;
    }
    
    .modal-content {
        border-radius: 15px;
    }
    
    .modal-header {
        background: #667eea;
        color: white;
        border-radius: 15px 15px 0 0;
    }
    
    .modal-header .btn-close {
        color: white;
    }
    
    .unread-count {
        background: #dc3545;
        color: white;
        border-radius: 50%;
        padding: 2px 8px;
        font-size: 0.8rem;
        margin-left: 8px;
    }
</style>

<div class="profile-container">
    <!-- Profile Header -->
    <div class="profile-header">
        <h1 class="mb-2">My Profile</h1>
        <p class="mb-0">Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
    </div>
    
    <!-- Tabs with Message Count -->
    <div class="tab-container">
        <button class="tab-btn active" onclick="showTab('orders')">
            <i class="fas fa-shopping-bag me-2"></i>Orders
        </button>
        <button class="tab-btn" onclick="showTab('messages')">
            <i class="fas fa-envelope me-2"></i>Messages & Feedback
            <?php
            // Count total messages
            $total_msgs = $messages->num_rows;
            if($total_msgs > 0):
            ?>
            <span class="badge bg-primary ms-2"><?php echo $total_msgs; ?></span>
            <?php endif; ?>
        </button>
    </div>
    
    <!-- Orders Tab -->
    <div id="orders-tab">
        <h3 class="mb-3">Order History</h3>
        
        <?php if($orders->num_rows > 0): ?>
            <div class="order-table table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Payment</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($order = $orders->fetch_assoc()): ?>
                        <tr>
                            <td><strong>#<?php echo $order['id']; ?></strong></td>
                            <td><?php echo date('d-m-Y', strtotime($order['order_date'])); ?></td>
                            <td>₹<?php echo number_format($order['total'], 2); ?></td>
                            <td><?php echo ucfirst($order['payment_method']); ?></td>
                            <td>
                                <span class="badge-status 
                                    <?php 
                                    if($order['status'] == 'confirmed') echo 'badge-success';
                                    elseif($order['status'] == 'pending') echo 'badge-warning';
                                    else echo 'badge-secondary';
                                    ?>">
                                    <?php echo ucfirst($order['status']); ?>
                                </span>
                            </td>
                            <td>
                                <a href="download_invoice.php?order_id=<?php echo $order['id']; ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-download me-1"></i>Download Invoice
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-shopping-bag"></i>
                <h4>No Orders Yet</h4>
                <p>You haven't placed any orders yet.</p>
                <a href="menu.php" class="btn btn-primary">Browse Menu</a>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Messages Tab (Initially Hidden) -->
    <div id="messages-tab" style="display: none;">
        <h3 class="mb-3">Messages & Feedback</h3>
        
        <?php 
        // Reset messages pointer
        $messages->data_seek(0);
        if($messages->num_rows > 0): 
        ?>
            <div class="messages-list">
                <?php while($msg = $messages->fetch_assoc()): 
                    // Get replies for this message
                    $reply_sql = "SELECT r.*, u.username FROM message_replies r 
                                 LEFT JOIN users u ON r.admin_id = u.id 
                                 WHERE r.message_id = ? ORDER BY r.created_at ASC";
                    $reply_stmt = $conn->prepare($reply_sql);
                    $reply_stmt->bind_param("i", $msg['id']);
                    $reply_stmt->execute();
                    $replies = $reply_stmt->get_result();
                    
                    // Determine status class
                    $statusClass = '';
                    $statusText = ucfirst($msg['status']);
                    
                    if($msg['status'] == 'replied') {
                        $statusClass = 'badge-success';
                    } elseif($msg['status'] == 'unread') {
                        $statusClass = 'badge-warning';
                    } else {
                        $statusClass = 'badge-info';
                    }
                ?>
                <div class="message-card <?php echo $msg['status'] == 'replied' ? 'replied' : ($msg['status'] == 'read' ? 'read' : ''); ?>">
                    <div class="message-header" onclick="toggleMessage(<?php echo $msg['id']; ?>)">
                        <div>
                            <span class="message-subject">
                                <i class="fas fa-envelope me-2"></i>
                                <?php echo htmlspecialchars($msg['subject'] ?: 'No Subject'); ?>
                            </span>
                            <?php if($replies->num_rows > 0): ?>
                                <span class="badge-status badge-success ms-2">
                                    <i class="fas fa-reply me-1"></i><?php echo $replies->num_rows; ?> reply(ies)
                                </span>
                            <?php endif; ?>
                        </div>
                        <div>
                            <span class="message-date me-3"><?php echo date('d M Y', strtotime($msg['created_at'])); ?></span>
                            <span class="badge-status <?php echo $statusClass; ?>">
                                <?php echo $statusText; ?>
                            </span>
                            <i class="fas fa-chevron-down ms-3"></i>
                        </div>
                    </div>
                    
                    <div class="message-body" id="msg-body-<?php echo $msg['id']; ?>">
                        <!-- Original Message -->
                        <div class="mb-4">
                            <h6 class="text-muted mb-2">Your Message:</h6>
                            <div class="p-3 bg-light rounded">
                                <?php echo nl2br(htmlspecialchars($msg['message'])); ?>
                            </div>
                        </div>
                        
                        <!-- Replies Section -->
                        <?php if($replies->num_rows > 0): ?>
                            <div class="mb-3">
                                <h6 class="text-muted mb-2">Admin Replies:</h6>
                                <?php while($reply = $replies->fetch_assoc()): ?>
                                    <div class="reply-item admin">
                                        <div class="reply-header">
                                            <span class="reply-name admin">
                                                <i class="fas fa-user-shield me-1"></i>Coffee Shop Admin
                                            </span>
                                            <span class="text-muted">
                                                <i class="far fa-clock me-1"></i>
                                                <?php echo date('d M Y h:i A', strtotime($reply['created_at'])); ?>
                                            </span>
                                        </div>
                                        <p class="mb-0"><?php echo nl2br(htmlspecialchars($reply['reply_text'])); ?></p>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Message Status -->
                        <div class="text-muted small mt-2">
                            <i class="fas fa-info-circle me-1"></i>
                            Status: <strong><?php echo ucfirst($msg['status']); ?></strong>
                            <?php if($msg['status'] == 'read'): ?>
                                <span class="ms-2">✓ Seen on <?php echo date('d M Y', strtotime($msg['updated_at'] ?? $msg['created_at'])); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-envelope"></i>
                <h4>No Messages Yet</h4>
                <p>You haven't sent any messages to us yet.</p>
                <a href="contact.php" class="btn btn-primary">Contact Us</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    // Toggle message body
    function toggleMessage(id) {
        const element = document.getElementById('msg-body-' + id);
        element.classList.toggle('show');
    }
    
    // Show selected tab
    function showTab(tab) {
        const ordersTab = document.getElementById('orders-tab');
        const messagesTab = document.getElementById('messages-tab');
        const tabs = document.querySelectorAll('.tab-btn');
        
        if(tab === 'orders') {
            ordersTab.style.display = 'block';
            messagesTab.style.display = 'none';
            tabs[0].classList.add('active');
            tabs[1].classList.remove('active');
        } else {
            ordersTab.style.display = 'none';
            messagesTab.style.display = 'block';
            tabs[0].classList.remove('active');
            tabs[1].classList.add('active');
        }
    }
    
    // Check URL hash for tab
    if(window.location.hash === '#messages') {
        showTab('messages');
    }
</script>

<?php include 'includes/footer.php'; ?>