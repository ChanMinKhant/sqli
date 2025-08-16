<?php
    session_start();
    if (!empty($_SESSION['user'])) {
      header('Location: dashboard.php');
      exit;
    }
    ?>
    <!doctype html>
    <html>
    <head>
      <meta charset="utf-8">
      <title>SecureBank</title>
      <link rel="stylesheet" href="assets/style.css">
    </head>
    <body>
      <nav class="nav">
        <div class="brand">
          <img src="assets/logo.svg" alt="SecureBank" class="logo"> SecureBank
        </div>
        <div class="nav-right">
          <a href="login.php">Login</a>
          <a href="register.php">Register</a>
          <a href="admin/login.php">Admin</a>
        </div>
      </nav>
      <main class="container">
        <h1>Welcome to SecureBank</h1>
        <p>SecureBank is a demo online banking portal for academic purposes.</p>
        <p><a class="btn" href="register.php">Create Account</a> <a class="btn ghost" href="login.php">Customer Login</a></p>
      </main>
      <footer class="footer">SecureBank &copy; 2025</footer>
    </body>
    </html>