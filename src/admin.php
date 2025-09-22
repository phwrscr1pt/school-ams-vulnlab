<?php include 'db.php'; if(empty($_SESSION['is_admin'])){ header("Location: login.php"); exit; } include '_header.php'; ?>
<div class="card">
  <h1>ผู้ดูแลระบบ (Admin)</h1>
  <p>ยินดีต้อนรับ <b><?=htmlspecialchars($_SESSION['user'])?></b></p>
  <div class="notice">FLAG: <b>LOCTH{Admin_Dashboard_OK}</b></div>
</div>
<div class="card">
  <h1>รายชื่อนักเรียน (ตัวอย่าง)</h1>
  <table class="table">
    <tr><th>SID</th><th>ชื่อ-สกุล</th><th>คณะ</th><th>สาขา</th></tr>
    <?php $q=$conn->query("SELECT student_id, fullname, faculty, major FROM students ORDER BY student_id ASC");
    while($q && $row=$q->fetch_assoc()): ?>
      <tr>
        <td><?=htmlspecialchars($row['student_id'])?></td>
        <td><?=htmlspecialchars($row['fullname'])?></td>
        <td><?=htmlspecialchars($row['faculty'])?></td>
        <td><?=htmlspecialchars($row['major'])?></td>
      </tr>
    <?php endwhile; ?>
  </table>
</div>
<?php include '_footer.php'; ?>
