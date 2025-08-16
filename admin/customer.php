<?php
    session_start();
    require '../db.php';
    if (empty($_SESSION['user']) || $_SESSION['user']['role']!=='admin') { header('Location: login.php'); exit; }
    $id = intval($_GET['id'] ?? 0);
    if (!$id) die('Missing id');
    $stmt = $pdo->prepare("SELECT u.*, a.account_no, a.balance FROM users u JOIN accounts a ON u.id=a.user_id WHERE u.id=:id LIMIT 1");
    $stmt->execute([':id'=>$id]);
    $r = $stmt->fetch();
    if (!$r) die('Not found');
    ?>
    <!doctype html><html><head><meta charset="utf-8"><title>Customer - SecureBank</title><link rel="stylesheet" href="../assets/style.css"></head><body>
    <nav class="nav"><div class="brand"><img src="../assets/logo.svg" class="logo"> SecureBank - Admin</div>
      <div class="nav-right"><a href="dashboard.php">Back</a> <a href="../logout.php">Logout</a></div></nav>
    <main class="container">
      <h2>Customer: <?php echo htmlspecialchars($r['username']); ?></h2>
      <p><strong>Phone:</strong> <?php echo htmlspecialchars($r['phone']); ?></p>
      <p><strong>Email:</strong> <?php echo htmlspecialchars($r['email']); ?></p>
      <p><strong>Account:</strong> <?php echo htmlspecialchars($r['account_no']); ?> — Balance: $<?php echo number_format($r['balance'],2); ?></p>
      <h3>Transactions</h3>
      <?php
      $stmt2 = $pdo->prepare("SELECT * FROM transactions WHERE account_no=:a ORDER BY date DESC");
      $stmt2->execute([':a'=>$r['account_no']]);
      $txs = $stmt2->fetchAll();
      ?>
      <table><thead><tr><th>Date</th><th>Description</th><th>Amount</th><th>Balance</th></tr></thead><tbody>
      <?php foreach ($txs as $t): ?>
        <tr><td><?php echo $t['date']; ?></td><td><?php echo htmlspecialchars($t['description']); ?></td><td><?php echo number_format($t['amount'],2); ?></td><td><?php echo number_format($t['balance_after'],2); ?></td></tr>
      <?php endforeach; ?>
      </tbody></table>
      <h3>Admin Actions</h3>
      <form method="post" action="adjust_balance.php">
        <input type="hidden" name="account_no" value="<?php echo $r['account_no']; ?>" />
        <label>Adjust Amount (positive to add, negative to remove)</label>
        <input name="amount" />
        <label>Reason/Description</label>
        <input name="desc" />
        <p><button class="btn" type="submit">Apply</button></p>
      </form>
    </main></body></html>