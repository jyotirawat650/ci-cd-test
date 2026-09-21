<?php
$host = 'mysql-db';
$db   = 'test_db';
$user = 'root';
$pass = 'rootpassword';

$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
try {
     $pdo = new PDO($dsn, $user, $pass);
     echo "Database connection successful!";
} catch (\PDOException $e) {
     echo "Connection failed: " . $e->getMessage();
}
?>