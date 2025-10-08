<?php
session_start();
include 'db.php';

if($_SERVER['REQUEST_METHOD']=='POST'){
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO users (username,email,phone,password) VALUES (?,?,?,?)");
    try{
        $stmt->execute([$username,$email,$phone,$password]);
        header("Location: login.php");
        exit;
    }catch(Exception $e){
        $error = "Username or Email already exists!";
    }
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Register</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
  <div class="header"><div class="brand">Recycle zone File Portal</div><div class="actions"><a class="btn" href="index.php">Home</a></div></div>

<div class="container" style="display: flex; justify-content: center; align-items: center; min-height: 100vh;">
  <div class="card" style="width: 100%; max-width: 400px;">
    <h3 style="text-align: center;">Register</h3><br>
    <?php if(!empty($error)) echo "<p style='color:red;text-align:center;'>$error</p>"; ?>
    <form method="POST" style="display: flex; flex-direction: column; align-items: center;">
      <input class="input" type="text" name="username" placeholder="Username" required style="width:100%;"><br>
      <input class="input" type="email" name="email" placeholder="Email" required style="width:100%;"><br>
      <input class="input" type="text" name="phone" placeholder="Phone" style="width:100%;"><br>
      <input class="input" type="password" name="password" placeholder="Password" required style="width:100%;"><br>
      <button class="btn" type="submit" style="width:100%;">Register</button>
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
