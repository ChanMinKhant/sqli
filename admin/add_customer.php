<?php
    session_start();
    require '../db.php';
    if (empty($_SESSION['user']) || $_SESSION['user']['role']!=='admin') { header('Location: login.php'); exit; }
    $error='';
    if ($_SERVER['REQUEST_METHOD']==='POST') {
      $username = trim($_POST['username']); $phone=trim($_POST['phone']); $email=trim($_POST['email']); $password=trim($_POST['password']);
      try {
        $stmt = $pdo->prepare("INSERT INTO users (username, phone, email, password) VALUES (:u,:p,:e,:pw)");
        $stmt->execute([':u'=>$username, ':p'=>$phone, ':e'=>$email, ':pw'=>$password]);
        $uid = $pdo->lastInsertId();
        $acct = 10000 + intval($uid);
        $stmt2 = $pdo->prepare("INSERT INTO accounts (account_no, user_id, balance) VALUES (:a,:uid,0.00)");
        $stmt2->execute([':a'=>$acct, ':uid'=>$uid]);
        header('Location: dashboard.php'); exit;
      } catch (Exception $e) { $error = $e->getMessage(); }
    }
    ?>
    <!doctype html><html><head><meta charset="utf-8"><title>Add Customer - SecureBank</title><link rel="stylesheet" href="../assets/style.css"></head><body>
    <nav class="nav"><div class="brand"><img src="../assets/logo.svg" class="logo"> SecureBank - Admin</div></nav>
    <main class="container">
      <h2>Add Customer</h2>
      <?php if ($error): ?><p style="color:red;"><?php echo htmlspecialchars($error); ?></p><?php endif; ?>
      <form method="post">
        <label>Username</label><input name="username" />
        <label>Phone</label><input name="phone" />
        <label>Email</label><input name="email" />
        <label>Password</label><input name="password" />
        <p><button class="btn" type="submit">Add</button></p>
      </form>
    </main></body></html>