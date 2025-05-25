<?php
require_once '../config.php';
require_once 'includes/functions.php';

// Check if user is admin
if (!isAdmin()) {
    header('Location: login.php');
    exit();
}

// Get statistics
try {
    // Total products
    $stmt = $pdo->query("SELECT COUNT(*) FROM merchandise");
    $totalProducts = $stmt->fetchColumn();
    
    // Low stock products (less than 10)
    $stmt = $pdo->query("SELECT COUNT(*) FROM merchandise WHERE stock < 10");
    $lowStockProducts = $stmt->fetchColumn();
    
    // Recent products
    $stmt = $pdo->query("SELECT * FROM merchandise ORDER BY created_at DESC LIMIT 5");
    $recentProducts = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Error fetching statistics: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aespa Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #34495e;
            --accent: #3498db;
            --success: #2ecc71;
            --warning: #f1c40f;
            --danger: #e74c3c;
            --light: #ecf0f1;
            --dark: #2c3e50;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f6fa;
            color: var(--dark);
        }

        .container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            background: var(--primary);
            color: var(--light);
            padding: 20px;
        }

        .sidebar h2 {
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .nav-menu {
            list-style: none;
        }

        .nav-item {
            margin-bottom: 5px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 10px;
            color: var(--light);
            text-decoration: none;
            border-radius: 5px;
            transition: all 0.3s;
        }

        .nav-link:hover, .nav-link.active {
            background: var(--secondary);
        }

        .nav-link i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        /* Main Content */
        .main {
            flex: 1;
            padding: 20px;
        }

        .header {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #ddd;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .stat-card h3 {
            color: var(--secondary);
            margin-bottom: 10px;
            font-size: 1.1rem;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: bold;
            color: var(--primary);
        }

        .stat-icon {
            font-size: 2.5rem;
            color: var(--accent);
            margin-bottom: 10px;
        }

        .recent-products {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .recent-products h2 {
            margin-bottom: 20px;
            color: var(--primary);
        }

        .products-table {
            width: 100%;
            border-collapse: collapse;
        }

        .products-table th,
        .products-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .products-table th {
            background: var(--light);
            font-weight: 500;
        }

        .products-table tr:last-child td {
            border-bottom: none;
        }

        .product-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 4px;
        }

        .status {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.9em;
        }

        .status.in-stock { background: var(--success); color: white; }
        .status.low-stock { background: var(--warning); color: var(--dark); }
        .status.out-of-stock { background: var(--danger); color: white; }

        .btn {
            display: inline-flex;
            align-items: center;
            padding: 8px 16px;
            border-radius: 4px;
            text-decoration: none;
            color: white;
            background: var(--accent);
            border: none;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 0.9rem;
        }

        .btn:hover {
            opacity: 0.9;
        }

        .btn i {
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <h2>Aespa Admin</h2>
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="index.php" class="nav-link active">
                        <i class="fas fa-tachometer-alt"></i>
                        Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a href="products.php" class="nav-link">
                        <i class="fas fa-box"></i>
                        Products
                    </a>
                </li>
                <li class="nav-item">
                    <a href="add_product.php" class="nav-link">
                        <i class="fas fa-plus"></i>
                        Add Product
                    </a>
                </li>
                <li class="nav-item">
                    <a href="logout.php" class="nav-link">
                        <i class="fas fa-sign-out-alt"></i>
                        Logout
                    </a>
                </li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main">
            <div class="header">
                <h1>Dashboard</h1>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <i class="fas fa-box stat-icon"></i>
                    <h3>Total Products</h3>
                    <div class="stat-value"><?php echo number_format($totalProducts); ?></div>
                </div>

                <div class="stat-card">
                    <i class="fas fa-exclamation-triangle stat-icon"></i>
                    <h3>Low Stock Products</h3>
                    <div class="stat-value"><?php echo number_format($lowStockProducts); ?></div>
                </div>
            </div>

            <div class="recent-products">
                <h2>Recent Products</h2>
                <table class="products-table">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentProducts as $product): ?>
                            <tr>
                                <td>
                                    <img src="../<?php echo htmlspecialchars($product['image']); ?>" 
                                         alt="<?php echo htmlspecialchars($product['name']); ?>"
                                         class="product-image"
                                         onerror="this.src='https://via.placeholder.com/50x50?text=No+Image'">
                                </td>
                                <td><?php echo htmlspecialchars($product['name']); ?></td>
                                <td>$<?php echo number_format($product['price'], 2); ?></td>
                                <td>
                                    <?php
                                    $status = getStockStatus($product['stock']);
                                    echo '<span class="status ' . $status['class'] . '">' . $status['text'] . '</span>';
                                    ?>
                                </td>
                                <td>
                                    <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="btn">
                                        <i class="fas fa-edit"></i>
                                        Edit
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html> 