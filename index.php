<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>aespa - Official Fan Website</title>
    <link rel="icon" href="pic/aespalogo.jpg">
    <link rel="stylesheet" href="styles/base.css">
    <link rel="stylesheet" href="styles/navbar.css">
    <link rel="stylesheet" href="styles/hero.css">
    <link rel="stylesheet" href="styles/sections.css">
    <link rel="stylesheet" href="styles/news.css">
    <link rel="stylesheet" href="styles/albums.css">
    <link rel="stylesheet" href="styles/merch.css">
    <link rel="stylesheet" href="styles/about.css">
    <link rel="stylesheet" href="styles/contact.css">
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
            <a href="index.php" class="active">Home</a>
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
        <section class="hero">
            <div class="hero-content">
                <h1>Welcome to Aespa World<?php if (isLoggedIn()): ?>, <?php echo htmlspecialchars($_SESSION['username']); ?>!<?php endif; ?></h1>
                <p>Experience the future of K-pop with aespa</p>
                <div class="hero-buttons">
                    <a href="albums.php" class="cta-button">Explore Music</a>
                    <a href="about.php" class="cta-button secondary">Learn More</a>
                </div>
            </div>
        </section>

        <section class="featured-section">
            <h2>Featured Content</h2>
            <div class="featured-grid">
                <div class="featured-card">
                    <img src="ALBUM/Aespa_-_Whiplash.png" alt="Latest Release">
                    <div class="featured-content">
                        <h3>Latest Release</h3>
                        <p>Check out our newest album "Whiplash"</p>
                        <a href="albums.php" class="featured-link">Listen Now</a>
                    </div>
                </div>
                <div class="featured-card">
                    <img src="MERCH/aespa1stalbum.jpeg" alt="Official Merch">
                    <div class="featured-content">
                        <h3>New Merchandise</h3>
                        <p>Exclusive aespa merchandise available now</p>
                        <a href="merch.php" class="featured-link">Shop Now</a>
                    </div>
                </div>
                <div class="featured-card">
                    <img src="pic/encore.jpeg" alt="Latest News">
                    <div class="featured-content">
                        <h3>Latest News</h3>
                        <p>Stay updated with aespa's activities</p>
                        <a href="news.php" class="featured-link">Read More</a>
                    </div>
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