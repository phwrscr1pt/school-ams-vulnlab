<?php require 'db.php'; include '_header.php'; 
$q = $_GET['q'] ?? '';
$sql = "SELECT id, name, position, department, email, phone FROM advisors WHERE name LIKE '%$q%' OR email LIKE '%$q%' OR department LIKE '%$q%' ORDER BY id ASC";
$res = $conn->query($sql);
?>
<div class="card">
  <h1>ค้นหาอาจารย์ที่ปรึกษา</h1>
  <form method="get">
    <input name="q" placeholder="ค้นหา..." value="<?=htmlspecialchars($q)?>">
    <button class="btn">ค้นหา</button>
  </form>
  <div class="small">Hint: ช่องค้นหานี้เปราะบางต่อ <b>UNION-based SQL Injection</b> (columns = 6)</div>
  <div class="debug"><b>DEBUG SQL:</b> <?=htmlspecialchars($sql)?></div>
</div>
<div class="card">
  <table class="table">
    <tr><th>#</th><th>ชื่อ</th><th>ตำแหน่งงาน</th><th>สังกัด</th><th>Email</th><th>เบอร์โทรศัพท์</th></tr>
    <?php while($res && $row=$res->fetch_row()): ?>
      <tr>
        <?php for($i=0;$i<6;$i++): ?><td><?=htmlspecialchars($row[$i]??'')?></td><?php endfor; ?>
      </tr>
    <?php endwhile; ?>
  </table>
</div>
<?php include '_footer.php'; ?>
