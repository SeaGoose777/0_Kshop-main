<?php
require_once 'config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

// Get user information
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Get user's orders
$stmt = $pdo->prepare("
    SELECT o.*, oi.quantity, oi.price, p.name as product_name 
    FROM orders o 
    JOIN order_items oi ON o.id = oi.order_id 
    JOIN merchandise p ON oi.product_id = p.id 
    WHERE o.user_id = ? 
    ORDER BY o.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account - aespa Fan Website</title>
    <link rel="icon" href="pic/aespalogo.jpg">
    <link rel="stylesheet" href="styles/base.css">
    <link rel="stylesheet" href="styles/navbar.css">
    <link rel="stylesheet" href="styles/sections.css">
    <link rel="stylesheet" href="styles/account.css">
    <link rel="stylesheet" href="styles/footer.css">
    <link rel="stylesheet" href="styles/animations.css">
    <link rel="stylesheet" href="styles/responsive.css">
    <link rel="stylesheet" href="styles/cart.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
        <div class="nav-icons">
            <a href="cart.php" class="cart-icon" id="cart-icon">
                <i class="fas fa-shopping-cart"></i>
                <span class="cart-count"><?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?></span>
            </a>
            <a href="#" class="search-icon"><i class="fas fa-search"></i></a>
            <?php if (isLoggedIn()): ?>
                <a href="account.php" class="account-icon"><i class="fas fa-user"></i></a>
                <a href="logout.php" class="logout-icon"><i class="fas fa-sign-out-alt"></i></a>
            <?php else: ?>
                <a href="login.php" class="login-icon"><i class="fas fa-sign-in-alt"></i> Log In</a>
            <?php endif; ?>
        </div>
        <div class="hamburger">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </nav>

    <!-- Cart Sidebar -->
    <div class="cart-sidebar" id="cart-sidebar">
        <div class="cart-header">
            <h3>Your Cart</h3>
            <button class="close-cart" id="close-cart">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="cart-items" id="cart-items">
            <!-- Cart items will be dynamically added here -->
        </div>
        <div class="cart-total">
            <h4>Total: $<span id="cart-total">0.00</span></h4>
            <a href="cart.php" class="checkout-btn">Purchase</a>
        </div>
    </div>

    <main>
        <section class="account-section">
            <div class="account-container">
                <h2>My Account</h2>
                
                <div class="account-info">
                    <h3>Account Information</h3>
                    <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                    <p><strong>Full Name:</strong> <?php echo htmlspecialchars($user['full_name']); ?></p>
                    <p><strong>Address:</strong> <?php echo htmlspecialchars($user['address'] ?? 'Not set'); ?></p>
                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($user['phone'] ?? 'Not set'); ?></p>
                    
                    <a href="edit_account.php" class="edit-button">Edit Account</a>
                </div>
                
                <div class="order-history">
                    <h3>Order History</h3>
                    <?php if (empty($orders)): ?>
                        <p>No orders found.</p>
                    <?php else: ?>
                        <?php foreach ($orders as $order): ?>
                            <div class="order">
                                <h4>Order #<?php echo $order['id']; ?></h4>
                                <p>Date: <?php echo date('F j, Y', strtotime($order['created_at'])); ?></p>
                                <p>Status: <?php echo htmlspecialchars($order['status']); ?></p>
                                <p>Total: $<?php echo number_format($order['total_amount'], 2); ?></p>
                                
                                <div class="order-items">
                                    <h5>Items:</h5>
                                    <ul>
                                        <li>
                                            <?php echo htmlspecialchars($order['product_name']); ?> - 
                                            Quantity: <?php echo $order['quantity']; ?> - 
                                            Price: $<?php echo number_format($order['price'], 2); ?>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h3>Connect With Us</h3>
                <div class="social-links">
                    <a href="https://www.instagram.com/aespa_official/" target="_blank" rel="noopener noreferrer"><i class="fab fa-instagram"></i></a>
                    <a href="https://x.com/aespa_official" target="_blank" rel="noopener noreferrer"><i class="fa-brands fa-x-twitter"></i></a>
                    <a href="https://www.youtube.com/aespa" target="_blank" rel="noopener noreferrer"><i class="fab fa-youtube"></i></a>
                    <a href="https://www.tiktok.com/@aespa_official" target="_blank" rel="noopener noreferrer"><i class="fab fa-tiktok"></i></a>
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
            <div class="footer-section">
                <h3>Newsletter</h3>
                <form class="newsletter-form" method="POST" action="subscribe.php">
                    <input type="email" name="email" placeholder="Enter your email" required>
                    <button type="submit">Subscribe</button>
                </form>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> aespa Fan Website. All rights reserved.</p>
        </div>
    </footer>

    <script src="scripts/script.js"></script>
    <script src="scripts/cart.js"></script>
    <script src="scripts/newsletter.js"></script>
</body>
</html> 