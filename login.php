<?php
session_start();
include 'db.php';

if($_SERVER['REQUEST_METHOD']=='POST'){
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username=? LIMIT 1");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if($user && password_verify($password, $user['password'])){
        $_SESSION['user'] = [
            'id'=>$user['id'],
            'username'=>$user['username'],
            'is_admin'=>$user['is_admin']
        ];
        header("Location: index.php"); exit;
    } else {
        $error="Invalid username or password";
    }
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Login</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
  <div class="header"><div class="brand">Recycle zone File Portal</div><div class="actions"><a class="btn" href="index.php">Home</a></div></div>

<div class="container" style="display:flex;justify-content:center;align-items:center;min-height:100vh;">
  <div class="card" style="margin:auto;">
    <h3>Login</h3><br>
    <?php if(!empty($error)) echo "<p style='color:red'>$error</p>"; ?>
    <form method="POST" style="display:flex;flex-direction:column;align-items:center;">
      <input class="input" type="text" name="username" placeholder="Username" required style="margin-bottom:15px; width:220px;">
      <input class="input" type="password" name="password" placeholder="Password" required style="margin-bottom:15px; width:220px;">
      <button class="btn" type="submit" style="width:120px;">Login</button>
    </form>
  </div>
</div>
<!-- Footer -->
<footer class="footer">
  <div class="container">
    <p>&copy; <?=date('Y')?> Recycle Zone File Portal. All rights reserved.</p>
  </div>
</footer>

</body>
</html>
