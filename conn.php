<?php
$host = 'localhost';
$username = 'root';        
$password = '1234';            
$database = 'kpop';        

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
