<?php
    session_start();
    require '../db.php';
    if (empty($_SESSION['user']) || $_SESSION['user']['role']!=='admin') { header('Location: login.php'); exit; }
    $id = intval($_GET['id'] ?? 0);
    if (!$id) die('Missing');
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id=:id LIMIT 1");
    $stmt->execute([':id'=>$id]); $u = $stmt->fetch(); if (!$u) die('Not found');
    $error='';
    if ($_SERVER['REQUEST_METHOD']==='POST') {
      $username = trim($_POST['username']); $phone=trim($_POST['phone']); $email=trim($_POST['email']);
      try {
        $stmt = $pdo->prepare("UPDATE users SET username=:u, phone=:p, email=:e WHERE id=:id");
        $stmt->execute([':u'=>$username, ':p'=>$phone, ':e'=>$email, ':id'=>$id]);
        header('Location: dashboard.php'); exit;
      } catch (Exception $e) { $error = $e->getMessage(); }
    }
    ?>
    <!doctype html><html><head><meta charset="utf-8"><title>Edit Customer - SecureBank</title><link rel="stylesheet" href="../assets/style.css"></head><body>
    <nav class="nav"><div class="brand"><img src="../assets/logo.svg" class="logo"> SecureBank - Admin</div></nav>
    <main class="container">
      <h2>Edit Customer</h2>
      <?php if ($error): ?><p style="color:red;"><?php echo htmlspecialchars($error); ?></p><?php endif; ?>
      <form method="post">
        <label>Username</label><input name="username" value="<?php echo htmlspecialchars($u['username']); ?>" />
        <label>Phone</label><input name="phone" value="<?php echo htmlspecialchars($u['phone']); ?>" />
        <label>Email</label><input name="email" value="<?php echo htmlspecialchars($u['email']); ?>" />
        <p><button class="btn" type="submit">Save</button></p>
      </form>
    </main></body></html>