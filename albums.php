<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Albums - aespa Fan Website</title>
    <link rel="icon" href="pic/aespalogo.jpg">
    <link rel="stylesheet" href="styles/base.css">
    <link rel="stylesheet" href="styles/navbar.css">
    <link rel="stylesheet" href="styles/sections.css">
    <link rel="stylesheet" href="styles/albums.css">
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
            <a href="albums.php" class="active">Albums</a>
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
        <section class="albums-section">
            <h2>Discography</h2>
            <div class="albums-grid">
                <div class="album-card" onclick="window.location.href='https://open.spotify.com/album/3OaW4df1SA62k0arNpn6bK?si=PBAVgA55Sveg7kISjWdLCg'">
                    <img src="ALBUM/Aespa_-_Whiplash.png" alt="Whiplash">
                    <div class="album-content">
                        <h3>Whiplash</h3>
                        <p>2024</p>
                        <div class="tracks">
                            <span>Whiplash</span>
                            <span>Kill it</span>
                            <span>Flights, Not Feelings</span>
                            <span>Pink Hoodie</span>
                            <span>Flowers</span>
                            <span>Just Another Girl</span>
                        </div>
                    </div>
                </div>
                <div class="album-card" onclick="window.location.href='https://open.spotify.com/album/3gHhPm8z8tid1kvpniUKuK?si=MHapxBlDRU-Whpf7oqG-tg'">
                    <img src="ALBUM/Aespa_Armageddon.jpg" alt="Armageddon">
                    <div class="album-content">
                        <h3>Armageddon</h3>
                        <p>2024</p>
                        <div class="tracks">
                            <span>Supernova</span>
                            <span>Armageddon</span>
                            <span>Set The Tone</span>
                            <span>Mine</span>
                            <span>Licorice</span>
                            <span>BAHAMA</span>
                            <span>Long Chat (#♥︎)</span>
                            <span>Prologue</span>
                            <span>Live My Life</span>
                            <span>Melody</span>
                        </div>
                    </div>
                </div>
                <div class="album-card" onclick="window.location.href='https://open.spotify.com/album/5NMtxQJy4wq3mpo3ERVnLs?si=1Xt53iMJS7afTeaTAgZKdQ'">
                    <img src="ALBUM/Aespa_-_Drama.png" alt="Drama">
                    <div class="album-content">
                        <h3>Drama</h3>
                        <p>2023</p>
                        <div class="tracks">
                            <span>Drama</span>
                            <span>Trick or Trick</span>
                            <span>Don't Blink</span>
                            <span>Hot Air Balloon</span>
                            <span>YOLO</span>
                            <span>You</span>
                            <span>Better Things</span>
                        </div>
                    </div>
                </div>
                <div class="album-card" onclick="window.location.href='https://open.spotify.com/album/69xF8jTd0c4Zoo7DT3Rwrn?si=Wdtxsn3rT2CfJuzEvwMKyw'">
                    <img src="ALBUM/Aespa_-_My_World.png" alt="My World">
                    <div class="album-content">
                        <h3>My World</h3>
                        <p>2023</p>
                        <div class="tracks">
                            <span>Welcome to MY World (ft.nævis)</span>
                            <span>Spicy</span>
                            <span>Salty & Sweet</span>
                            <span>Thirsty</span>
                            <span>'Till We Meet Again</span>
                            <span>I'm Unhappy</span>
                        </div>
                    </div>
                </div>
                <div class="album-card" onclick="window.location.href='https://open.spotify.com/album/4w1dbvUy1crv0knXQvcSeY?si=-zMuOmgYSV2eATFM3sNHDA'">
                    <img src="ALBUM/Aespa_-_Girls.png" alt="Girls">
                    <div class="album-content">
                        <h3>Girls - The 2nd Mini Album</h3>
                        <p>2022</p>
                        <div class="tracks">
                            <span>Girls</span>
                            <span>Illusion</span>
                            <span>Lingo</span>
                            <span>Life's Too Short</span>
                            <span>ICU</span>
                            <span>Black Mamba</span>
                            <span>Forever</span>
                            <span>Dreams Come True</span>
                        </div>
                    </div>
                </div>
                <div class="album-card" onclick="window.location.href='https://open.spotify.com/album/3vyyDkvYWC36DwgZCYd3Wu?si=uG51rektRP-KsTjMfsaONA'">
                    <img src="ALBUM/savage.png" alt="Savage">
                    <div class="album-content">
                        <h3>Savage - The 1st Mini Album</h3>
                        <p>2021</p>
                        <div class="tracks">
                            <span>aenergy</span>
                            <span>Savage</span>
                            <span>I'll Make You Cry</span>
                            <span>YEPPI YEPPI</span>
                            <span>ICONIC</span>
                            <span>Lucid Dream</span>
                        </div>
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