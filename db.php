<?php
    // db.php - PDO connection
    $host = '127.0.0.1';
    $db   = 'securebank';
    $user = 'root';
    $pass = '';
    $dsn  = "mysql:host=$host;dbname=$db;charset=utf8mb4";
    try {
      $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      ]);
    } catch (Exception $e) {
      die('DB connection error: ' . $e->getMessage());
    }
    ?>