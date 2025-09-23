<?php
require 'db.php';
if (empty($_SESSION['is_admin'])) { header('Location: login.php'); exit; }
function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
$q = trim($_GET['q'] ?? '');
$filter = '';
if ($q !== '') {
  $qEsc = $conn->real_escape_string($q);
  $filter = "WHERE student_id LIKE '%$qEsc%' OR fullname LIKE '%$qEsc%' OR faculty LIKE '%$qEsc%' OR major LIKE '%$qEsc%'";
}
$sql = "SELECT student_id, fullname, faculty, major, gpa FROM students $filter ORDER BY student_id ASC LIMIT 200";
$res = $conn->query($sql);
include '_header.php';
?>
<div class="card">
  <h1>จัดการนักเรียน (Admin)</h1>
  <p class="hint">ค้นหา/แก้ไขข้อมูลรายบุคคล — แสดงตัวอย่างการควบคุมข้อมูลโดยผู้ดูแล</p>
  <form class="row mt-12" method="get" action="admin_students.php">
    <input name="q" placeholder="ค้นหา SID / ชื่อ / คณะ / สาขา" value="<?=h($q)?>" />
    <button class="btn" type="submit">ค้นหา</button>
    <a class="btn btn-secondary" href="admin_students.php">รีเซ็ต</a>
  </form>
  <div class="debug"><b>DEBUG SQL:</b> <?=h($sql)?></div>
</div>
<div class="card">
  <table class="table">
    <thead><tr><th>SID</th><th>ชื่อ-สกุล</th><th>คณะ</th><th>สาขา</th><th>GPA</th><th>จัดการ</th></tr></thead>
    <tbody>
    <?php if($res): while($row=$res->fetch_assoc()): ?>
      <tr>
        <td><?=h($row['student_id'])?></td>
        <td><?=h($row['fullname'])?></td>
        <td><?=h($row['faculty'])?></td>
        <td><?=h($row['major'])?></td>
        <td><?=h($row['gpa'])?></td>
        <td><a class="btn" href="admin_student_edit.php?sid=<?=h($row['student_id'])?>">แก้ไข</a></td>
      </tr>
    <?php endwhile; else: ?>
      <tr><td colspan="6">ไม่พบข้อมูล</td></tr>
    <?php endif; ?>
    </tbody>
  </table>
</div>
<?php include '_footer.php'; ?>
