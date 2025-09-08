<?php
    session_start();
    require 'db.php';
    require 'lab_mode.php';
    $error = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $id = trim($_POST['id'] ?? '');
      $password = trim($_POST['password'] ?? '');
      if ($id === '' || $password === '') $error = 'Enter credentials';
      else {
        if (isset($lab_mode) && $lab_mode === true) {
          // LAB MODE: SQL Injection vulnerable code (no quotes)
          $sql = "SELECT * FROM users WHERE (username='$id' OR phone='$id' OR email='$id') AND password='$password' LIMIT 1";
          echo htmlspecialchars($sql); 
          $result = $pdo->query($sql);
          $user = $result->fetch();
        } else {
          // Secure code
          $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :id OR phone = :id OR email = :id AND password = :p LIMIT 1");
          $stmt->execute([':id'=>$id, ':p'=>$password]);
          $user = $stmt->fetch();
        }
        if ($user) {
          $_SESSION['user'] = ['id'=>$user['id'], 'username'=>$user['username'], 'role'=>$user['role']];
          header('Location: dashboard.php'); exit;
        } else $error = 'Invalid login';
      }
    }
    ?>
    <!doctype html>
    <html><head><meta charset="utf-8"><title>Login - SecureBank</title><link rel="stylesheet" href="assets/style.css"></head><body>
    <nav class="nav"><div class="brand"><img src="assets/logo.svg" class="logo"> SecureBank</div></nav>
    <main class="container">
      <h2>Customer Login</h2>
      <?php if ($error): ?><p style="color:red;"><?php echo htmlspecialchars($error); ?></p><?php endif; ?>
      <form method="post">
        <label>Username / Phone / Email</label><input name="id" />
        <label>Password</label><input name="password" type="password" />
        <p><button class="btn" type="submit">Login</button></p>
      </form>
      <p class="small">Admin? <a href="admin/login.php">Admin Login</a></p>
    </main></body></html>

    <!-- 
    inject code - username') #
    
    vunlarable - user can login just knowing username, phone, or email
    -->