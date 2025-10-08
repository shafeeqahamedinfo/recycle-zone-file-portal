<?php
session_start();
include 'db.php';
$err = '';
$success = '';
$logged = isset($_SESSION['user']);
$user_id = $logged ? $_SESSION['user']['id'] : null;

$MAX = 2048 * 1024 * 1024; // 2GB max upload

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
  $folder_type = ($_POST['folder_type'] === 'private' && $logged) ? 'private' : 'public';
  $target_dir = $folder_type === 'public' ? $publicDir : ($privateDir . '/' . $user_id);

  if (!is_dir($target_dir)) mkdir($target_dir, 0755, true);

  if (!empty($_POST['create_folder'])){
    $create = preg_replace('#[^a-zA-Z0-9_\- ]#','', $_POST['create_folder']);
    if ($create){
      $newPath = $target_dir . '/' . $create;
      if (!is_dir($newPath)) mkdir($newPath, 0755, true);
      $success = 'Folder created: ' . htmlspecialchars($create);
    }
  }

  if (isset($_FILES['file']) && $_FILES['file']['error'] !== UPLOAD_ERR_NO_FILE){
    $f = $_FILES['file'];
    if ($f['error'] !== UPLOAD_ERR_OK){ $err = 'Upload error code: ' . $f['error']; }
    elseif ($f['size'] > $MAX) { $err = 'File too large. Max 10MB.'; }
    else {
      $mime = mime_content_type($f['tmp_name']);
      $orig = basename($f['name']);
      $uniq = time() . '_' . bin2hex(random_bytes(6)) . '_' . preg_replace('/[^a-zA-Z0-9._-]/','', $orig);
      $subfolder = (!empty($_POST['subfolder'])) ? preg_replace('#[^a-zA-Z0-9_\- ]#','', $_POST['subfolder']) : '';
      $destFolder = $target_dir . ($subfolder ? '/' . $subfolder : '');
      if (!is_dir($destFolder)) mkdir($destFolder, 0755, true);
      $dest = $destFolder . '/' . $uniq;
      if (move_uploaded_file($f['tmp_name'], $dest)){
        $relPath = ($folder_type === 'public') ? ('public' . ($subfolder?'/'.$subfolder:'')) : ('private/' . $user_id . ($subfolder?'/'.$subfolder:''));
        $stmt = $pdo->prepare('INSERT INTO files (user_id, filename_orig, filename_disk, folder_type, folder_path, size, mime) VALUES (?,?,?,?,?,?,?)');
        $stmt->execute([$user_id, $orig, $uniq, $folder_type, $relPath, $f['size'], $mime]);
        $success = 'Uploaded: ' . htmlspecialchars($orig);
      } else { $err = 'Failed to move uploaded file.'; }
    }
  }
}
?>
<!doctype html>
<html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Upload</title>
<link rel="stylesheet" href="assets/style.css"></head><body>
<div class="header"><div class="brand">Recycle zone File Portal</div><div class="actions"><a class="btn" href="index.php">Home</a></div></div>
<div class="container"><div class="card">
  <h3>Upload File or Create Folder</h3>
  <?php if($err): ?><div class="notice"><?=htmlspecialchars($err)?></div><?php endif; ?>
  <?php if($success): ?><div class="notice"><?=htmlspecialchars($success)?></div><?php endif; ?>
  <form method="post" enctype="multipart/form-data">
    <label class="small">Choose target:</label>
    <div class="form-row">
      <select name="folder_type" class="input">
        <option value="public">Public (visible to all)</option>
        <?php if($logged): ?><option value="private">My Private (requires login)</option><?php endif; ?>
      </select>
      <input class="input" name="subfolder" placeholder="Optional: subfolder name" />
    </div>
    <div style="margin-top:10px">
      <input type="file" name="file">
    </div>
    <div style="margin-top:10px">
      <button class="btn">Upload</button>
    </div>
    <br>
    
    <br>
    <!--<h4>Create folder</h4>
    <div class="form-row">
      <input class="input" name="create_folder" placeholder="Folder name (letters,numbers, - _)" />
      <button class="btn">Create Folder</button>
    </div>-->
  </form>
</div></div>
<!-- Footer -->
<footer class="footer">
  <div class="container">
    <p>&copy; <?=date('Y')?> Recycle Zone File Portal. All rights reserved.</p>
  </div>
</footer>

</body></html>
