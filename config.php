<?php
// Database Configuration
$host = 'localhost';
$db   = 'rsoa_rsoa237_38';
$user = 'rsoa_rsoa237_38';
$pass = '123456';
$charset = 'utf8mb4';
 
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
 
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // In production, you might want to log this and show a friendly message
    die("Database connection failed: " . $e->getMessage());
}
 
// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
 
// Global functions (helper functions)
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}
 
function getRole() {
    return $_SESSION['role'] ?? null;
}
 
function redirect($path) {
    header("Location: $path");
    exit();
}
 
// Security: Prevent XSS
function h($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}
?>
 
