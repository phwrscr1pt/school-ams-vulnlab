<?php
require 'db.php';
if (empty($_SESSION['is_admin'])) { header('Location: login.php'); exit; }
function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
$sid = $_GET['sid'] ?? '';
if ($sid === '') { header('Location: admin_students.php'); exit; }
$sidEsc = $conn->real_escape_string($sid);
$select_sql = "SELECT * FROM students WHERE student_id='$sidEsc' LIMIT 1";
$res = $conn->query($select_sql);
if (!$res || $res->num_rows === 0) { $notfound = true; }
$st = $res ? $res->fetch_assoc() : null;
$notice=''; $error=''; $debug_sql='';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $fields=['fullname','faculty','major','program','dob','citizen_id','nationality','religion','blood','parent_father','parent_mother','address','gpa'];
  $updates=[];
  foreach($fields as $f){ $v=$_POST[$f]??''; $v=$conn->real_escape_string($v); $updates[]="$f='$v'"; }
  $update_sql = "UPDATE students SET ".implode(',', $updates)." WHERE student_id='$sidEsc' LIMIT 1";
  $debug_sql = $update_sql;
  if ($conn->query($update_sql)) { $notice="บันทึกการแก้ไขของ SID <b>".h($sid)."</b> เรียบร้อย"; $res=$conn->query($select_sql); $st=$res?$res->fetch_assoc():null; }
  else { $error="อัปเดตไม่สำเร็จ"; }
}
include '_header.php';
?>
<div class="card">
  <h1>แก้ไขข้อมูลนักเรียน</h1>
  <p class="hint">SID: <b><?=h($sid)?></b></p>
  <?php if(isset($notfound) && $notfound): ?>
    <p class="notice">ไม่พบข้อมูลนักเรียน</p>
  <?php else: ?>
    <?php if($notice): ?><p class="notice"><?= $notice ?></p><?php endif; ?>
    <?php if($error): ?><p class="notice" style="border-color:#ff8;"><b>ข้อผิดพลาด:</b> <?=h($error)?></p><?php endif; ?>
    <form method="post">
      <div class="grid">
        <div class="col-6"><label>ชื่อ-สกุล</label><input name="fullname" value="<?=h($st['fullname'])?>"></div>
        <div class="col-3"><label>คณะ</label><input name="faculty" value="<?=h($st['faculty'])?>"></div>
        <div class="col-3"><label>สาขา</label><input name="major" value="<?=h($st['major'])?>"></div>
        <div class="col-4"><label>หลักสูตร</label><input name="program" value="<?=h($st['program'])?>"></div>
        <div class="col-4"><label>วันเกิด (YYYY-MM-DD)</label><input name="dob" value="<?=h($st['dob'])?>"></div>
        <div class="col-4"><label>เลขบัตรประชาชน</label><input name="citizen_id" value="<?=h($st['citizen_id'])?>"></div>
        <div class="col-4"><label>สัญชาติ</label><input name="nationality" value="<?=h($st['nationality'])?>"></div>
        <div class="col-4"><label>ศาสนา</label><input name="religion" value="<?=h($st['religion'])?>"></div>
        <div class="col-4"><label>หมู่เลือด</label><input name="blood" value="<?=h($st['blood'])?>"></div>
        <div class="col-6"><label>ชื่อบิดา</label><input name="parent_father" value="<?=h($st['parent_father'])?>"></div>
        <div class="col-6"><label>ชื่อมารดา</label><input name="parent_mother" value="<?=h($st['parent_mother'])?>"></div>
        <div class="col-12"><label>ที่อยู่</label><input name="address" value="<?=h($st['address'])?>"></div>
        <div class="col-3"><label>GPA</label><input name="gpa" value="<?=h($st['gpa'])?>"></div>
      </div>
      <div class="row mt-16">
        <button class="btn" type="submit">บันทึกการแก้ไข</button>
        <a class="btn btn-secondary" href="admin_students.php">กลับรายการ</a>
      </div>
    </form>
    <div class="debug mt-12"><b>DEBUG SQL:</b> <?=h($debug_sql ?: '— เปิดหน้านี้ครั้งแรก —')?></div>
  <?php endif; ?>
</div>
<?php include '_footer.php'; ?>
