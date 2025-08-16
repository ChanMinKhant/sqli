<?php
    session_start();
    require 'db.php';
    $error = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $username = trim($_POST['username'] ?? '');
      $phone = trim($_POST['phone'] ?? '');
      $email = trim($_POST['email'] ?? '');
      $password = trim($_POST['password'] ?? '');
      if (!$username || !$phone || !$email || !$password) {
        $error = 'All fields are required.';
      } else {
        // create user
        $stmt = $pdo->prepare("INSERT INTO users (username, phone, email, password) VALUES (:u,:p,:e,:pw)");
        try {
          $stmt->execute([':u'=>$username, ':p'=>$phone, ':e'=>$email, ':pw'=>$password]);
          $user_id = $pdo->lastInsertId();
          // create account number (simple incremental)
          $acct = 10000 + intval($user_id);
          $stmt2 = $pdo->prepare("INSERT INTO accounts (account_no, user_id, balance) VALUES (:a,:uid,0.00)");
          $stmt2->execute([':a'=>$acct, ':uid'=>$user_id]);
          $_SESSION['user'] = ['id'=>$user_id, 'username'=>$username];
          header('Location: dashboard.php'); exit;
        } catch (Exception $e) {
          $error = 'Could not create account: ' . $e->getMessage();
        }
      }
    }
    ?>
    <!doctype html>
    <html>
    <head><meta charset="utf-8"><title>Register - SecureBank</title><link rel="stylesheet" href="assets/style.css"></head>
    <body>
    <nav class="nav"><div class="brand"><img src="assets/logo.svg" class="logo"> SecureBank</div></nav>
    <main class="container">
      <h2>Register</h2>
      <?php if ($error): ?><p style="color:red;"><?php echo htmlspecialchars($error); ?></p><?php endif; ?>
      <form method="post">
        <label>Username</label><input name="username" />
        <label>Phone</label><input name="phone" />
        <label>Email</label><input name="email" type="email" />
        <label>Password</label><input name="password" type="password" />
        <p><button class="btn" type="submit">Create Account</button></p>
      </form>
      <p class="small">Already have an account? <a href="login.php">Login</a></p>
    </main>
    </body>
    </html>