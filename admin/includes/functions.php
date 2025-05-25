<?php
// Check if user is admin
function isAdmin() {
    return isset($_SESSION['admin_id']);
}

// Get admin info
function getAdminInfo($pdo) {
    if (!isAdmin()) {
        return null;
    }
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE id = ?");
        $stmt->execute([$_SESSION['admin_id']]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        return null;
    }
}

// Format price
function formatPrice($price) {
    return '$' . number_format($price, 2);
}

// Get stock status
function getStockStatus($stock) {
    if ($stock > 10) {
        return ['class' => 'in-stock', 'text' => 'In Stock'];
    } elseif ($stock > 0) {
        return ['class' => 'low-stock', 'text' => 'Low Stock'];
    } else {
        return ['class' => 'out-of-stock', 'text' => 'Out of Stock'];
    }
}

// Get status badge
function getStatusBadge($status) {
    return $status ? 
        ['class' => 'active', 'text' => 'Active'] : 
        ['class' => 'inactive', 'text' => 'Inactive'];
}

// Sanitize output
function h($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// Generate pagination
function generatePagination($currentPage, $totalPages, $url) {
    $pagination = '<div class="pagination">';
    
    // Previous button
    if ($currentPage > 1) {
        $pagination .= '<a href="' . $url . '?page=' . ($currentPage - 1) . '" class="page-link">&laquo; Previous</a>';
    }
    
    // Page numbers
    for ($i = 1; $i <= $totalPages; $i++) {
        $active = $i == $currentPage ? ' active' : '';
        $pagination .= '<a href="' . $url . '?page=' . $i . '" class="page-link' . $active . '">' . $i . '</a>';
    }
    
    // Next button
    if ($currentPage < $totalPages) {
        $pagination .= '<a href="' . $url . '?page=' . ($currentPage + 1) . '" class="page-link">Next &raquo;</a>';
    }
    
    $pagination .= '</div>';
    return $pagination;
}

// Handle file upload
function handleFileUpload($file, $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'], $maxSize = 5242880) {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('No file uploaded or upload error occurred.');
    }

    $fileInfo = pathinfo($file['name']);
    $extension = strtolower($fileInfo['extension']);

    if (!in_array($extension, $allowedTypes)) {
        throw new Exception('Invalid file type. Allowed types: ' . implode(', ', $allowedTypes));
    }

    if ($file['size'] > $maxSize) {
        throw new Exception('File size exceeds limit. Maximum size: ' . ($maxSize / 1024 / 1024) . 'MB');
    }

    $uploadDir = "../uploads/products/";
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $newFilename = uniqid() . '.' . $extension;
    $targetPath = $uploadDir . $newFilename;

    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        throw new Exception('Failed to move uploaded file.');
    }

    return 'uploads/products/' . $newFilename;
}

// Delete file
function deleteFile($filepath) {
    $fullPath = "../" . $filepath;
    if (file_exists($fullPath)) {
        unlink($fullPath);
    }
}

// Get all categories
function getAllCategories($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

// Get category name
function getCategoryName($pdo, $categoryId) {
    try {
        $stmt = $pdo->prepare("SELECT name FROM categories WHERE id = ?");
        $stmt->execute([$categoryId]);
        return $stmt->fetchColumn() ?: 'Uncategorized';
    } catch (PDOException $e) {
        return 'Uncategorized';
    }
}
?> 