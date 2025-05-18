<?php
session_start(); // Start the session

// Destroy all session data
session_unset(); 
session_destroy();

// Redirect to homepage (change if needed)
header("Location: index.php");
exit();
?>
