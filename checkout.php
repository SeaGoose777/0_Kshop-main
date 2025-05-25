<?php
session_start();
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Handle checkout process
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();

        // Get cart items
        $stmt = $pdo->prepare("
            SELECT c.*, m.name, m.price, m.stock 
            FROM cart c 
            JOIN merchandise m ON c.product_id = m.id 
            WHERE c.user_id = ?
        ");
        $stmt->execute([$user_id]);
        $cart_items = $stmt->fetchAll();

        if (empty($cart_items)) {
            throw new Exception("Your cart is empty!");
        }

        // Check stock availability
        foreach ($cart_items as $item) {
            if ($item['quantity'] > $item['stock']) {
                throw new Exception("Insufficient stock for {$item['name']}. Available: {$item['stock']}");
            }
        }

        // Calculate total
        $total = 0;
        foreach ($cart_items as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        // Create order
        $stmt = $pdo->prepare("
            INSERT INTO orders (user_id, total_amount, status) 
            VALUES (?, ?, 'completed')
        ");
        $stmt->execute([$user_id, $total]);
        $order_id = $pdo->lastInsertId();

        // Create order items and update stock
        foreach ($cart_items as $item) {
            // Add to order items
            $stmt = $pdo->prepare("
                INSERT INTO order_items (order_id, product_id, quantity, price) 
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([
                $order_id,
                $item['product_id'],
                $item['quantity'],
                $item['price']
            ]);

            // Update stock
            $stmt = $pdo->prepare("
                UPDATE merchandise 
                SET stock = stock - ? 
                WHERE id = ?
            ");
            $stmt->execute([$item['quantity'], $item['product_id']]);
        }

        // Clear cart
        $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
        $stmt->execute([$user_id]);

        $pdo->commit();
        $success = "Order completed successfully!";
        
        // Redirect to merch page after successful checkout
        header("Location: merch.php?success=1");
        exit();

    } catch (Exception $e) {
        $pdo->rollBack();
        $error = $e->getMessage();
    }
}

// Get cart items for display
$stmt = $pdo->prepare("
    SELECT c.*, m.name, m.price, m.image 
    FROM cart c 
    JOIN merchandise m ON c.product_id = m.id 
    WHERE c.user_id = ?
");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll();

// Calculate total
$total = 0;
foreach ($cart_items as $item) {
    $total += $item['price'] * $item['quantity'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Aespa Fan Website</title>
    <link rel="icon" href="pic/aespalogo.jpg">
    <link rel="stylesheet" href="styles/base.css">
    <link rel="stylesheet" href="styles/navbar.css">
    <link rel="stylesheet" href="styles/sections.css">
    <link rel="stylesheet" href="styles/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #6C63FF;
            --secondary-color: #FF6B6B;
            --accent-color: #4ECDC4;
            --background-color: #1A1A1A;
            --text-color: #FFFFFF;
            --card-background: #2A2A2A;
            --gradient: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        }

        body {
            background-color: var(--background-color);
            color: var(--text-color);
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .navbar {
            background: rgba(26, 26, 26, 0.95);
            backdrop-filter: blur(10px);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        }

        .nav-brand a {
            text-decoration: none;
        }

        .nav-brand img {
            height: 40px;
            transition: transform 0.3s ease;
        }

        .nav-brand img:hover {
            transform: scale(1.05);
        }

        .nav-links {
            display: flex;
            gap: 2rem;
            align-items: center;
        }

        .nav-links a {
            color: var(--text-color);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .nav-links a:hover {
            color: var(--primary-color);
        }

        .nav-links a.active {
            color: var(--primary-color);
        }

        main {
            margin-top: 80px;
            flex: 1;
        }

        .checkout-section {
            padding: 2rem 5%;
            max-width: 1200px;
            margin: 0 auto;
        }

        .checkout-section h1 {
            text-align: center;
            color: var(--text-color);
            margin-bottom: 1rem;
            font-size: 2.5rem;
            font-weight: 600;
            position: relative;
            z-index: 1;
        }

        .title-underline {
            width: 100px;
            height: 4px;
            background: var(--gradient);
            margin: -1rem auto 2rem;
            border-radius: 2px;
            position: relative;
            z-index: 0;
        }

        .checkout-container {
            max-width: 800px;
            margin: 0 auto;
        }

        .order-summary {
            background: var(--card-background);
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
        }

        .order-summary h2 {
            color: var(--text-color);
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
            font-weight: 600;
        }

        .order-items {
            margin-bottom: 2rem;
        }

        .order-item {
            display: flex;
            align-items: center;
            padding: 1rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .order-item img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
            margin-right: 1rem;
        }

        .item-details {
            flex: 1;
        }

        .item-details h4 {
            color: var(--text-color);
            margin: 0 0 0.5rem 0;
            font-size: 1.1rem;
        }

        .item-details p {
            color: rgba(255, 255, 255, 0.7);
            margin: 0.25rem 0;
            font-size: 0.9rem;
        }

        .order-total {
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1rem;
            color: var(--text-color);
        }

        .total-row.final {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--primary-color);
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .checkout-form {
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .place-order-btn {
            width: 100%;
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 1.5rem;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 1.2rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .place-order-btn:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(255, 107, 107, 0.3);
        }

        .empty-cart-message {
            text-align: center;
            padding: 3rem;
            background: var(--card-background);
            border-radius: 12px;
            margin-top: 2rem;
        }

        .empty-cart-message p {
            color: var(--text-color);
            font-size: 1.2rem;
            margin-bottom: 1.5rem;
        }

        .continue-shopping-btn {
            display: inline-block;
            background: var(--primary-color);
            color: white;
            padding: 1rem 2rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s;
        }

        .continue-shopping-btn:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(255, 107, 107, 0.3);
        }

        .success-message, .error-message {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            padding: 1rem 2rem;
            border-radius: 8px;
            animation: fadeInOut 2s ease-in-out;
            z-index: 1000;
            font-weight: 500;
            text-align: center;
            min-width: 200px;
        }

        .success-message {
            background: rgba(78, 205, 196, 0.9);
            color: white;
        }

        .error-message {
            background: rgba(255, 107, 107, 0.9);
            color: white;
        }

        @keyframes fadeInOut {
            0% { opacity: 0; transform: translate(-50%, -50%) scale(0.8); }
            20% { opacity: 1; transform: translate(-50%, -50%) scale(1); }
            80% { opacity: 1; transform: translate(-50%, -50%) scale(1); }
            100% { opacity: 0; transform: translate(-50%, -50%) scale(0.8); }
        }

        footer {
            background: var(--card-background);
            padding: 2rem 5%;
            margin-top: auto;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
        }

        .footer-section h3 {
            color: var(--text-color);
            margin-bottom: 1rem;
            font-size: 1.2rem;
        }

        .footer-section ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .footer-section ul li {
            margin-bottom: 0.5rem;
        }

        .footer-section ul li a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: color 0.3s;
        }

        .footer-section ul li a:hover {
            color: var(--primary-color);
        }

        .social-links {
            display: flex;
            gap: 1rem;
        }

        .social-links a {
            color: var(--text-color);
            font-size: 1.5rem;
            transition: color 0.3s;
        }

        .social-links a:hover {
            color: var(--primary-color);
        }

        .footer-bottom {
            text-align: center;
            padding-top: 2rem;
            margin-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.7);
        }

        @media (max-width: 768px) {
            .nav-links {
                display: none;
            }

            .checkout-container {
                padding: 0 1rem;
            }

            .order-item {
                flex-direction: column;
                text-align: center;
            }

            .order-item img {
                margin: 0 0 1rem 0;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-brand">
            <a href="index.php">
                <img src="pic/logo.png" alt="aespa" class="logo">
            </a>
        </div>
        <div class="nav-links">
            <a href="index.php">Home</a>
            <a href="news.php">News</a>
            <a href="albums.php">Albums</a>
            <a href="merch.php">Merch</a>
            <a href="gallery.php">Gallery</a>
            <a href="about.php">About</a>
            <a href="contact.php">Contact</a>
        </div>
    </nav>

    <main>
        <section class="checkout-section">
            <h1>Checkout</h1>
            <div class="title-underline"></div>

            <?php if (isset($_GET['success'])): ?>
                <div class="success-message">
                    Order completed successfully!
                </div>
            <?php endif; ?>

            <?php if (empty($cart_items)): ?>
                <div class="empty-cart-message">
                    <p>Your cart is empty</p>
                    <a href="merch.php" class="continue-shopping-btn">Continue Shopping</a>
                </div>
            <?php else: ?>
                <div class="checkout-container">
                    <div class="order-summary">
                        <h2>Order Summary</h2>
                        <div class="order-items">
                            <?php
                            $total = 0;
                            foreach ($cart_items as $item):
                                $subtotal = $item['price'] * $item['quantity'];
                                $total += $subtotal;
                            ?>
                                <div class="order-item">
                                    <img src="<?php echo htmlspecialchars($item['image']); ?>" 
                                         alt="<?php echo htmlspecialchars($item['name']); ?>">
                                    <div class="item-details">
                                        <h4><?php echo htmlspecialchars($item['name']); ?></h4>
                                        <p>Quantity: <?php echo $item['quantity']; ?></p>
                                        <p>Price: $<?php echo number_format($item['price'], 2); ?></p>
                                        <p>Subtotal: $<?php echo number_format($subtotal, 2); ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="order-total">
                            <div class="total-row">
                                <span>Subtotal</span>
                                <span>$<?php echo number_format($total, 2); ?></span>
                            </div>
                            <div class="total-row">
                                <span>Shipping</span>
                                <span>Free</span>
                            </div>
                            <div class="total-row final">
                                <span>Total</span>
                                <span>$<?php echo number_format($total, 2); ?></span>
                            </div>
                        </div>

                        <form action="checkout.php" method="POST" class="checkout-form">
                            <button type="submit" class="place-order-btn">Complete Purchase</button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h3>Connect With Us</h3>
                <div class="social-links">
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-youtube"></i></a>
                    <a href="#"><i class="fab fa-tiktok"></i></a>
                </div>
            </div>
            <div class="footer-section">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="news.php">News</a></li>
                    <li><a href="albums.php">Albums</a></li>
                    <li><a href="merch.php">Merch</a></li>
                    <li><a href="gallery.php">Gallery</a></li>
                    <li><a href="about.php">About</a></li>
                    <li><a href="contact.php">Contact</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> Aespa Fan Website. All rights reserved.</p>
        </div>
    </footer>
</body>
</html> 