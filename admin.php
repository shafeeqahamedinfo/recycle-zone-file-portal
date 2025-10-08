<?php
session_start();
include 'db.php';
if (!isset($_SESSION['user']) || !$_SESSION['user']['is_admin']){
    die('Access denied.');
}

// Delete public file
if (!empty($_GET['delete_file'])){
    $id = (int)$_GET['delete_file'];
    $stmt = $pdo->prepare('SELECT * FROM files WHERE id=? AND folder_type="public"');
    $stmt->execute([$id]);
    $file = $stmt->fetch();
    if ($file){
        $path = __DIR__.'/uploads/'.$file['folder_path'].'/'.$file['filename_disk'];
        if(is_file($path)) unlink($path);
        $pdo->prepare('DELETE FROM files WHERE id=?')->execute([$id]);
        header('Location: admin.php'); exit;
    }
}

// Delete user (not self)
if (!empty($_GET['delete_user'])){
    $uid = (int)$_GET['delete_user'];
    if ($uid != $_SESSION['user']['id']){
        $pdo->prepare('DELETE FROM users WHERE id=?')->execute([$uid]);
        header('Location: admin.php'); exit;
    }
}

// Fetch public files
$files = $pdo->query('SELECT * FROM files WHERE folder_type="public"')->fetchAll();
// Fetch all users
$users = $pdo->query('SELECT * FROM users')->fetchAll();
?>
<h2>Admin Panel</h2>
<h3>Public Files</h3>
<ul>
<?php foreach($files as $f): ?>
    <li><?=htmlspecialchars($f['filename_orig'])?> 
    <a href="admin.php?delete_file=<?=$f['id']?>" onclick="return confirm('Delete?')">Delete</a></li>
<?php endforeach; ?>
</ul>

<h3>Users</h3>
<ul>
<?php foreach($users as $u): ?>
    <li><?=htmlspecialchars($u['username'])?> <?= $u['is_admin'] ? '(Admin)' : '' ?>
    <?php if($u['id'] != $_SESSION['user']['id']): ?>
        <a href="admin.php?delete_user=<?=$u['id']?>" onclick="return confirm('Delete?')">Delete</a>
    <?php endif; ?>
    </li>
<?php endforeach; ?>
</ul>
<a href="logout.php">Logout</a>
