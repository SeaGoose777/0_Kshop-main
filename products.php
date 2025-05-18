<?php
require_once 'conn.php';

// Function to get all products
function getAllProducts() {
    global $conn;
    $sql = "SELECT * FROM products";
    $result = $conn->query($sql);
    $products = [];
    while($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    return $products;
}

// Function to get a single product
function getProduct($id) {
    global $conn;
    $sql = "SELECT * FROM products WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Function to add a new product
function addProduct($name, $price, $quantity, $image_path, $description) {
    global $conn;
    $sql = "INSERT INTO products (name, price, quantity, image_path, description) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sdiss", $name, $price, $quantity, $image_path, $description);
    return $stmt->execute();
}

// Function to update a product
function updateProduct($id, $name, $price, $quantity, $image_path, $description) {
    global $conn;
    $sql = "UPDATE products SET name = ?, price = ?, quantity = ?, image_path = ?, description = ? WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sdissi", $name, $price, $quantity, $image_path, $description, $id);
    return $stmt->execute();
}

// Function to delete a product
function deleteProduct($id) {
    global $conn;
    $sql = "DELETE FROM products WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

// Function to update product quantity
function updateProductQuantity($id, $quantity) {
    global $conn;
    $sql = "UPDATE products SET quantity = quantity - ? WHERE product_id = ? AND quantity >= ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $quantity, $id, $quantity);
    return $stmt->execute();
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch($action) {
        case 'add':
            $name = $_POST['name'] ?? '';
            $price = $_POST['price'] ?? 0;
            $quantity = $_POST['quantity'] ?? 0;
            $image_path = $_POST['image_path'] ?? '';
            $description = $_POST['description'] ?? '';
            
            if(addProduct($name, $price, $quantity, $image_path, $description)) {
                echo json_encode(['success' => true, 'message' => 'Product added successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error adding product']);
            }
            break;
            
        case 'update':
            $id = $_POST['id'] ?? 0;
            $name = $_POST['name'] ?? '';
            $price = $_POST['price'] ?? 0;
            $quantity = $_POST['quantity'] ?? 0;
            $image_path = $_POST['image_path'] ?? '';
            $description = $_POST['description'] ?? '';
            
            if(updateProduct($id, $name, $price, $quantity, $image_path, $description)) {
                echo json_encode(['success' => true, 'message' => 'Product updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error updating product']);
            }
            break;
            
        case 'delete':
            $id = $_POST['id'] ?? 0;
            
            if(deleteProduct($id)) {
                echo json_encode(['success' => true, 'message' => 'Product deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error deleting product']);
            }
            break;
            
        case 'update_quantity':
            $id = $_POST['id'] ?? 0;
            $quantity = $_POST['quantity'] ?? 0;
            
            if(updateProductQuantity($id, $quantity)) {
                echo json_encode(['success' => true, 'message' => 'Quantity updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error updating quantity']);
            }
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? '';
    
    switch($action) {
        case 'get_all':
            echo json_encode(['success' => true, 'products' => getAllProducts()]);
            break;
            
        case 'get':
            $id = $_GET['id'] ?? 0;
            $product = getProduct($id);
            if($product) {
                echo json_encode(['success' => true, 'product' => $product]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Product not found']);
            }
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
}
?> 