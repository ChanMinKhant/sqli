<?php
    session_start();
    require '../db.php';
    require '../lab_mode.php';
    $error = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $username = $_POST['username'] ?? '';
      $password = $_POST['password'] ?? '';
      if (isset($lab_mode) && $lab_mode === true) {
        // LAB MODE: SQL Injection vulnerable code (no quotes)
        $sql = "SELECT * FROM users WHERE username='$username' AND password='$password' AND role='admin' LIMIT 1";
        // output the query
        echo htmlspecialchars($sql); 
        $result = $pdo->query($sql);
        $user = $result->fetch();
        echo htmlspecialchars($user ? 'User found' : 'No user found'); 
      } else {
        // Secure code
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username=:u AND password=:p AND role='admin' LIMIT 1");
        $stmt->execute([':u'=>$username, ':p'=>$password]);
        $user = $stmt->fetch();
      }
      if ($user) {
        $_SESSION['user'] = ['id'=>$user['id'], 'username'=>$user['username'], 'role'=>$user['role']];
        header('Location: dashboard.php'); exit;
      } else $error = 'Invalid admin login';
    }
    ?>
    <!doctype html><html><head><meta charset="utf-8"><title>Admin Login - SecureBank</title><link rel="stylesheet" href="../assets/style.css"></head><body>
    <nav class="nav"><div class="brand"><img src="../assets/logo.svg" class="logo"> SecureBank - Admin</div></nav>
    <main class="container">
      <h2>Admin Login</h2>
      <?php if ($error): ?><p style="color:red;"><?php echo htmlspecialchars($error); ?></p><?php endif; ?>
      <form method="post">
        <label>Username</label><input name="username" />
        <label>Password</label><input name="password" type="password" />
        <p><button class="btn" type="submit">Login</button></p>
      </form>
    </main></body></html>

    <!-- 
    inject code 
    
    1 OR 1=1 AND role='admin' LIMIT 1 -- 
     
    -->