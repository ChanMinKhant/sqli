<?php
    session_start();
    if (empty($_SESSION['user'])) { header('Location: login.php'); exit; }
    require 'db.php';
    $user = $_SESSION['user'];
    $account = $_GET['account'] ?? null;
    // if account not provided, pick user's first account
    if (!$account) {
      $stmt = $pdo->prepare("SELECT account_no FROM accounts WHERE user_id=:uid LIMIT 1");
      $stmt->execute([':uid'=>$user['id']]);
      $r = $stmt->fetch();
      $account = $r ? $r['account_no'] : null;
    }
    if (!$account) { die('No account found.'); }
    $q = trim($_GET['q'] ?? '');
    if ($q !== '') {
      $stmt = $pdo->prepare("SELECT * FROM transactions WHERE account_no=:a AND (description LIKE :q) ORDER BY date DESC");
      $stmt->execute([':a'=>$account, ':q'=>"%$q%"]);
    } else {
      $stmt = $pdo->prepare("SELECT * FROM transactions WHERE account_no=:a ORDER BY date DESC");
      $stmt->execute([':a'=>$account]);
    }
    $txs = $stmt->fetchAll();
    ?>
    <!doctype html>
    <html><head><meta charset="utf-8"><title>Transactions - SecureBank</title><link rel="stylesheet" href="assets/style.css"></head><body>
    <nav class="nav"><div class="brand"><img src="assets/logo.svg" class="logo"> SecureBank</div>
      <div class="nav-right"><a href="dashboard.php">Dashboard</a> <a href="transfer.php">Transfer</a> <a href="logout.php">Logout</a></div>
    </nav>
    <main class="container">
      <h2>Transactions for Account <?php echo htmlspecialchars($account); ?></h2>
      <form method="get">
        <input type="hidden" name="account" value="<?php echo htmlspecialchars($account); ?>" />
        <label>Search description</label><input name="q" value="<?php echo htmlspecialchars($q); ?>" />
        <p><button class="btn" type="submit">Filter</button></p>
      </form>
      <table><thead><tr><th>Date</th><th>Description</th><th>Amount</th><th>Balance After</th></tr></thead><tbody>
      <?php foreach ($txs as $t): ?>
        <tr>
          <td><?php echo htmlspecialchars($t['date']); ?></td>
          <td><a href="transaction.php?id=<?php echo $t['id']; ?>"><?php echo htmlspecialchars($t['description']); ?></a></td>
          <td><?php echo number_format($t['amount'],2); ?></td>
          <td><?php echo number_format($t['balance_after'],2); ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody></table>
    </main></body></html>