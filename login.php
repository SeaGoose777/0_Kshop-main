<?php
require_once 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header('Location: index.php');
        exit();
    } else {
        $error = 'Invalid username or password';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>aespa Login</title>
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
            align-items: center;
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

        .login-container {
            background: var(--card-background);
            padding: min(2.5rem, 5vw);
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 400px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            animation: fadeIn 0.5s ease-out;
            margin: auto;
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

        .login-container h2 {
            color: var(--text-color);
            margin-bottom: 1.5rem;
            font-size: clamp(1.5rem, 4vw, 2rem);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .form-group {
            margin-bottom: 1.5rem;
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

        .register-link {
            margin-top: 1.5rem;
            color: var(--text-color);
            font-size: clamp(0.8rem, 2vw, 0.9rem);
            opacity: 0.8;
        }

        .register-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .register-link a:hover {
            color: var(--secondary-color);
        }

        .logo {
            max-width: min(120px, 30vw);
            margin-bottom: 2rem;
            filter: drop-shadow(0 0 10px rgba(108, 99, 255, 0.3));
        }

        /* Media Queries */
        @media screen and (max-height: 700px) {
            body {
                padding: 10px;
            }
            
            .login-container {
                padding: 1.5rem;
            }
            
            .form-group {
                margin-bottom: 1rem;
            }
            
            .logo {
                margin-bottom: 1rem;
            }
        }

        @media screen and (max-width: 480px) {
            .login-container {
                padding: 1.5rem;
            }
            
            button[type="submit"] {
                padding: 0.8rem 1.5rem;
            }
        }

        /* Decorative elements */
        .login-container::before {
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
    <div class="login-container">
        <img src="pic/logo.png" alt="Jovelen" class="logo">
        <h2>Welcome Back</h2>
        
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required placeholder="Enter your username">
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required placeholder="Enter your password">
            </div>
            
            <button type="submit">Login</button>
        </form>
        
        <p class="register-link">Don't have an account? <a href="register.php">Register here</a></p>
    </div>
</body>
</html> 