<?php
session_start();
include 'db.php';

$logged = isset($_SESSION['user']);
$user_id = $logged ? $_SESSION['user']['id'] : 0;
$is_admin = $logged ? $_SESSION['user']['is_admin'] : 0;

// Handle delete request
if($logged && !empty($_GET['delete'])){
    $file_id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("SELECT * FROM files WHERE id=? LIMIT 1");
    $stmt->execute([$file_id]);
    $file = $stmt->fetch();

    if($file && ($file['user_id']==$user_id || $is_admin)){
        $path = __DIR__.'/uploads/'.$file['folder_path'].'/'.$file['filename_disk'];
        if(is_file($path)) unlink($path);
        $pdo->prepare("DELETE FROM files WHERE id=?")->execute([$file_id]);
        header("Location: view.php"); exit;
    }
}

// Fetch visible files
if($logged){
    $stmt = $pdo->prepare("SELECT * FROM files WHERE folder_type='public' OR (folder_type='private' AND user_id=?) ORDER BY folder_path, filename_orig");
    $stmt->execute([$user_id]);
}else{
    $stmt = $pdo->query("SELECT * FROM files WHERE folder_type='public' ORDER BY folder_path, filename_orig");
}

$files = $stmt->fetchAll();

// Group files by folder
$folders = [];
foreach($files as $f){
    $folders[$f['folder_path']][] = $f;
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>View Files</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
  <div class="header"><div class="brand">Recycle zone File Portal</div><div class="actions"><a class="btn" href="index.php">Home</a></div></div>

<div class="container">
  <h3>Files</h3>
  <?php foreach($folders as $folder => $f_list): ?>
    <div class="card">
      <h4>Folder: <?=htmlspecialchars($folder)?></h4>
      <div class="file-list">
        <?php foreach($f_list as $f): ?>
          <div class="file-card">
            <strong><?=htmlspecialchars($f['filename_orig'])?></strong>
            <div class="meta">Size: <?=round($f['size']/1024,2)?> KB • Uploaded: <?=$f['uploaded_at']?></div>
            <div style="margin-top:10px;">
              <!-- Open file in new tab -->
              <a class="btn" href="uploads/<?=$f['folder_path'].'/'.$f['filename_disk']?>" target="_blank" style="margin-right: 10px;">Open</a>
              <!-- Download -->
              <a class="btn" href="uploads/<?=$f['folder_path'].'/'.$f['filename_disk']?>" download style="margin-right: 10px;">Download</a>
              <!-- Delete (only owner or admin) -->
              <?php if($logged && ($f['user_id']==$user_id || $is_admin)): ?>
              <a class="btn" href="view.php?delete=<?=$f['id']?>" onclick="return confirm('Delete this file?')" style="margin-top: 10px;">Delete</a>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endforeach; ?>
</div>
<!-- Footer -->
<footer class="footer">
  <div class="container">
    <p>&copy; <?=date('Y')?> Recycle Zone File Portal. All rights reserved.</p>
  </div>
</footer>

</body>
</html>
