<?php
// src/request.php (ปรับปรุง)
include '_header.php';

$LEVEL = $_GET['level'] ?? 'easy'; // easy, medium, hard

?>
<div class="card">
  <h1>ยื่นคำร้อง / อัปโหลดเอกสาร</h1>
  
  <!-- Level Selector -->
  <div class="row mb-12">
    <a href="?level=easy" class="btn <?=$LEVEL==='easy'?'':'btn-secondary'?>">Easy</a>
    <a href="?level=medium" class="btn <?=$LEVEL==='medium'?'':'btn-secondary'?>">Medium</a>
    <a href="?level=hard" class="btn <?=$LEVEL==='hard'?'':'btn-secondary'?>">Hard</a>
  </div>

  <form method="post" enctype="multipart/form-data">
    <input type="hidden" name="level" value="<?=htmlspecialchars($LEVEL)?>">
    <label>ไฟล์เอกสาร</label>
    <input type="file" name="doc" required>
    <button class="btn" type="submit">อัปโหลด</button>
  </form>

<?php
if($_FILES){
  $f = $_FILES['doc'];
  $orig = $f['name'];
  $ctype = $f['type'];
  $tmpname = $f['tmp_name'];
  $level = $_POST['level'] ?? 'easy';
  
  $allowed = false;
  $error_msg = '';
  
  if ($level === 'easy') {
    // Easy: ตรวจ MIME type จาก client (แก้ได้)
    $allowed_mime = ['application/pdf','application/msword','image/png','image/jpeg'];
    if(in_array($ctype, $allowed_mime)) {
      $allowed = true;
    } else {
      $error_msg = "ชนิดไฟล์ไม่อนุญาต ($ctype)";
    }
    
  } elseif ($level === 'medium') {
    // Medium: ตรวจ extension
    $ext = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
    $allowed_ext = ['pdf','doc','docx','png','jpg','jpeg'];
    if(in_array($ext, $allowed_ext)) {
      $allowed = true;
    } else {
      $error_msg = "Extension ไม่อนุญาต (.$ext)";
    }
    
  } else { // hard
    // Hard: ตรวจ extension + Magic Number
    $ext = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
    $allowed_ext = ['pdf','doc','docx','png','jpg','jpeg'];
    
    if(!in_array($ext, $allowed_ext)) {
      $error_msg = "Extension ไม่อนุญาต";
    } else {
      // ตรวจ Magic Number
      $handle = fopen($tmpname, "r");
      $bytes = fread($handle, 4);
      fclose($handle);
      
      $valid = false;
      // JPG: FF D8 FF
      if(bin2hex(substr($bytes, 0, 3)) === 'ffd8ff') $valid = true;
      // PNG: 89 50 4E 47
      if(bin2hex($bytes) === '89504e47') $valid = true;
      // PDF: 25 50 44 46
      if(bin2hex($bytes) === '25504446') $valid = true;
      
      if($valid) {
        $allowed = true;
      } else {
        $error_msg = "File signature ไม่ถูกต้อง";
      }
    }
  }
  
  if($allowed) {
    $target = __DIR__.'/uploads/'.basename($orig);
    if(move_uploaded_file($tmpname, $target)){
      echo "<p class='notice'>อัปโหลดสำเร็จ: <a target='_blank' href='uploads/".htmlspecialchars(basename($orig))."'>เปิดไฟล์</a></p>";
    } else {
      echo "<p class='notice'>อัปโหลดล้มเหลว</p>";
    }
  } else {
    echo "<p class='notice'>$error_msg</p>";
  }
}
?>
</div>
<?php include '_footer.php'; ?>