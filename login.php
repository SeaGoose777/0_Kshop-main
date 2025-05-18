<?php
session_start();

// Connect to DB
$conn = new mysqli("localhost", "root", "1234", "kpop");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get POST values
$email = $_POST['email'];
$password = $_POST['password'];

// Check user
$stmt = $conn->prepare("SELECT id, firstname, password FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->bind_result($id, $firstname, $hashedPassword);
    $stmt->fetch();

    if (password_verify($password, $hashedPassword)) {
        $_SESSION['user_id'] = $id;
        $_SESSION['user_name'] = $firstname;
        header("Location: index.php"); // Success → redirect
        exit;
    } else {
        echo "Incorrect password.";
    }
} else {
    echo "User not found.";
}

$stmt->close();
$conn->close();
?>
