<?php
// Database connection configuration
$host = 'localhost';
$user = 'root';
$pass = '1234';
$dbname = 'procurement_db';

try {
    // Create PDO instance
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    // Set error mode to exceptions
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // If connection fails, display a friendly error
    die('Database connection failed: ' . $e->getMessage());
}
