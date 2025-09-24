<?php include '_header.php'; ?>
<div class="card">
  <h1>ยื่นคำร้อง / อัปโหลดเอกสาร</h1>
  <form method="post" enctype="multipart/form-data">
    <label>ไฟล์เอกสาร (รองรับเฉพาะไฟล์ .png .jpg .pdf .doc)</label>
    <input type="file" name="doc" required>
    <button class="btn" type="submit">อัปโหลด</button>
  </form>
  <!-- <div class="hint">ตั้งใจอ่อนแอ: เช็คจาก <code>Content-Type</code> ที่  client ส่งมา (ดัดแปลงได้ด้วย Burp)</div> -->
<?php
if($_FILES){
  $f = $_FILES['doc'];
  $orig = $f['name']; $ctype = $f['type']; // vulnerable: trust user header
  $allowed = ['application/pdf','application/msword','application/vnd.openxmlformats-officedocument.wordprocessingml.document','image/png','image/jpeg'];
  if(in_array($ctype,$allowed)){
     $target = __DIR__.'/uploads/'.basename($orig);
     if(move_uploaded_file($f['tmp_name'],$target)){
        // echo "<p class='notice'>อัปโหลดสำเร็จ: <a target='_blank' href='uploads/".htmlspecialchars(basename($orig))."'>เปิดไฟล์</a></p>";
     } else { echo "<p class='notice'>อัปโหลดล้มเหลว</p>"; }
  } else { echo "<p class='notice'>ชนิดไฟล์ไม่อนุญาต ($ctype)</p>"; }
}
?>
<!-- </div>
<div class="card"><h2>คำแนะนำสำหรับผู้สอน</h2>
  <ul>
    <li>โฟลเดอร์ <code>/uploads</code> เปิดรัน <code>.php/.phtml</code> ได้ (ดู <code>uploads/.htaccess</code>)</li>
    <li>โจทย์: อัปโหลด <code>shell.phtml</code> โดยตั้ง <code>Content-Type: application/pdf</code> ในคำขอ</li>
  </ul>
</div> -->
<?php include '_footer.php'; ?>
