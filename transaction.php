<?php
session_start();
if (empty($_SESSION['user'])) { header('Location: login.php'); exit; }
require 'db.php';
require './lab_mode.php';

$id = $_GET['id'] ?? 0; 
if (!$id) die('Invalid id');

if (isset($lab_mode) && $lab_mode === true) {
    // ❌ Vulnerable SQL
    $sql = "SELECT * FROM transactions WHERE id='$id' LIMIT 1";
    echo $sql;
    $result = $pdo->query($sql);
    $t = $result->fetch();
    if (!$t) die('Not found');
} else {
    // ✅ Secure code
    $stmt = $pdo->prepare("SELECT t.*, a.user_id 
            FROM transactions t 
            JOIN accounts a ON t.account_no=a.account_no 
            WHERE t.id=:id 
            LIMIT 1");
    $stmt->execute([':id'=>$id]);
    $t = $stmt->fetch();
    if (!$t) die('Not found');
    if ($t['user_id'] != $_SESSION['user']['id'] && $_SESSION['user']['role'] !== 'admin') die('Access denied');
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Transaction Detail - SecureBank</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<nav class="nav"><div class="brand"><img src="assets/logo.svg" class="logo"> SecureBank</div></nav>
<main class="container">
  <h2>Transaction #<?php echo $t['id']; ?></h2>
  <p><strong>Date:</strong> <?php echo htmlspecialchars($t['date']); ?></p>
  <p><strong>Description:</strong> <?php echo htmlspecialchars($t['description']); ?></p>
  <p><strong>Amount:</strong> $<?php echo number_format($t['amount'],2); ?></p>
  <p><strong>Balance After:</strong> $<?php echo number_format($t['balance_after'],2); ?></p>
  <p><a href="transactions.php?account=<?php echo htmlspecialchars($t['account_no']); ?>">← Back</a></p>
</main>
</body>
</html>

