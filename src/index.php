<?php include '_header.php'; ?>
<div class="card">
  <h1>ยินดีต้อนรับสู่ระบบ AMS (Lab)</h1>
  <p>เว็บนี้ตั้งใจทำ <b>เปราะบาง</b> สำหรับฝึก <b>SQL Injection</b>, <b>IDOR</b>, <b>File Upload</b>, และการใช้งาน Burp Suite (Proxy / Repeater / Intruder / Decoder / Comparer / Extensions).</p>
  <?php if(empty($_SESSION['user'])): ?>
    <p><a class="btn" href="login.php">เข้าสู่ระบบ</a></p>
  <?php else: ?>
    <div class="notice">คุณเข้าสู่ระบบในชื่อ <b><?=htmlspecialchars($_SESSION['user'])?></b></div>
  <?php endif; ?>
</div>
<div class="card">
  <h1>โจทย์</h1>
  <ul>
    <li><b>Login bypass</b>: ลองใช้ SQLi เพื่อข้ามการเข้าสู่ระบบ</li>
    <li><b>Admin OTP</b>: เมื่อเป็น admin จะถูกบังคับไปกรอก OTP (0000–0200) ไม่มี rate limit</li>
    <li><b>IDOR</b>: เปลี่ยนค่า <code>sid</code> ใน <code>profile.php?sid=</code> เพื่อดูข้อมูลนักเรียนคนอื่น</li>
    <li><b>SQLi (UNION-based)</b>: หน้า <code>advisors.php</code> สามารถใช้ UNION SELECT</li>
    <li><b>File Upload</b>: หน้า <code>request.php</code> มีการตรวจสอบไฟล์แบบผิดพลาด อัปโหลด webshell ให้ได้</li>
  </ul>
</div>
<?php include '_footer.php'; ?>
