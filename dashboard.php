<?php
    session_start();
    if (empty($_SESSION['user'])) { header('Location: login.php'); exit; }
    require 'db.php';
    $user = $_SESSION['user'];
    // fetch accounts for user
    $stmt = $pdo->prepare("SELECT account_no, balance FROM accounts WHERE user_id = :uid");
    $stmt->execute([':uid'=>$user['id']]);
    $accounts = $stmt->fetchAll();
    ?>
    <!doctype html>
    <html><head><meta charset="utf-8"><title>Dashboard - SecureBank</title><link rel="stylesheet" href="assets/style.css"></head><body>
    <nav class="nav"><div class="brand"><img src="assets/logo.svg" class="logo"> SecureBank</div>
      <div class="nav-right"><a href="transfer.php">Transfer</a> <a href="transactions.php">Transactions</a> <a href="logout.php">Logout</a></div>
    </nav>
    <main class="container">
      <div class="topbar"><h2>Welcome, <?php echo htmlspecialchars($user['username']); ?></h2><div class="small">Account summary</div></div>
      <?php foreach ($accounts as $a): ?>
        <div class="card">
          <strong>Account #: <?php echo htmlspecialchars($a['account_no']); ?></strong>
          <p>Balance: $<?php echo number_format($a['balance'],2); ?></p>
          <p><a href="transactions.php?account=<?php echo $a['account_no']; ?>">View Transactions</a></p>
        </div>
      <?php endforeach; ?>
    </main></body></html>