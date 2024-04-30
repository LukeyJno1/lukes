<?php
$host = 'localhost';
$db_name = 'lukejear_toolhire';
$username = 'lukejear_lukejear';
$password = 'AlfieJohn1';

try {
    $dsn = "mysql:host={$host};dbname={$db_name}";
    $conn = new PDO($dsn, $username, $password);
    echo "Database connection successful!";
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage();
}