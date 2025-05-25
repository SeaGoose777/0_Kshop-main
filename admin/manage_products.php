<?php
require_once '../config.php';

// Check if user is admin
if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../login.php');
    exit;
}

$message = '';

// Handle product deletion
if (isset($_POST['delete_product'])) {
    $product_id = $_POST['product_id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM merchandise WHERE id = ?");
        $stmt->execute([$product_id]);
        $message = "Product deleted successfully!";
    } catch (PDOException $e) {
        $message = "Error deleting product: " . $e->getMessage();
    }
}

// Handle stock update
if (isset($_POST['update_stock'])) {
    $product_id = $_POST['product_id'];
    $new_stock = $_POST['new_stock'];
    try {
        $stmt = $pdo->prepare("UPDATE merchandise SET stock = ? WHERE id = ?");
        $stmt->execute([$new_stock, $product_id]);
        $message = "Stock updated successfully!";
    } catch (PDOException $e) {
        $message = "Error updating stock: " . $e->getMessage();
    }
}

// Fetch all products
try {
    $stmt = $pdo->query("SELECT * FROM merchandise ORDER BY created_at DESC");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $products = [];
    $message = "Error fetching products: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - Admin Panel</title>
    <link rel="stylesheet" href="../styles/base.css">
    <link rel="stylesheet" href="../styles/navbar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            padding: 20px;
        }
        .product-card {
            background: rgba(26, 26, 26, 0.95);
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }
        .product-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        .stock-control {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 15px;
        }
        .stock-control input {
            width: 80px;
            padding: 5px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 5px;
            background: rgba(0, 0, 0, 0.2);
            color: var(--text-color);
        }
        .action-buttons {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
        .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }
        .btn-update {
            background: var(--primary-color);
            color: white;
        }
        .btn-delete {
            background: #ff4444;
            color: white;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        .message {
            background: rgba(76, 175, 80, 0.1);
            color: #4CAF50;
            padding: 15px;
            border-radius: 5px;
            margin: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-brand">
            <a href="../index.php">
                <img src="../pic/logo.png" alt="aespa" class="logo">
            </a>
        </div>
        <div class="nav-links">
            <a href="../index.php">Home</a>
            <a href="manage_products.php" class="active">Manage Products</a>
            <a href="add_product.php">Add Product</a>
        </div>
    </nav>

    <main style="padding: 120px 20px 40px;">
        <h1 style="text-align: center; margin-bottom: 30px;">Manage Products</h1>
        
        <?php if ($message): ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <div class="product-grid">
            <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <img src="../<?php echo htmlspecialchars($product['image']); ?>" 
                         alt="<?php echo htmlspecialchars($product['name']); ?>" 
                         class="product-image">
                    <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                    <p><?php echo htmlspecialchars($product['description']); ?></p>
                    <p>Price: $<?php echo number_format($product['price'], 2); ?></p>
                    
                    <form method="POST" class="stock-control">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        <label>Stock:</label>
                        <input type="number" name="new_stock" value="<?php echo $product['stock']; ?>" min="0">
                        <button type="submit" name="update_stock" class="btn btn-update">Update</button>
                    </form>

                    <div class="action-buttons">
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <button type="submit" name="delete_product" class="btn btn-delete" 
                                    onclick="return confirm('Are you sure you want to delete this product?')">
                                Delete Product
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>
</body>
</html> 