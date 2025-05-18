<?php
session_start();
require_once 'conn.php';

header('Content-Type: application/json');

function getCart($userId) {
    global $conn;
    $sql = "SELECT c.*, p.name, p.price, p.image_path 
            FROM cart c 
            JOIN products p ON c.product_id = p.product_id 
            WHERE c.user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $cart = [];
    while($row = $result->fetch_assoc()) {
        $cart[] = $row;
    }
    return $cart;
}

function addToCart($userId, $productId, $quantity) {
    global $conn;
    
    // Check if product exists and has enough quantity
    $sql = "SELECT quantity FROM products WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    
    if(!$product || $product['quantity'] < $quantity) {
        return ['success' => false, 'message' => 'Product not available in requested quantity'];
    }
    
    // Check if item already in cart
    $sql = "SELECT * FROM cart WHERE user_id = ? AND product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $userId, $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows > 0) {
        // Update quantity
        $sql = "UPDATE cart SET quantity = quantity + ? WHERE user_id = ? AND product_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iii", $quantity, $userId, $productId);
    } else {
        // Insert new item
        $sql = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iii", $userId, $productId, $quantity);
    }
    
    if($stmt->execute()) {
        return ['success' => true, 'message' => 'Item added to cart'];
    }
    return ['success' => false, 'message' => 'Error adding item to cart'];
}

function removeFromCart($userId, $productId) {
    global $conn;
    $sql = "DELETE FROM cart WHERE user_id = ? AND product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $userId, $productId);
    
    if($stmt->execute()) {
        return ['success' => true, 'message' => 'Item removed from cart'];
    }
    return ['success' => false, 'message' => 'Error removing item from cart'];
}

function clearCart($userId) {
    global $conn;
    $sql = "DELETE FROM cart WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    
    if($stmt->execute()) {
        return ['success' => true, 'message' => 'Cart cleared'];
    }
    return ['success' => false, 'message' => 'Error clearing cart'];
}

// Handle requests
$action = $_GET['action'] ?? '';
$userId = $_SESSION['user_id'] ?? null;

if(!$userId) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

switch($action) {
    case 'get':
        echo json_encode(['success' => true, 'cart' => getCart($userId)]);
        break;
    case 'add':
        $productId = $_POST['product_id'] ?? 0;
        $quantity = $_POST['quantity'] ?? 1;
        echo json_encode(addToCart($userId, $productId, $quantity));
        break;
    case 'remove':
        $productId = $_POST['product_id'] ?? 0;
        echo json_encode(removeFromCart($userId, $productId));
        break;
    case 'clear':
        echo json_encode(clearCart($userId));
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
?> 