<?php 
session_start();
require_once 'conn.php';

// Get all products from database
$sql = "SELECT * FROM products";
$result = $conn->query($sql);
$products = [];
while($row = $result->fetch_assoc()) {
    $products[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MERCHANDISE</title>
    <link rel="icon" href="ICONS/WHIPLASH LOGO.png" sizes="32x32">
    <link rel="stylesheet" href="common.css">
    <link rel="stylesheet" href="stylesmerch.css">
</head>
<body>
    <div id="notification-container"></div>
    <div class="navbar">
        <input type="checkbox" id="menu-toggle" hidden>
    
        <label for="menu-toggle" class="menu-icon">
            <img src="ICONS/menu-bar.png" alt="Menu" class="icon">
        </label>
    
        <div class="sidebar">
            <a href="ALBUMS.php">
                <img src="ICONS/stack.png" alt="Albums" class="dropdown-icon"> Albums
            </a>
            <a href="MERCH.php">
                <img src="ICONS/bag.png" alt="Merchandise" class="dropdown-icon"> Merchandise
            </a>
            <a href="">
                <img src="ICONS/people.png" alt="About us" class="dropdown-icon"> About us
            </a>
        </div>
    
        <label for="menu-toggle" class="overlay"></label>
    
        <a href="index.php" class="home-logo">
            <img src="ICONS/MAIN_LOGO.jpeg" alt="Home" class="logo-center">
        </a>
    
        <div class="right-icons">
            <?php if (isset($_SESSION['firstname'])): ?>
                <span class="user-welcome">
                    Welcome, <?= htmlspecialchars($_SESSION['firstname']) ?>
                </span>
                <a href="logout.php">
                    <img src="ICONS/exit.png" alt="Logout" class="icon" />
                </a>
            <?php else: ?>
                <a href="LOGIN.html">
                    <img src="ICONS/account.png" alt="Account" class="icon" />
                </a>
            <?php endif; ?>

            <div class="cart-icon">
                <img src="ICONS/shopping-cart.png" alt="Cart" class="icon">
                <span class="cart-count">0</span>
            </div>
        </div>
    </div>

    <hr>

    <!-- Cart Modal -->
    <div id="cart-modal" class="modal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <h2>Shopping Cart</h2>
            <ul id="cart-modal-items"></ul>
            <div class="modal-footer">
                <div class="modal-total">
                    Total: <span id="cart-modal-total">₱0</span>
                </div>
                <div class="modal-buttons">
                    <button id="modal-checkout" class="checkout-button">Checkout</button>
                    <button id="modal-remove" class="remove-all-button">Remove All</button>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <h1>MERCHANDISE</h1>
        
        <div class="product-list">
            <?php foreach($products as $product): ?>
            <div class="product" data-id="<?= $product['product_id'] ?>" data-name="<?= htmlspecialchars($product['name']) ?>" data-price="<?= $product['price'] ?>">
                <img src="<?= htmlspecialchars($product['image_path']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                <h3><?= htmlspecialchars($product['name']) ?></h3>
                <p>₱<?= number_format($product['price'], 2) ?></p>
                <div class="quantity-control">
                    <button class="quantity-btn minus" type="button">-</button>
                    <input type="number" class="quantity-input" value="1" min="1" max="<?= $product['quantity'] ?>" readonly>
                    <button class="quantity-btn plus" type="button">+</button>
                </div>
                <button class="add-to-cart" type="button">Add to Cart</button>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="common.js"></script>
    <script src="script_merch.js"></script>
</body>
</html>