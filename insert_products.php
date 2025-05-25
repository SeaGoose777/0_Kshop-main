<?php
require_once 'config.php';

$products = [
    [
        'name' => 'Official Light Stick',
        'description' => 'Official IVE Light Stick with unique design and features.',
        'price' => 49.99,
        'stock' => 50,
        'image' => 'assets/images/merch/lightstick.jpg'
    ],
    [
        'name' => 'MINI BACKPACK SET',
        'description' => 'Exclusive IVE mini backpack with matching accessories.',
        'price' => 39.99,
        'stock' => 30,
        'image' => 'assets/images/merch/backpack.jpg'
    ],
    [
        'name' => 'IVE T-Shirt',
        'description' => 'Official IVE tour t-shirt with unique design.',
        'price' => 29.99,
        'stock' => 100,
        'image' => 'assets/images/merch/tshirt.jpg'
    ],
    [
        'name' => 'IVE Hoodie',
        'description' => 'Comfortable IVE hoodie perfect for casual wear.',
        'price' => 59.99,
        'stock' => 75,
        'image' => 'assets/images/merch/hoodie.jpg'
    ],
    [
        'name' => 'IVE Cap',
        'description' => 'Stylish IVE cap with embroidered logo.',
        'price' => 24.99,
        'stock' => 60,
        'image' => 'assets/images/merch/cap.jpg'
    ],
    [
        'name' => 'IVE Poster Set',
        'description' => 'Set of 5 high-quality IVE posters.',
        'price' => 19.99,
        'stock' => 200,
        'image' => 'assets/images/merch/posters.jpg'
    ]
];

try {
    $pdo->beginTransaction();

    // Clear existing products
    $pdo->exec("DELETE FROM merchandise");

    // Insert new products
    $stmt = $pdo->prepare("INSERT INTO merchandise (name, description, price, stock, image) VALUES (?, ?, ?, ?, ?)");
    
    foreach ($products as $product) {
        $stmt->execute([
            $product['name'],
            $product['description'],
            $product['price'],
            $product['stock'],
            $product['image']
        ]);
    }

    $pdo->commit();
    echo "Products inserted successfully!";

} catch(PDOException $e) {
    $pdo->rollBack();
    echo "Error: " . $e->getMessage();
}
?> 