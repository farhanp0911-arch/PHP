<?php

// Load environment variables
require 'vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Database connection using PDO for prepared statements
try {
    $pdo = new PDO('mysql:host=' . getenv('DB_HOST') . ';dbname=' . getenv('DB_NAME'), getenv('DB_USER'), getenv('DB_PASS'));
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch (PDOException $e) {
    error_log($e->getMessage());
    die("Database connection failed.");
}

// Validate and sanitize input
$username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
$password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

// Check if inputs are valid
if (!$username || !$password) {
    die("Invalid input");
}

// Using prepared statements to prevent SQL injection
$stmt = $pdo->prepare('SELECT * FROM users WHERE username = :username');
$stmt->bindParam(':username', $username);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Verify password and handle logic accordingly
if ($user && password_verify($password, $user['password'])) {
    // Start session and redirect or set user state
} else {
    die("Invalid credentials");
}

// Output safely
echo htmlspecialchars($username, ENT_QUOTES, 'UTF-8');

?>