<?php include '_header.php'; ?>
<div class="card">
  <h1>ยื่นคำร้อง / อัปโหลดเอกสาร</h1>
  <form method="post" enctype="multipart/form-data">
    <label>ไฟล์เอกสาร</label>
    <input type="file" name="doc" required>
    <button class="btn" type="submit">อัปโหลด</button>
  </form>
  <div class="small">การตรวจสอบไฟล์นี้ตั้งใจทำไม่รัดกุม (ยึดจาก <code>Content-Type</code> ของผู้ใช้) — สามารถหลอกได้ด้วย Burp</div>
<?php
if($_FILES){
  $f = $_FILES['doc'];
  $orig = $f['name'];
  $ctype = $f['type']; // trust user-controlled header (vulnerable)
  // naive allow-list based on MIME (can be spoofed)
  $allowed = ['application/pdf','application/msword','application/vnd.openxmlformats-officedocument.wordprocessingml.document','image/png','image/jpeg'];
  if(in_array($ctype, $allowed)){
     $target = __DIR__.'/uploads/'.basename($orig);
     if(move_uploaded_file($f['tmp_name'], $target)){
        echo "<p class='notice'>อัปโหลดสำเร็จ: <a href='uploads/".htmlspecialchars(basename($orig))."' target='_blank'>เปิดไฟล์</a></p>";
     } else {
        echo "<p class='notice'>อัปโหลดล้มเหลว</p>";
     }
  } else {
     echo "<p class='notice'>ชนิดไฟล์ไม่อนุญาต ($ctype)</p>";
  }
}
?>
</div>
<div class="card">
  <h1>คำแนะนำ (สำหรับผู้ดูแลห้องแล็บ)</h1>
  <ul>
    <li>โฟลเดอร์ <code>/uploads</code> อนุญาตให้รัน <code>.php/.phtml</code> (ดูไฟล์ <code>uploads/.htaccess</code>)</li>
    <li>โจทย์: อัปโหลด webshell เช่น <code>shell.phtml</code> โดยตั้ง <code>Content-Type: application/pdf</code> ในคำขอ</li>
  </ul>
</div>
<?php include '_footer.php'; ?>
