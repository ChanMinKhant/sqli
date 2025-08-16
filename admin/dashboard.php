<?php
    session_start();
    require '../db.php';
    if (empty($_SESSION['user']) || $_SESSION['user']['role']!=='admin') { header('Location: login.php'); exit; }
    $stmt = $pdo->query("SELECT u.id, u.username, u.phone, u.email, a.account_no, a.balance FROM users u JOIN accounts a ON u.id=a.user_id ORDER BY u.id");
    $rows = $stmt->fetchAll();
    ?>
    <!doctype html><html><head><meta charset="utf-8"><title>Admin - SecureBank</title><link rel="stylesheet" href="../assets/style.css"></head><body>
    <nav class="nav"><div class="brand"><img src="../assets/logo.svg" class="logo"> SecureBank - Admin</div>
      <div class="nav-right"><a href="../logout.php">Logout</a></div></nav>
    <main class="container">
      <h2>Customers</h2>
      <p><a class="btn" href="add_customer.php">Add Customer</a></p>
      <table><thead><tr><th>ID</th><th>Username</th><th>Phone</th><th>Email</th><th>Account</th><th>Balance</th><th>Actions</th></tr></thead><tbody>
      <?php foreach ($rows as $r): ?>
        <tr>
          <td><?php echo $r['id']; ?></td>
          <td><?php echo htmlspecialchars($r['username']); ?></td>
          <td><?php echo htmlspecialchars($r['phone']); ?></td>
          <td><?php echo htmlspecialchars($r['email']); ?></td>
          <td><?php echo htmlspecialchars($r['account_no']); ?></td>
          <td><?php echo number_format($r['balance'],2); ?></td>
          <td><a href="customer.php?id=<?php echo $r['id']; ?>">View</a> | <a href="edit_customer.php?id=<?php echo $r['id']; ?>">Edit</a> | <a href="delete_customer.php?id=<?php echo $r['id']; ?>" onclick="return confirm('Delete?')">Delete</a></td>
        </tr>
      <?php endforeach; ?>
      </tbody></table>
    </main></body></html>