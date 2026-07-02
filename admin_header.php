<?php
// Add at the top of admin_header.php
include '../config.php';
$unread_count = $conn->query("SELECT COUNT(*) as count FROM messages WHERE status='unread'")->fetch_assoc()['count'];
?>

<!-- In the navbar, add this before Logout -->
<li class="nav-item position-relative">
    <a class="nav-link" href="messages.php">
        <i class="fas fa-envelope me-1"></i>Messages
        <?php if($unread_count > 0): ?>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                <?php echo $unread_count; ?>
            </span>
        <?php endif; ?>
    </a>
</li>