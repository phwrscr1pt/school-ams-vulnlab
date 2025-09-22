<?php require 'db.php'; include '_header.php'; 
$sid = $_GET['sid'] ?? ($_SESSION['sid'] ?? '');
// Intentionally no authz: trusts sid from query param (IDOR)
$sql = "SELECT * FROM students WHERE student_id = '$sid'";
$res = $conn->query($sql);
?>
<div class="card">
  <h1>ข้อมูลส่วนตัว</h1>
  <?php if($res && $res->num_rows): $st = $res->fetch_assoc(); ?>
    <p><b>ชื่อ-สกุล:</b> <?=htmlspecialchars($st['fullname'])?></p>
    <p><b>รหัสนักศึกษา:</b> <?=htmlspecialchars($st['student_id'])?></p>
    <p><b>คณะ/สาขา/หลักสูตร:</b> <?=htmlspecialchars($st['faculty'])?> / <?=htmlspecialchars($st['major'])?> / <?=htmlspecialchars($st['program'])?></p>
</div>
<div class="card">
  <h1>ข้อมูลส่วนบุคคล</h1>
  <p><b>วันเกิด:</b> <?=htmlspecialchars($st['dob'])?></p>
  <p><b>รหัสบัตรประชาชน:</b> <?=htmlspecialchars($st['citizen_id'])?></p>
  <p><b>สัญชาติ/ศาสนา/หมู่เลือด:</b> <?=htmlspecialchars($st['nationality'])?> / <?=htmlspecialchars($st['religion'])?> / <?=htmlspecialchars($st['blood'])?></p>
  <p><b>บิดา/มารดา:</b> <?=htmlspecialchars($st['parent_father'])?> / <?=htmlspecialchars($st['parent_mother'])?></p>
  <p><b>ที่อยู่:</b> <?=htmlspecialchars($st['address'])?></p>
</div>
<div class="card">
  <h1>ข้อมูลผลการเรียน</h1>
  <p><b>เกรดเฉลี่ย:</b> <?=htmlspecialchars($st['gpa'])?></p>
</div>
<div class="card">
  <div class="debug"><b>DEBUG SQL:</b> <?=htmlspecialchars($sql)?></div>
</div>
  <?php else: ?>
    <p class="notice">ไม่พบนักเรียน SID นี้</p>
  <?php endif; ?>
<?php include '_footer.php'; ?>
