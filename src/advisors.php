<?php
require 'db.php';
include '_header.php';

$q = $_GET['q'] ?? '';

// ตั้งใจอ่อนแอ: ทั้ง WHERE และ ORDER BY อยู่ "บรรทัดเดียว" + จุดฉีดเดียว (LIKE)
$sql = "SELECT advisor_code,
               name,
               TRIM(SUBSTRING_INDEX(name,' ',1)) AS prefix
        FROM advisors
        WHERE CONCAT_WS(' ', advisor_code, name, TRIM(SUBSTRING_INDEX(name,' ',1))) LIKE '%$q%' ORDER BY id ASC";

$res = $conn->query($sql);

$rows = [];
while ($res && $row = $res->fetch_assoc()) { $rows[] = $row; }

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
?>
<div class="card">
  <h1>ค้นหาอาจารย์ (รหัส/ชื่อ/คำนำหน้า)</h1>
  <form method="get" class="row">
    <input name="q" placeholder="เช่น 0001, รศ., อ., ดร., ชื่อบางส่วน หรือ payload" value="<?=h($q)?>">
    <button class="btn">ค้นหา</button>
  </form>
  <div class="debug"><b>DEBUG SQL:</b> <?=h($sql)?></div>
</div>

<?php if (empty($rows)): ?>
  <div class="card"><p>ไม่พบข้อมูล</p></div>
<?php else: ?>
  <div class="card">
    <h2>ผลลัพธ์ (<?=h(count($rows))?>)</h2>
    <ul style="list-style:none; padding:0; margin:0">
      <?php foreach($rows as $r): ?>
        <li style="padding:8px 0; border-bottom:1px solid var(--border)">
          <div style="font-weight:700"><?=h($r['name'])?> <small>(<?=h($r['advisor_code'])?>)</small></div>
          <div class="hint">คำนำหน้า: <?=h($r['prefix'])?></div>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<?php include '_footer.php'; ?>
