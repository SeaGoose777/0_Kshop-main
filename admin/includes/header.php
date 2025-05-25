<?php
require_once 'functions.php';
require_once '../config.php';

// Check if user is admin
if (!isAdmin()) {
    header('Location: login.php');
    exit;
}

// Get current page for active state
$currentPage = basename($_SERVER['PHP_SELF']);

// Get admin info
$adminInfo = getAdminInfo($pdo);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Jovelen</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #34495e;
            --accent-color: #3498db;
            --text-color: #2c3e50;
            --light-text: #ecf0f1;
            --border-color: #bdc3c7;
            --success-color: #2ecc71;
            --warning-color: #f1c40f;
            --danger-color: #e74c3c;
            --sidebar-width: 250px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f6fa;
            color: var(--text-color);
        }

        .admin-container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styles */
        .sidebar {
            width: var(--sidebar-width);
            background-color: var(--primary-color);
            color: var(--light-text);
            padding: 20px 0;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            transition: all 0.3s ease;
        }

        .sidebar-header {
            padding: 0 20px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 20px;
        }

        .sidebar-header h2 {
            font-size: 1.5rem;
            margin-bottom: 5px;
        }

        .sidebar-header p {
            font-size: 0.9rem;
            opacity: 0.8;
        }

        .nav-links {
            list-style: none;
        }

        .nav-links li {
            margin-bottom: 5px;
        }

        .nav-links a {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: var(--light-text);
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .nav-links a:hover {
            background-color: var(--secondary-color);
        }

        .nav-links a.active {
            background-color: var(--accent-color);
        }

        .nav-links i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        /* Main Content Styles */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            padding: 20px;
            transition: all 0.3s ease;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--border-color);
        }

        .page-title {
            font-size: 1.8rem;
            color: var(--primary-color);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-info i {
            font-size: 1.2rem;
            color: var(--accent-color);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                z-index: 1000;
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .toggle-sidebar {
                display: block;
            }
        }

        /* Card Styles */
        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
        }

        /* Button Styles */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .btn-primary {
            background-color: var(--accent-color);
            color: white;
        }

        .btn-primary:hover {
            background-color: #2980b9;
        }

        .btn-danger {
            background-color: var(--danger-color);
            color: white;
        }

        .btn-danger:hover {
            background-color: #c0392b;
        }

        /* Table Styles */
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .table th,
        .table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }

        .table tr:hover {
            background-color: #f8f9fa;
        }

        /* Form Styles */
        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            font-size: 0.9rem;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--accent-color);
        }

        /* Alert Styles */
        .alert {
            padding: 12px 20px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>Jovelen Admin</h2>
                <p>Welcome, <?php echo h($adminInfo['username'] ?? 'Admin'); ?></p>
            </div>
            <ul class="nav-links">
                <li>
                    <a href="index.php" class="<?php echo $currentPage === 'index.php' ? 'active' : ''; ?>">
                        <i class="fas fa-tachometer-alt"></i>
                        Dashboard
                    </a>
                </li>
                <li>
                    <a href="products.php" class="<?php echo $currentPage === 'products.php' ? 'active' : ''; ?>">
                        <i class="fas fa-box"></i>
                        Products
                    </a>
                </li>
                <li>
                    <a href="add_product.php" class="<?php echo $currentPage === 'add_product.php' ? 'active' : ''; ?>">
                        <i class="fas fa-plus"></i>
                        Add Product
                    </a>
                </li>
                <li>
                    <a href="manage_products.php" class="<?php echo $currentPage === 'manage_products.php' ? 'active' : ''; ?>">
                        <i class="fas fa-cog"></i>
                        Manage Products
                    </a>
                </li>
                <li>
                    <a href="logout.php">
                        <i class="fas fa-sign-out-alt"></i>
                        Logout
                    </a>
                </li>
            </ul>
        </aside>
        <main class="main-content">
            <div class="page-header">
                <h1 class="page-title">
                    <?php
                    switch ($currentPage) {
                        case 'index.php':
                            echo '<i class="fas fa-tachometer-alt"></i> Dashboard';
                            break;
                        case 'products.php':
                            echo '<i class="fas fa-box"></i> Products';
                            break;
                        case 'add_product.php':
                            echo '<i class="fas fa-plus"></i> Add Product';
                            break;
                        case 'manage_products.php':
                            echo '<i class="fas fa-cog"></i> Manage Products';
                            break;
                        default:
                            echo 'Admin Panel';
                    }
                    ?>
                </h1>
                <div class="user-info">
                    <i class="fas fa-user-circle"></i>
                    <span><?php echo h($adminInfo['username'] ?? 'Admin'); ?></span>
                </div>
            </div>
        </main>
    </div>
</body>
</html> 