<?php
require 'db.php';
include '_header.php';

$q = $_GET['q'] ?? '';
$sql = "SELECT id, name, position, department, email, phone
        FROM advisors
        WHERE name LIKE '%$q%' OR email LIKE '%$q%' OR department LIKE '%$q%'
        ORDER BY id ASC";
$res = $conn->query($sql);

// ดึงทั้งหมดแล้วแบ่งหน้าใน PHP (10 ชื่อ/แท็บ)
$rows = [];
while ($res && $row = $res->fetch_assoc()) { $rows[] = $row; }

$perPage = 10;
$total = count($rows);
$pages = max(1, (int)ceil($total / $perPage));
$page = isset($_GET['page']) ? max(1, min($pages, (int)$_GET['page'])) : 1;
$start = ($page - 1) * $perPage;
$current = array_slice($rows, $start, $perPage);

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function page_link($n){
  $q = $_GET['q'] ?? '';
  $params = ['page'=>$n];
  if ($q !== '') $params['q']=$q;
  return 'advisors.php?' . http_build_query($params);
}
?>
<div class="card">
  <h1>ค้นหาอาจารย์ที่ปรึกษา (ลองใส่ ผศ , ดร)</h1>
  <form method="get" class="row">
    <input name="q" placeholder="ค้นหา..." value="<?=h($q)?>">
    <button class="btn">ค้นหา</button>
    <!-- <a class="btn btn-secondary" href="advisors.php">รีเซ็ต</a> -->
  </form>
  <!-- <div class="hint">* โหมดฝึก: ยังรองรับ <b>UNION-based SQLi</b> (คอลัมน์ 6) เหมือนเดิม</div> -->
  <div class="debug"><b>DEBUG SQL:</b> <?=h($sql)?></div>
</div>

<?php if ($total === 0): ?>
  <div class="card"><p>ไม่พบอาจารย์ที่ปรึกษา</p></div>
<?php else: ?>
  <div class="card">
    <h2>รายชื่ออาจารย์ (ทั้งหมด <?=h($total)?> คน)</h2>

    <!-- Tabs (บน) -->
    <div class="row" role="tablist" style="flex-wrap:wrap; gap:8px; margin-bottom:10px">
      <?php for($i=1;$i<=$pages;$i++): ?>
        <a href="<?=h(page_link($i))?>"
           class="btn <?= $i===$page ? '' : 'btn-secondary' ?>"
           role="tab"
           aria-selected="<?= $i===$page ? 'true':'false' ?>"
        >หน้า <?=h($i)?></a>
      <?php endfor; ?>
    </div>

    <!-- เนื้อหาแท็บ: ชื่อ 10 รายการ -->
    <ul style="list-style:none; padding:0; margin:0">
      <?php foreach($current as $row): ?>
        <li style="padding:10px 0; border-bottom:1px solid var(--border)">
          <div style="font-weight:700"><?=h($row['name'])?></div>
          <div class="hint"><?=h($row['position'])?> • <?=h($row['department'])?></div>
        </li>
      <?php endforeach; ?>
    </ul>

    <!-- Tabs (ล่าง) -->
    <!-- <div class="row" role="tablist" style="flex-wrap:wrap; gap:8px; margin-top:12px">
      <?php for($i=1;$i<=$pages;$i++): ?>
        <a href="<?=h(page_link($i))?>"
           class="btn <?= $i===$page ? '' : 'btn-secondary' ?>"
           role="tab"
           aria-selected="<?= $i===$page ? 'true':'false' ?>"
        >หน้า <?=h($i)?></a>
      <?php endfor; ?>
    </div> -->
  </div>
<?php endif; ?>

<?php include '_footer.php'; ?>
