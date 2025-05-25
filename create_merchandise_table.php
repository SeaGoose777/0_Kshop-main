<?php
require_once 'config.php';

try {
    // Create merchandise table if it doesn't exist
    $sql = "CREATE TABLE IF NOT EXISTS merchandise (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        description TEXT,
        price DECIMAL(10,2) NOT NULL,
        image VARCHAR(255) NOT NULL,
        stock INT NOT NULL DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    $pdo->exec($sql);
    echo "Merchandise table created successfully!<br>";

    // Insert sample products if table is empty
    $stmt = $pdo->query("SELECT COUNT(*) FROM merchandise");
    $count = $stmt->fetchColumn();

    if ($count == 0) {
        $products = [
            [
                'name' => 'Official Light Stick',
                'description' => 'Official aespa light stick with customizable colors',
                'price' => 45.00,
                'image' => 'MERCH/lightstick.jpeg',
                'stock' => 10
            ],
            [
                'name' => 'MINI BACKPACK SET',
                'description' => 'aespa \'Armageddon : The Mystery Circle\' POP-UP MD',
                'price' => 35.00,
                'image' => 'MERCH/backpack.jpeg',
                'stock' => 15
            ],
            [
                'name' => 'The 1st Album \'Armageddon\'',
                'description' => '(My Power Ver.)',
                'price' => 25.00,
                'image' => 'MERCH/aespa1stalbum2.jpeg',
                'stock' => 20
            ],
            [
                'name' => 'PINK HOODIE ZIP-UP SET',
                'description' => '2025 aespa LIVE TOUR - SYNK : PARALLEL LINE - ENCORE MD',
                'price' => 55.00,
                'image' => 'MERCH/pinkhoodie.jpeg',
                'stock' => 8
            ]
        ];

        $stmt = $pdo->prepare("INSERT INTO merchandise (name, description, price, image, stock) VALUES (?, ?, ?, ?, ?)");
        
        foreach ($products as $product) {
            $stmt->execute([
                $product['name'],
                $product['description'],
                $product['price'],
                $product['image'],
                $product['stock']
            ]);
        }
        
        echo "Sample products inserted successfully!<br>";
    } else {
        echo "Table already contains products.<br>";
    }

} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?> 