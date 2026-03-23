<?php
$host = 'localhost';  // vagy a szervered neve
$username = 'root';   // Az adatbázis felhasználó neve
$password = '';       // Az adatbázis jelszó
$dbname = 'projektnorbimark'; // Az adatbázis neve

// Kapcsolódás létrehozása
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, array(
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ));
    
    // Set UTF-8 encoding for the connection
    $pdo->exec("SET NAMES utf8mb4");
    $pdo->exec("SET CHARACTER SET utf8mb4");
    $pdo->exec("SET CHARACTER_SET_CONNECTION=utf8mb4");
    $pdo->exec("SET CHARACTER_SET_RESULTS=utf8mb4");
    $pdo->exec("SET CHARACTER_SET_CLIENT=utf8mb4");
    $pdo->exec("SET CHARACTER_SET_SERVER=utf8mb4");
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
