<?php
    session_start();
    require '../db.php';
    if (empty($_SESSION['user']) || $_SESSION['user']['role']!=='admin') { header('Location: login.php'); exit; }
    $account_no = $_POST['account_no'] ?? ''; $amount = floatval($_POST['amount'] ?? 0); $desc = trim($_POST['desc'] ?? 'Admin adjustment');
    if (!$account_no) die('Missing');
    try {
      $pdo->beginTransaction();
      $stmt = $pdo->prepare("SELECT balance FROM accounts WHERE account_no=:a FOR UPDATE");
      $stmt->execute([':a'=>$account_no]);
      $r = $stmt->fetch();
      if (!$r) throw new Exception('Account not found');
      $new = $r['balance'] + $amount;
      $stmt2 = $pdo->prepare("UPDATE accounts SET balance=:b WHERE account_no=:a");
      $stmt2->execute([':b'=>$new, ':a'=>$account_no]);
      $stmt3 = $pdo->prepare("INSERT INTO transactions (account_no, description, amount, balance_after) VALUES (:a,:d,:amt,:bal)");
      $stmt3->execute([':a'=>$account_no, ':d'=>$desc, ':amt'=>$amount, ':bal'=>$new]);
      $pdo->commit();
      header('Location: dashboard.php'); exit;
    } catch (Exception $e) {
      $pdo->rollBack();
      die('Error: '.$e->getMessage());
    }
    ?>