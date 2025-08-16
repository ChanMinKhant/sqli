<?php
    session_start();
    require '../db.php';
    if (empty($_SESSION['user']) || $_SESSION['user']['role']!=='admin') { header('Location: login.php'); exit; }
    $id = intval($_GET['id'] ?? 0);
    if (!$id) die('Missing');
    // delete user (accounts + transactions cascade)
    $stmt = $pdo->prepare("DELETE FROM users WHERE id=:id");
    $stmt->execute([':id'=>$id]);
    header('Location: dashboard.php');
    exit;
    ?>