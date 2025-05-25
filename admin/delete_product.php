<?php
require_once '../config.php';
require_once 'includes/functions.php';

// Check if user is admin
if (!isAdmin()) {
    header('Location: login.php');
    exit();
}

// Get product ID from URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    header('Location: products.php');
    exit();
}

try {
    // Start transaction
    $pdo->beginTransaction();
    
    // Get product image before deleting
    $stmt = $pdo->prepare("SELECT image FROM merchandise WHERE id = ?");
    $stmt->execute([$id]);
    $image = $stmt->fetchColumn();
    
    // Delete related cart items
    $stmt = $pdo->prepare("DELETE FROM cart WHERE product_id = ?");
    $stmt->execute([$id]);
    
    // Delete related order items
    $stmt = $pdo->prepare("DELETE FROM order_items WHERE product_id = ?");
    $stmt->execute([$id]);
    
    // Delete product
    $stmt = $pdo->prepare("DELETE FROM merchandise WHERE id = ?");
    $stmt->execute([$id]);
    
    // Delete image file if exists
    if ($image && file_exists("../" . $image)) {
        unlink("../" . $image);
    }
    
    // Commit transaction
    $pdo->commit();
    
    header('Location: products.php?success=Product deleted successfully');
} catch (PDOException $e) {
    // Rollback transaction on error
    $pdo->rollBack();
    header('Location: products.php?error=Error deleting product: ' . urlencode($e->getMessage()));
}
exit(); 