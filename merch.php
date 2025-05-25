<?php
session_start();
require_once 'config.php';

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Please log in to add items to cart']);
        exit;
    }

    $action = $_POST['action'];
    $product_id = $_POST['product_id'] ?? 0;
    $quantity = $_POST['quantity'] ?? 1;

    try {
        switch ($action) {
            case 'add':
                // Check if product exists and has enough stock
                $stmt = $pdo->prepare("SELECT stock FROM merchandise WHERE id = ?");
                $stmt->execute([$product_id]);
                $product = $stmt->fetch();

                if (!$product) {
                    throw new Exception('Product not found');
                }

                if ($product['stock'] < $quantity) {
                    throw new Exception('Not enough stock available');
                }

                // Check if item already in cart
                $stmt = $pdo->prepare("SELECT quantity FROM cart WHERE user_id = ? AND product_id = ?");
                $stmt->execute([$_SESSION['user_id'], $product_id]);
                $cart_item = $stmt->fetch();

                if ($cart_item) {
                    // Update quantity if item exists
                    $new_quantity = $cart_item['quantity'] + $quantity;
                    if ($new_quantity > $product['stock']) {
                        throw new Exception('Not enough stock available');
                    }
                    $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
                    $stmt->execute([$new_quantity, $_SESSION['user_id'], $product_id]);
                } else {
                    // Add new item to cart
                    $stmt = $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
                    $stmt->execute([$_SESSION['user_id'], $product_id, $quantity]);
                }

                // Get updated cart items for modal
                $stmt = $pdo->prepare("
                    SELECT c.*, m.name, m.price, m.image 
                    FROM cart c 
                    JOIN merchandise m ON c.product_id = m.id 
                    WHERE c.user_id = ?
                ");
                $stmt->execute([$_SESSION['user_id']]);
                $cart_items = $stmt->fetchAll();

                $total = 0;
                foreach ($cart_items as $item) {
                    $total += $item['price'] * $item['quantity'];
                }

                echo json_encode([
                    'success' => true,
                    'message' => 'Item added to cart',
                    'stock' => $product['stock'],
                    'cart_html' => generateCartHTML($cart_items, $total)
                ]);
                break;

            case 'update':
                // Check if product exists and has enough stock
                $stmt = $pdo->prepare("SELECT stock FROM merchandise WHERE id = ?");
                $stmt->execute([$product_id]);
                $product = $stmt->fetch();

                if (!$product) {
                    throw new Exception('Product not found');
                }

                if ($quantity > $product['stock']) {
                    throw new Exception('Not enough stock available');
                }

                // Update cart quantity
                $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
                $stmt->execute([$quantity, $_SESSION['user_id'], $product_id]);

                // Get updated cart items for modal
                $stmt = $pdo->prepare("
                    SELECT c.*, m.name, m.price, m.image 
                    FROM cart c 
                    JOIN merchandise m ON c.product_id = m.id 
                    WHERE c.user_id = ?
                ");
                $stmt->execute([$_SESSION['user_id']]);
                $cart_items = $stmt->fetchAll();

                $total = 0;
                foreach ($cart_items as $item) {
                    $total += $item['price'] * $item['quantity'];
                }

                echo json_encode([
                    'success' => true,
                    'message' => 'Cart updated',
                    'stock' => $product['stock'],
                    'cart_html' => generateCartHTML($cart_items, $total)
                ]);
                break;

            case 'remove':
                // Remove from cart
                $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
                $stmt->execute([$_SESSION['user_id'], $product_id]);

                // Get updated cart items for modal
                $stmt = $pdo->prepare("
                    SELECT c.*, m.name, m.price, m.image 
                    FROM cart c 
                    JOIN merchandise m ON c.product_id = m.id 
                    WHERE c.user_id = ?
                ");
                $stmt->execute([$_SESSION['user_id']]);
                $cart_items = $stmt->fetchAll();

                $total = 0;
                foreach ($cart_items as $item) {
                    $total += $item['price'] * $item['quantity'];
                }

                echo json_encode([
                    'success' => true,
                    'message' => 'Item removed from cart',
                    'cart_html' => generateCartHTML($cart_items, $total)
                ]);
                break;

            default:
                throw new Exception('Invalid action');
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

// Helper function to generate cart HTML
function generateCartHTML($cart_items, $total) {
    ob_start();
    ?>
    <div class="cart-items">
        <?php if (empty($cart_items)): ?>
            <p class="empty-cart">Your cart is empty</p>
        <?php else: ?>
            <?php foreach ($cart_items as $item): ?>
                <div class="cart-item" data-id="<?php echo $item['product_id']; ?>">
                    <img src="<?php echo htmlspecialchars($item['image']); ?>" 
                         alt="<?php echo htmlspecialchars($item['name']); ?>">
                    <div class="item-details">
                        <h4><?php echo htmlspecialchars($item['name']); ?></h4>
                        <p>Quantity: <?php echo $item['quantity']; ?></p>
                        <p>Price: $<?php echo number_format($item['price'], 2); ?></p>
                        <p>Subtotal: $<?php echo number_format($item['price'] * $item['quantity'], 2); ?></p>
                    </div>
                    <button class="remove-item" data-id="<?php echo $item['product_id']; ?>">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            <?php endforeach; ?>
            <div class="cart-total">
                <h4>Total: $<?php echo number_format($total, 2); ?></h4>
                <a href="checkout.php" class="checkout-btn">Proceed to Checkout</a>
            </div>
        <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
}

// Get all products
$stmt = $pdo->query("SELECT * FROM merchandise ORDER BY name");
$products = $stmt->fetchAll();

// Get cart count
$cart_count = 0;
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT SUM(quantity) as count FROM cart WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $result = $stmt->fetch();
    $cart_count = $result['count'] ?? 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Official Merchandise - Aespa Fan Website</title>
    <link rel="icon" href="pic/aespalogo.jpg">
    <link rel="stylesheet" href="styles/base.css">
    <link rel="stylesheet" href="styles/navbar.css">
    <link rel="stylesheet" href="styles/sections.css">
    <link rel="stylesheet" href="styles/footer.css">
    <link rel="stylesheet" href="styles/animations.css">
    <link rel="stylesheet" href="styles/responsive.css">
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
            <a href="merch.php" class="active">Merch</a>
            <a href="gallery.php">Gallery</a>
            <a href="about.php">About</a>
            <a href="contact.php">Contact</a>
        </div>
        <div class="nav-icons">
            <a href="#" class="cart-icon" id="cart-icon">
                <i class="fas fa-shopping-cart"></i>
                <span class="cart-count" id="cart-count"><?php echo $cart_count; ?></span>
            </a>
            <a href="#" class="search-icon"><i class="fas fa-search"></i></a>
            <?php if (isset($_SESSION['user_id'])): ?>
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

    <!-- Cart Modal -->
    <div class="cart-modal" id="cart-modal">
        <div class="cart-content">
            <div class="cart-header">
                <h3>Your Cart</h3>
                <button class="close-cart" id="close-cart">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="cart-body" id="cart-body">
                <?php
                if (isset($_SESSION['user_id'])) {
                    $stmt = $pdo->prepare("
                        SELECT c.*, m.name, m.price, m.image 
                        FROM cart c 
                        JOIN merchandise m ON c.product_id = m.id 
                        WHERE c.user_id = ?
                    ");
                    $stmt->execute([$_SESSION['user_id']]);
                    $cart_items = $stmt->fetchAll();
                    $total = 0;
                    foreach ($cart_items as $item) {
                        $total += $item['price'] * $item['quantity'];
                    }
                    echo generateCartHTML($cart_items, $total);
                } else {
                    echo '<p class="empty-cart">Please log in to view your cart</p>';
                }
                ?>
            </div>
        </div>
    </div>

    <main>
        <section class="merch-section">
            <h1>Official Merchandise</h1>
            <div class="title-underline"></div>
            
            <?php if (isset($_GET['success'])): ?>
                <div class="success-message">
                    Order completed successfully!
                </div>
            <?php endif; ?>

            <div class="merch-grid">
                <?php foreach ($products as $product): ?>
                    <div class="merch-item">
                        <img src="<?php echo htmlspecialchars($product['image']); ?>" 
                             alt="<?php echo htmlspecialchars($product['name']); ?>">
                        <div class="merch-info">
                            <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                            <p><?php echo htmlspecialchars($product['description']); ?></p>
                            <p class="price">$<?php echo number_format($product['price'], 2); ?></p>
                            <p class="stock-status <?php echo $product['stock'] > 0 ? 'in-stock' : 'out-of-stock'; ?>">
                                <?php echo $product['stock'] > 0 ? 'In Stock: ' . $product['stock'] : 'Out of Stock'; ?>
                            </p>
                            
                            <?php if ($product['stock'] > 0): ?>
                                <div class="quantity-controls">
                                    <button class="quantity-btn minus" data-id="<?php echo $product['id']; ?>">-</button>
                                    <input type="number" class="quantity-input" value="1" min="1" max="<?php echo $product['stock']; ?>" 
                                           data-id="<?php echo $product['id']; ?>">
                                    <button class="quantity-btn plus" data-id="<?php echo $product['id']; ?>">+</button>
                                </div>
                                <button class="add-to-cart" data-id="<?php echo $product['id']; ?>">Add to Cart</button>
                            <?php else: ?>
                                <button class="add-to-cart" disabled>Out of Stock</button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
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
            <p>&copy; <?php echo date('Y'); ?> Aespa Fan Website. All rights reserved.</p>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Cart Modal
            const cartIcon = document.getElementById('cart-icon');
            const cartModal = document.getElementById('cart-modal');
            const closeCart = document.getElementById('close-cart');
            const cartBody = document.getElementById('cart-body');

            cartIcon.addEventListener('click', function(e) {
                e.preventDefault();
                cartModal.classList.add('active');
            });

            closeCart.addEventListener('click', function() {
                cartModal.classList.remove('active');
            });

            // Close modal when clicking outside
            cartModal.addEventListener('click', function(e) {
                if (e.target === cartModal) {
                    cartModal.classList.remove('active');
                }
            });

            // Quantity controls
            document.querySelectorAll('.quantity-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const input = this.parentElement.querySelector('.quantity-input');
                    const currentValue = parseInt(input.value);
                    const max = parseInt(input.max);
                    
                    if (this.classList.contains('minus')) {
                        if (currentValue > 1) {
                            input.value = currentValue - 1;
                        }
                    } else {
                        if (currentValue < max) {
                            input.value = currentValue + 1;
                        }
                    }
                });
            });

            document.querySelectorAll('.add-to-cart').forEach(button => {
                button.addEventListener('click', function() {
                    const productId = this.dataset.id;
                    const input = document.querySelector(`.quantity-input[data-id="${productId}"]`);
                    const quantity = parseInt(input.value);

                    fetch('merch.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `action=add&product_id=${productId}&quantity=${quantity}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const cartCount = document.getElementById('cart-count');
                            cartCount.textContent = parseInt(cartCount.textContent) + quantity;
                            
                            cartBody.innerHTML = data.cart_html;
                            
                            const stockStatus = this.closest('.merch-item').querySelector('.stock-status');
                            if (stockStatus) {
                                stockStatus.textContent = 'In Stock: ' + data.stock;
                                if (data.stock === 0) {
                                    stockStatus.textContent = 'Out of Stock';
                                    stockStatus.classList.remove('in-stock');
                                    stockStatus.classList.add('out-of-stock');
                                    this.disabled = true;
                                    this.textContent = 'Out of Stock';
                                }
                            }
                            
                            // Show success message
                            const successMessage = document.createElement('div');
                            successMessage.className = 'success-message';
                            successMessage.textContent = data.message;
                            document.querySelector('.merch-section').insertBefore(successMessage, document.querySelector('.merch-grid'));
                            
                            setTimeout(() => {
                                successMessage.remove();
                            }, 2000);

                            // Show cart modal
                            cartModal.classList.add('active');
                        } else {
                            alert(data.message || 'Error adding item to cart');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error adding item to cart');
                    });
                });
            });

            cartBody.addEventListener('click', function(e) {
                if (e.target.closest('.remove-item')) {
                    const button = e.target.closest('.remove-item');
                    const productId = button.dataset.id;

                    fetch('merch.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `action=remove&product_id=${productId}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Update cart count
                            const cartCount = document.getElementById('cart-count');
                            const removedItem = button.closest('.cart-item');
                            const removedQuantity = parseInt(removedItem.querySelector('.item-details p:nth-child(2)').textContent.split(': ')[1]);
                            cartCount.textContent = parseInt(cartCount.textContent) - removedQuantity;
                            
                            // Update cart modal content
                            cartBody.innerHTML = data.cart_html;
                        } else {
                            alert(data.message || 'Error removing item from cart');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error removing item from cart');
                    });
                }
            });

            // Add hamburger menu functionality
            const hamburger = document.querySelector('.hamburger');
            const navLinks = document.querySelector('.nav-links');

            hamburger.addEventListener('click', function() {
                this.classList.toggle('active');
                navLinks.classList.toggle('active');
            });

            // Close mobile menu when clicking outside
            document.addEventListener('click', function(e) {
                if (!hamburger.contains(e.target) && !navLinks.contains(e.target)) {
                    hamburger.classList.remove('active');
                    navLinks.classList.remove('active');
                }
            });
        });
    </script>

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

        .merch-section {
            max-width: 1400px;
            margin: 0 auto;
        }

        .merch-section h1 {
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

        .merch-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 2rem;
            padding: 1rem;
        }

        .merch-item {
            background: var(--card-background);
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
            display: flex;
            flex-direction: column;
        }

        .merch-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
        }

        .merch-item img {
            width: 100%;
            height: 280px;
            object-fit: cover;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .merch-info {
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            flex: 1;
            justify-content: space-between;
        }

        .merch-info-content {
            flex: 1;
        }

        .merch-info-actions {
            margin-top: auto;
            width: 100%;
        }

        .merch-info h3 {
            color: var(--text-color);
            text-align: center;
            font-size: 1.25rem;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        .merch-info p {
            color: rgba(255, 255, 255, 0.7);
            text-align: center;
            margin-bottom: 1rem;
            line-height: 1.5;
        }

        .price {
            color: var(--primary-color) !important;
            font-size: 1.5rem !important;
            font-weight: 600 !important;
            margin: 1rem 0 !important;
        }

        .stock-status {
            font-size: 0.9rem;
            margin: 0.5rem 0;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            display: inline-block;
        }

        .in-stock {
            background-color: rgba(78, 205, 196, 0.1);
            color: var(--accent-color);
        }

        .out-of-stock {
            background-color: rgba(255, 107, 107, 0.1);
            color: var(--secondary-color);
        }

        .quantity-controls {
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 1rem 0;
            gap: 0.5rem;
        }

        .quantity-btn {
            background: rgba(255, 255, 255, 0.1);
            border: none;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 1.2rem;
            color: var(--text-color);
        }

        .quantity-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: scale(1.05);
        }

        .quantity-input {
            width: 60px;
            text-align: center;
            padding: 0.5rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 4px;
            font-size: 1rem;
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-color);
        }

        .add-to-cart {
            width: 100%;
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 1rem;
            border-radius: 8px;
            cursor: pointer;
            margin: 0;
            transition: all 0.3s;
            font-size: 1rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: block;
            text-align: center;
        }

        .add-to-cart:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(255, 107, 107, 0.3);
        }

        .add-to-cart:disabled {
            background: rgba(255, 255, 255, 0.1);
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        /* Cart Modal Styles */
        .cart-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            z-index: 1000;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .cart-modal.active {
            display: flex;
            opacity: 1;
        }

        .cart-content {
            position: fixed;
            top: 0;
            right: -400px;
            width: 400px;
            height: 100%;
            background: var(--card-background);
            box-shadow: -2px 0 5px rgba(0, 0, 0, 0.3);
            transition: right 0.3s ease;
            overflow-y: auto;
        }

        .cart-modal.active .cart-content {
            right: 0;
        }

        .cart-header {
            padding: 1.5rem;
            background: var(--primary-color);
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1;
        }

        .cart-header h3 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 600;
        }

        .close-cart {
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
            padding: 0.5rem;
            transition: transform 0.3s;
        }

        .close-cart:hover {
            transform: rotate(90deg);
        }

        .cart-body {
            padding: 1.5rem;
        }

        .cart-item {
            display: flex;
            align-items: center;
            padding: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            position: relative;
            transition: background-color 0.3s;
        }

        .cart-item:hover {
            background-color: rgba(255, 255, 255, 0.05);
        }

        .cart-item img {
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
            margin: 0 0 0.5rem 0;
            color: var(--text-color);
            font-size: 1.1rem;
        }

        .item-details p {
            margin: 0.25rem 0;
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.9rem;
        }

        .remove-item {
            background: none;
            border: none;
            color: var(--secondary-color);
            cursor: pointer;
            padding: 0.5rem;
            transition: transform 0.3s;
        }

        .remove-item:hover {
            transform: scale(1.1);
        }

        .cart-total {
            padding: 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            text-align: right;
            background: rgba(255, 255, 255, 0.05);
            position: sticky;
            bottom: 0;
        }

        .cart-total h4 {
            margin: 0 0 1rem 0;
            color: var(--text-color);
            font-size: 1.25rem;
        }

        .checkout-btn {
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

        .checkout-btn:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(255, 107, 107, 0.3);
        }

        .empty-cart {
            text-align: center;
            padding: 3rem 1rem;
            color: rgba(255, 255, 255, 0.7);
            font-size: 1.1rem;
        }

        /* Message Styles */
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

        /* Responsive Styles */
        @media (max-width: 768px) {
            .merch-grid {
                grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
                gap: 1rem;
            }

            .cart-content {
                width: 100%;
                right: -100%;
            }

            .merch-item img {
                height: 240px;
            }

            .merch-info {
                padding: 1rem;
            }

            .price {
                font-size: 1.25rem !important;
            }
        }

        @media (max-width: 480px) {
            .merch-section {
                padding: 1rem;
            }

            .merch-section h1 {
                font-size: 2rem;
            }

            .merch-grid {
                grid-template-columns: 1fr;
            }

            .quantity-controls {
                flex-wrap: wrap;
            }
        }
    </style>
</body>
</html> 