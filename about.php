<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About aespa - Official Fan Website</title>
    <link rel="icon" href="pic/aespalogo.jpg">
    <link rel="stylesheet" href="styles/base.css">
    <link rel="stylesheet" href="styles/navbar.css">
    <link rel="stylesheet" href="styles/sections.css">
    <link rel="stylesheet" href="styles/about.css">
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
            <a href="about.php" class="active">About</a>
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
        <section class="about-hero">
            <div class="about-hero-content">
                <h1>About aespa</h1>
                <p>Meet the future of K-pop</p>
            </div>
        </section>

        <section class="about-intro">
            <div class="about-text">
                <h2>aespa's Story</h2>
                <p>aespa is a South Korean girl group formed by SM Entertainment in 2020. The group consists of four members: Karina, Giselle, Winter, and Ningning. Their name "aespa" combines the English words "avatar" and "experience" with the word "aspect," representing the group's unique concept of combining real-world and virtual members.</p>
                <p>Known for their innovative approach to K-pop, aespa has been making waves in the industry with their unique concept of "metaverse" and "AI" avatars, creating a new paradigm in the world of K-pop entertainment.</p>
            </div>
        </section>

        <section class="members-section">
            <h2>Meet the Members</h2>
            <div class="members-grid">
                <div class="member-card">
                    <img src="pic/karina.jpg" alt="Karina">
                    <div class="member-info">
                        <h3>Karina</h3>
                        <p class="position">Leader, Main Dancer</p>
                        <p class="description">Born on April 11, 2000, Karina is known for her powerful dance moves and charismatic stage presence. She leads the group with her strong leadership and versatile talents.</p>
                    </div>
                </div>
                <div class="member-card">
                    <img src="pic/giselle.jpg" alt="Giselle">
                    <div class="member-info">
                        <h3>Giselle</h3>
                        <p class="position">Main Rapper</p>
                        <p class="description">Born on October 30, 2000, Giselle brings her unique rap style and multilingual abilities to the group, being fluent in Korean, Japanese, and English.</p>
                    </div>
                </div>
                <div class="member-card">
                    <img src="pic/winter.jpg" alt="Winter">
                    <div class="member-info">
                        <h3>Winter</h3>
                        <p class="position">Lead Vocalist</p>
                        <p class="description">Born on January 1, 2001, Winter is known for her crystal-clear vocals and exceptional dance skills, making her one of the group's most versatile members.</p>
                    </div>
                </div>
                <div class="member-card">
                    <img src="pic/ningning.jpg" alt="Ningning">
                    <div class="member-info">
                        <h3>Ningning</h3>
                        <p class="position">Main Vocalist</p>
                        <p class="description">Born on October 23, 2002, Ningning is the youngest member and main vocalist, known for her powerful vocals and charming personality.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="concept-section">
            <h2>Our Concept</h2>
            <div class="concept-content">
                <div class="concept-text">
                    <h3>The Metaverse Experience</h3>
                    <p>aespa's unique concept revolves around the idea of "metaverse" and "AI" avatars. Each member has their own virtual counterpart, known as their "ae" (avatar and experience). These virtual members exist in the "KWANGYA" universe, creating an innovative and immersive experience for fans.</p>
                    <h3>Music and Message</h3>
                    <p>Through their music, aespa explores themes of technology, virtual reality, and the relationship between the real and digital worlds. Their songs often carry messages about self-discovery, empowerment, and the future of human-AI interaction.</p>
                </div>
                <div class="concept-image">
                    <img src="pic/concept.jpeg" alt="aespa Concept">
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