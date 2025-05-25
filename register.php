<?php
require_once 'config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $full_name = $_POST['full_name'];
    
    // Check if username already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->rowCount() > 0) {
        $error = 'Username already exists';
    } else {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert new user
        $stmt = $pdo->prepare("INSERT INTO users (username, password, email, full_name) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$username, $hashed_password, $email, $full_name])) {
            $success = 'Registration successful! You can now login.';
        } else {
            $error = 'Registration failed. Please try again.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Jovelen</title>
    <link rel="stylesheet" href="styles/base.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 20px;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding-top: max(20px, 5vh);
            background: var(--background-color);
            font-family: 'Poppins', sans-serif;
            position: relative;
            overflow-x: hidden;
            overflow-y: auto;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: var(--gradient);
            opacity: 0.1;
            z-index: -1;
        }

        .register-container {
            background: var(--card-background);
            padding: min(2.5rem, 5vw);
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 450px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            animation: fadeIn 0.5s ease-out;
            margin: 0 auto min(20px, 5vh);
            position: relative;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .register-container h2 {
            color: var(--text-color);
            margin-bottom: min(1.5rem, 4vh);
            font-size: clamp(1.5rem, 4vw, 2rem);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .form-group {
            margin-bottom: min(1.5rem, 3vh);
            text-align: left;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-color);
            font-size: clamp(0.8rem, 2vw, 0.9rem);
            opacity: 0.8;
        }

        .form-group input {
            width: 100%;
            padding: clamp(0.8rem, 2vw, 1rem);
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            font-size: clamp(0.9rem, 2vw, 1rem);
            color: var(--text-color);
            transition: all 0.3s ease;
            box-sizing: border-box;
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(108, 99, 255, 0.2);
        }

        .form-group input::placeholder {
            color: rgba(255, 255, 255, 0.4);
        }

        .error {
            background: rgba(255, 107, 107, 0.1);
            color: var(--secondary-color);
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-size: clamp(0.8rem, 2vw, 0.9rem);
            border: 1px solid rgba(255, 107, 107, 0.2);
        }

        .success {
            background: rgba(78, 205, 196, 0.1);
            color: var(--accent-color);
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-size: clamp(0.8rem, 2vw, 0.9rem);
            border: 1px solid rgba(78, 205, 196, 0.2);
        }

        button[type="submit"] {
            background: var(--gradient);
            color: white;
            border: none;
            padding: clamp(0.8rem, 2vw, 1rem) 2rem;
            border-radius: 8px;
            font-size: clamp(0.9rem, 2vw, 1rem);
            font-weight: 500;
            cursor: pointer;
            width: 100%;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 1rem;
        }

        button[type="submit"]:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(108, 99, 255, 0.3);
        }

        .login-link {
            margin-top: 1.5rem;
            color: var(--text-color);
            font-size: clamp(0.8rem, 2vw, 0.9rem);
            opacity: 0.8;
        }

        .login-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .login-link a:hover {
            color: var(--secondary-color);
        }

        .logo {
            max-width: min(120px, 30vw);
            margin-bottom: min(2rem, 4vh);
            filter: drop-shadow(0 0 10px rgba(108, 99, 255, 0.3));
        }

        /* Password strength indicator */
        .password-strength {
            margin-top: 0.5rem;
            height: 4px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 2px;
            overflow: hidden;
        }

        .password-strength-bar {
            height: 100%;
            width: 0;
            transition: all 0.3s ease;
        }

        .weak { background: var(--secondary-color); width: 33.33%; }
        .medium { background: var(--accent-color); width: 66.66%; }
        .strong { background: var(--primary-color); width: 100%; }

        /* Media Queries */
        @media screen and (max-height: 700px) {
            body {
                padding: 10px;
                padding-top: max(10px, 3vh);
            }
            
            .register-container {
                padding: 1.25rem;
                margin-bottom: 10px;
            }
            
            .form-group {
                margin-bottom: 0.75rem;
            }
            
            .logo {
                margin-bottom: 0.75rem;
            }

            .register-container h2 {
                margin-bottom: 1rem;
            }

            button[type="submit"] {
                margin-top: 0.75rem;
            }

            .login-link {
                margin-top: 1rem;
            }
        }

        @media screen and (max-width: 480px) {
            body {
                padding: 10px;
                padding-top: max(10px, 3vh);
            }

            .register-container {
                padding: 1.25rem;
            }
            
            button[type="submit"] {
                padding: 0.8rem 1.5rem;
            }
            
            .form-group {
                margin-bottom: 0.75rem;
            }
        }

        /* For very small heights */
        @media screen and (max-height: 600px) {
            body {
                padding-top: 10px;
            }

            .register-container {
                padding: 1rem;
            }

            .logo {
                max-width: min(100px, 25vw);
                margin-bottom: 0.5rem;
            }

            .register-container h2 {
                margin-bottom: 0.75rem;
                font-size: clamp(1.25rem, 3vw, 1.5rem);
            }

            .form-group {
                margin-bottom: 0.5rem;
            }

            .form-group input {
                padding: 0.6rem 1rem;
            }

            button[type="submit"] {
                padding: 0.6rem 1.25rem;
                margin-top: 0.5rem;
            }

            .login-link {
                margin-top: 0.75rem;
            }
        }

        /* Decorative elements */
        .register-container::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(108, 99, 255, 0.1) 0%, transparent 70%);
            z-index: -1;
            animation: rotate 20s linear infinite;
        }

        @keyframes rotate {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <img src="pic/logo.png" alt="Jovelen" class="logo">
        <h2>Create Account</h2>
        
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="" id="registerForm">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required placeholder="Choose a username">
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required placeholder="Enter your email">
            </div>
            
            <div class="form-group">
                <label for="full_name">Full Name</label>
                <input type="text" id="full_name" name="full_name" required placeholder="Enter your full name">
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required placeholder="Create a password">
                <div class="password-strength">
                    <div class="password-strength-bar"></div>
                </div>
            </div>
            
            <button type="submit">Create Account</button>
        </form>
        
        <p class="login-link">Already have an account? <a href="login.php">Login here</a></p>
    </div>

    <script>
        // Password strength indicator
        const passwordInput = document.getElementById('password');
        const strengthBar = document.querySelector('.password-strength-bar');

        passwordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            
            if (password.length >= 8) strength++;
            if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
            if (password.match(/\d/) && password.match(/[^a-zA-Z\d]/)) strength++;
            
            strengthBar.className = 'password-strength-bar';
            if (strength === 1) strengthBar.classList.add('weak');
            else if (strength === 2) strengthBar.classList.add('medium');
            else if (strength === 3) strengthBar.classList.add('strong');
        });
    </script>
</body>
</html> 