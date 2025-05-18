<?php session_start(); 
?>
<!DOCTYPE html>
<html>
<head>
    <title>Home</title>
    <link rel="icon" href="ICONS/WHIPLASH LOGO.png" sizes="32x32">
    <link rel="stylesheet" href="stylesindex.css">
    <link rel="stylesheet" href="common.css">
</head>

<body>
<main>
    <div class="navbar">
        <input type="checkbox" id="menu-toggle" hidden>

        <label for="menu-toggle" class="menu-icon">
            <img src="ICONS/menu-bar.png" alt="Menu" class="icon">
        </label>
    
        <div class="sidebar">
            <a href="ALBUMS.php">
                <img src="ICONS/stack.png" alt="About" class="dropdown-icon"> Albums
            </a>
            <a href="MERCH.php">
                <img src="ICONS/bag.png" alt="Merch" class="dropdown-icon"> Merchandise
            </a>
            <a href="">
                <img src="ICONS/people.png" alt="About" class="dropdown-icon"> About us
            </a>
        </div>
    
        <label for="menu-toggle" class="overlay"></label>
    
        <a href="index.php" class="home-logo">
            <img src="ICONS/MAIN_LOGO.jpeg" alt="Home" class="logo-center">
        </a>
    
        <div class="right-icons">
            <?php if (isset($_SESSION['firstname'])): ?>
                <span style="font-family: sans-serif; font-size: 14px;">
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

            <a href="#cart">
                <img src="ICONS/shopping-cart.png" alt="Cart" class="icon">
            </a>
        </div>
    </div>

    <div class="video-container">
        <video autoplay loop muted> 

         
            <source src="AESPAWHIP2.mp4" type="video/mp4">
            
        </video>
    </div>
</main>

<footer class="footer">
        <div class="footer-line"></div>
    
        <div class="social-icons">
            <a href="https://www.facebook.com/aespa.official/"><i class="fab fa-facebook-f"><img src="ICONS/facebook.png"></i></a>
            <a href="https://www.instagram.com/aespa_official/"><i class="fab fa-instagram"><img src="ICONS/instagram.png"></i></a>
            <a href="https://www.youtube.com/aespa"><i class="fab fa-youtube"><img src="ICONS/youtube.png"></i></a>
            <a href="https://www.tiktok.com/@aespa_official"><i class="fab fa-tiktok"><img src="ICONS/tik-tok.png"></i></a>
            <a href="https://x.com/aespa_official"><i class="fab fa-x-twitter"><img src="ICONS/twitter.png"></i></a>
        </div>
    
        <div class="footer-text">
            © 2025, aespa US Official · <a href="#">Privacy Policy</a>
        </div>
    
        <div class="footer-line"></div>
</footer>

<script src="common.js"></script>

</body>
</html>