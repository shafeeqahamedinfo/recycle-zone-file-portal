<?php
session_start();
include 'db.php';
$logged = isset($_SESSION['user']);
$user = $logged ? $_SESSION['user'] : null;
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>File Portal</title>
<link rel="stylesheet" href="assets/style.css">
<link rel="icon" href="assets/recycle.png">
</head>
<body>

<div class="header">
  <div class="brand">Recycle zone File Portal</div>
  <!-- <div class="menu-toggle" id="menuToggle">&#9776;</div> -->
  <div class="actions" id="menuItems">
    <a class="btn" href="upload.php">Upload</a>
    <a class="btn" href="view.php">View</a>
    <?php if($logged): ?>
      <span class="btn">Hi, <?=htmlspecialchars($user['username'])?></span>
      <a class="btn" href="logout.php">Logout</a>
      <?php if($user['is_admin']): ?>
        <a class="btn" href="admin.php">Admin</a>
      <?php endif; ?>
    <?php else: ?>
      <a class="btn" href="login.php">Login</a>
      <a class="btn" href="register.php">Register</a>
    <?php endif; ?>
  </div>
</div>

<div class="container">
  <div class="home-grid">
    <a href="upload.php" class="home-card">
      <div class="card-icon">&#8682;</div>
      <div class="card-title">Upload</div>
      <div class="card-desc">Upload files and folders.</div>
    </a>

    <a href="view.php" class="home-card">
      <div class="card-icon">&#128065;</div>
      <div class="card-title">View</div>
      <div class="card-desc">View public or your private files.</div>
    </a>

    <a href="login.php" class="home-card">
      <div class="card-icon">&#128274;</div>
      <div class="card-title">Login</div>
      <div class="card-desc">Access your private folder.</div>
    </a>

    <a href="register.php" class="home-card">
      <div class="card-icon">&#128100;</div>
      <div class="card-title">Register</div>
      <div class="card-desc">Create a new account.</div>
    </a>
  </div>
</div>
<!-- Footer -->
<footer class="footer">
  <div class="container">
    <p>&copy; <?=date('Y')?> Recycle Zone File Portal. All rights reserved.</p>
  </div>
</footer>


<script src="assets/script.js"></script>
</body>
</html>
