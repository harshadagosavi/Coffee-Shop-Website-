<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

include '../config.php';

if (isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $image = $_POST['image'];

    $sql = "INSERT INTO products (name, description, price, category, image) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdss", $name, $description, $price, $category, $image);
    $stmt->execute();
    header("Location: manage_products.php");
    exit();
}

if (isset($_POST['update_product'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $image = $_POST['image'];

    $sql = "UPDATE products SET name=?, description=?, price=?, category=?, image=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdssi", $name, $description, $price, $category, $image, $id);
    $stmt->execute();
    header("Location: manage_products.php");
    exit();
}

if (isset($_POST['delete_product'])) {
    $id = $_POST['id'];
    $sql = "DELETE FROM products WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: manage_products.php");
    exit();
}

$products = $conn->query("SELECT * FROM products ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - Coffee Shop Admin</title>
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
        
        .product-img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 8px;
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
            <span class="navbar-brand ms-2">Product Management</span>
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
                        <a class="nav-link active" href="manage_products.php">
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
                            <h2 class="fw-bold">Product Management</h2>
                            <p class="text-muted mb-0">Add, edit, or remove products from your store</p>
                        </div>
                        <div class="d-none d-md-block">
                            <span class="badge bg-primary p-2"><?php echo date('F j, Y'); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add Product Button -->
            <div class="mb-4">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                    <i class="fas fa-plus me-2"></i>Add New Product
                </button>
            </div>

            <!-- Products Table -->
            <div class="card border-0 shadow">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0">All Products</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Price</th>
                                    <th>Category</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($product = $products->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $product['id']; ?></td>
                                    <td><img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" class="product-img"></td>
                                    <td><strong><?php echo htmlspecialchars($product['name']); ?></strong></td>
                                    <td><?php echo substr(htmlspecialchars($product['description']), 0, 50) . '...'; ?></td>
                                    <td><span class="badge bg-success">₹<?php echo number_format($product['price'], 2); ?></span></td>
                                    <td><span class="badge bg-info"><?php echo ucfirst($product['category']); ?></span></td>
                                    <td>
                                        <button class="btn btn-warning btn-sm" onclick="editProduct(<?php echo $product['id']; ?>, '<?php echo addslashes($product['name']); ?>', '<?php echo addslashes($product['description']); ?>', <?php echo $product['price']; ?>, '<?php echo $product['category']; ?>', '<?php echo $product['image']; ?>')">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this product?')">
                                            <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                                            <button type="submit" name="delete_product" class="btn btn-danger btn-sm">
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

    <!-- Add Product Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Product Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="price" class="form-label">Price (₹)</label>
                            <input type="number" class="form-control" id="price" name="price" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="category" class="form-label">Category</label>
                            <select class="form-select" id="category" name="category" required>
                                <option value="coffee">Coffee</option>
                                <option value="dessert">Dessert</option>
                                <option value="cake">Cake</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="image" class="form-label">Image URL</label>
                            <input type="text" class="form-control" id="image" name="image" placeholder="https://example.com/image.jpg" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="add_product" class="btn btn-primary">Add Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <div class="modal fade" id="editProductModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" id="edit_id" name="id">
                        <div class="mb-3">
                            <label for="edit_name" class="form-label">Product Name</label>
                            <input type="text" class="form-control" id="edit_name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_description" class="form-label">Description</label>
                            <textarea class="form-control" id="edit_description" name="description" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="edit_price" class="form-label">Price (₹)</label>
                            <input type="number" class="form-control" id="edit_price" name="price" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_category" class="form-label">Category</label>
                            <select class="form-select" id="edit_category" name="category" required>
                                <option value="coffee">Coffee</option>
                                <option value="dessert">Dessert</option>
                                <option value="cake">Cake</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit_image" class="form-label">Image URL</label>
                            <input type="text" class="form-control" id="edit_image" name="image" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="update_product" class="btn btn-primary">Update Product</button>
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

        function editProduct(id, name, description, price, category, image) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_description').value = description;
            document.getElementById('edit_price').value = price;
            document.getElementById('edit_category').value = category;
            document.getElementById('edit_image').value = image;
            new bootstrap.Modal(document.getElementById('editProductModal')).show();
        }
    </script>
</body>
</html>