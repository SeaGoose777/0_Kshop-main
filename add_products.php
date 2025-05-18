<?php
require_once 'conn.php';

// Create cart table if it doesn't exist
$createCartTableSQL = "CREATE TABLE IF NOT EXISTS cart (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (product_id) REFERENCES products(id)
)";

if ($conn->query($createCartTableSQL)) {
    echo "Cart table created or already exists.<br>";
} else {
    echo "Error creating cart table: " . $conn->error . "<br>";
    exit;
}

// Array of products to add
$products = [
    [
        'name' => 'The Whiplash - The 5th Mini Album (GISELLE Exclusive Signed Ver.)',
        'price' => 450.00,
        'quantity' => 100,
        'image_path' => 'MERCH/aespaGISELLE.jpeg',
        'description' => 'GISELLE Exclusive Signed Version of The Whiplash Mini Album'
    ],
    [
        'name' => 'Whiplash - The 5th Mini Album (KARINA Exclusive Signed Ver.)',
        'price' => 450.00,
        'quantity' => 100,
        'image_path' => 'MERCH/aespaKARINA.jpeg',
        'description' => 'KARINA Exclusive Signed Version of The Whiplash Mini Album'
    ],
    [
        'name' => 'Whiplash - The 5th Mini Album (WINTER Exclusive Signed Ver.)',
        'price' => 450.00,
        'quantity' => 100,
        'image_path' => 'MERCH/aespaWINTER.jpeg',
        'description' => 'WINTER Exclusive Signed Version of The Whiplash Mini Album'
    ],
    [
        'name' => 'Whiplash - The 5th Mini Album (NINGNING Exclusive Signed Ver.)',
        'price' => 450.00,
        'quantity' => 100,
        'image_path' => 'MERCH/aespaNINGNING.jpeg',
        'description' => 'NINGNING Exclusive Signed Version of The Whiplash Mini Album'
    ],
    [
        'name' => "The 1st Album 'Armageddon' (My Power Ver.)",
        'price' => 1400.00,
        'quantity' => 50,
        'image_path' => 'MERCH/aespa1stalbum2.jpeg',
        'description' => 'AESPA 1st Album Armageddon My Power Version'
    ],
    [
        'name' => "MINI BACKPACK SET - aespa 'Armageddon : The Mystery Circle' POP-UP MD",
        'price' => 3500.00,
        'quantity' => 30,
        'image_path' => 'MERCH/backpack.jpeg',
        'description' => 'Official AESPA Mini Backpack Set from the Armageddon Pop-up Store'
    ],
    [
        'name' => 'Official Fanlight',
        'price' => 2600.00,
        'quantity' => 40,
        'image_path' => 'MERCH/lightstick.jpeg',
        'description' => 'Official AESPA Fanlight'
    ],
    [
        'name' => 'Pinkhoodie',
        'price' => 3500.00,
        'quantity' => 25,
        'image_path' => 'MERCH/pinkhoodie.jpeg',
        'description' => 'Official AESPA Pink Hoodie'
    ]
];

// First, create the products table if it doesn't exist
$createTableSQL = "CREATE TABLE IF NOT EXISTS products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    quantity INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    description TEXT
)";

if ($conn->query($createTableSQL)) {
    echo "Products table created or already exists.<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
    exit;
}

// Prepare the insert statement
$stmt = $conn->prepare("INSERT INTO products (name, price, quantity, image_path, description) VALUES (?, ?, ?, ?, ?)");

// Insert each product
$successCount = 0;
foreach ($products as $product) {
    $stmt->bind_param("sdiss", 
        $product['name'],
        $product['price'],
        $product['quantity'],
        $product['image_path'],
        $product['description']
    );
    
    if ($stmt->execute()) {
        $successCount++;
        echo "Added product: " . $product['name'] . "<br>";
    } else {
        echo "Error adding product " . $product['name'] . ": " . $stmt->error . "<br>";
    }
}

echo "<br>Successfully added $successCount products to the database.";

$stmt->close();
$conn->close();
?> 