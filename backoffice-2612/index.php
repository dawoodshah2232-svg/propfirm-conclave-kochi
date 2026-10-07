<?php
// Admin sign-in.
require_once __DIR__ . '/_auth.php';
if (current_admin()) { header('Location: ' . ADMIN_BASE . '/dashboard.php'); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';
    $user = q1('SELECT * FROM admin_users WHERE email = ?', [$email]);
    if ($user && password_verify($pass, $user['pass_hash'])) {
        login_admin($user);
        header('Location: ' . ADMIN_BASE . '/dashboard.php');
        exit;
    }
    $error = 'Invalid email or password.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Sign in · Conclave Admin</title>
<link rel="stylesheet" href="<?= ADMIN_BASE ?>/assets/admin.css">
</head>
<body>
<div class="login-wrap">
  <form class="login-box" method="post">
    <span class="brand-mark" style="width:52px;height:52px;font-size:18px">PC</span>
    <h1>Admin Portal</h1>
    <p>PropFirm Conclave Kochi 2026 — manage everything from here.</p>
    <?php if ($error): ?><div class="alert alert-err"><?= esc($error) ?></div><?php endif; ?>
    <div class="field" style="margin-bottom:16px">
      <label>Email</label>
      <input type="email" name="email" required autocomplete="username" value="<?= esc($_POST['email'] ?? '') ?>">
    </div>
    <div class="field" style="margin-bottom:22px">
      <label>Password</label>
      <input type="password" name="password" required autocomplete="current-password">
    </div>
    <button class="btn btn-gold" type="submit" style="width:100%;justify-content:center">Sign In</button>
  </form>
</div>
</body>
</html>
