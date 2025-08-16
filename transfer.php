<?php
    session_start();
    if (empty($_SESSION['user'])) { header('Location: login.php'); exit; }
    require 'db.php';
    $user = $_SESSION['user'];
    $msg = '';
    // fetch user's account (single account for simplicity)
    $stmt = $pdo->prepare("SELECT account_no, balance FROM accounts WHERE user_id = :uid LIMIT 1");
    $stmt->execute([':uid'=>$user['id']]);
    $acct = $stmt->fetch();
    if (!$acct) { die('No account found.'); }
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $to = trim($_POST['to'] ?? '');
      $amount = floatval($_POST['amount'] ?? 0);
      $desc = trim($_POST['description'] ?? '');
      if ($to === '' || $amount <= 0) $msg = 'Enter valid recipient and amount.';
      elseif ($amount > $acct['balance']) $msg = 'Insufficient funds.';
      else {
        // find recipient by username/phone/email
        $stmt = $pdo->prepare("SELECT u.id, a.account_no, a.balance FROM users u JOIN accounts a ON u.id=a.user_id WHERE u.username=:t OR u.phone=:t OR u.email=:t LIMIT 1");
        $stmt->execute([':t'=>$to]);
        $r = $stmt->fetch();
        if (!$r) { $msg = 'Recipient not found.'; }
        else {
          // perform transfer in a transaction
          try {
            $pdo->beginTransaction();
            // debit sender
            $new_sender_bal = $acct['balance'] - $amount;
            $stmt1 = $pdo->prepare("UPDATE accounts SET balance=:b WHERE account_no=:a");
            $stmt1->execute([':b'=>$new_sender_bal, ':a'=>$acct['account_no']]);
            $stmt2 = $pdo->prepare("INSERT INTO transactions (account_no, description, amount, balance_after) VALUES (:a,:d,-:amt,:bal)");
            $stmt2->execute([':a'=>$acct['account_no'], ':d'=>$desc ?: 'Transfer to '.$to, ':amt'=>$amount, ':bal'=>$new_sender_bal]);
            # credit recipient
            $new_rec_bal = $r['balance'] + $amount;
            $stmt3 = $pdo->prepare("UPDATE accounts SET balance=:b WHERE account_no=:a");
            $stmt3->execute([':b'=>$new_rec_bal, ':a'=>$r['account_no']]);
            $stmt4 = $pdo->prepare("INSERT INTO transactions (account_no, description, amount, balance_after) VALUES (:a,:d,:amt,:bal)");
            $stmt4->execute([':a'=>$r['account_no'], ':d'=>$desc ?: 'Transfer from '.$user['username'], ':amt'=>$amount, ':bal'=>$new_rec_bal]);
            $pdo->commit();
            $msg = 'Transfer successful.';
            // refresh account
            $stmt = $pdo->prepare("SELECT account_no, balance FROM accounts WHERE user_id = :uid LIMIT 1");
            $stmt->execute([':uid'=>$user['id']]);
            $acct = $stmt->fetch();
          } catch (Exception $e) {
            $pdo->rollBack();
            $msg = 'Transfer failed: ' . $e->getMessage();
          }
        }
      }
    }
    ?>
    <!doctype html>
    <html><head><meta charset="utf-8"><title>Transfer - SecureBank</title><link rel="stylesheet" href="assets/style.css"></head><body>
    <nav class="nav"><div class="brand"><img src="assets/logo.svg" class="logo"> SecureBank</div>
      <div class="nav-right"><a href="dashboard.php">Dashboard</a> <a href="transactions.php">Transactions</a> <a href="logout.php">Logout</a></div>
    </nav>
    <main class="container">
      <h2>Transfer Funds</h2>
      <?php if ($msg): ?><p style="color:green;"><?php echo htmlspecialchars($msg); ?></p><?php endif; ?>
      <p>Your Account: <strong><?php echo $acct['account_no']; ?></strong> — Balance: $<?php echo number_format($acct['balance'],2); ?></p>
      <form method="post">
        <label>Recipient (username/phone/email)</label><input name="to" />
        <label>Amount</label><input name="amount" type="text" />
        <label>Description</label><input name="description" />
        <p><button class="btn" type="submit">Send</button></p>
      </form>
    </main></body></html>