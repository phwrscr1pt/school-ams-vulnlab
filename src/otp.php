<?php
require 'db.php';

$TRAINING = true; // ← เปิดโหมดฝึก

// เดิม: ถ้าไม่มี admin_pending จะถูกรีไดเรกต์ไป login.php ทันที
// เปลี่ยนเป็น: อนุญาตให้ทดสอบได้แม้ไม่มี session เมื่อเป็นโหมดฝึก
if (empty($_SESSION['admin_pending']) && !$TRAINING) {
  header('Location: login.php'); exit;
}

$error = '';
$debug_sql = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $otp = preg_replace('/\D/', '', $_POST['otp'] ?? '');
  $ok  = false;

  if (preg_match('/^\d{4}$/', $otp)) {
    // ตั้งใจอ่อนแอเพื่อฝึก brute-force
    $sql = "SELECT 1 FROM admins WHERE username='admin' AND otp_code='$otp' LIMIT 1";
    $debug_sql = $sql;
    $rs = $conn->query($sql);
    $ok = $rs && $rs->num_rows === 1;
  }

  if ($ok) {
    $_SESSION['is_admin'] = true;
    // เดิมจะ unset ทำให้คำขอถัดไปกลายเป็น 302 -> login.php
    if (!$TRAINING) unset($_SESSION['admin_pending']);

    // สัญญาณชัดเจนให้ Intruder: 302 ไป admin.php เสมอเมื่อถูก
    header('Location: admin.php'); exit;
  } else {
    // เดาผิด: ตอบ 200 พร้อมข้อความเดิม
    $error = 'OTP ไม่ถูกต้อง';
  }
}

include '_header.php';
?>
<div class="card">
  <h1>ยืนยัน OTP (admin)</h1>
  <p class="hint">โหมดฝึก: ใส่ 4 หลัก (0000–9999)</p>
  <?php if($error): ?><p class="notice"><?=htmlspecialchars($error)?></p><?php endif; ?>
  <form method="post">
    <input name="otp" maxlength="4" placeholder="0000" inputmode="numeric" pattern="[0-9]*" autofocus>
    <button class="btn" type="submit">Verify</button>
  </form>
  <div class="debug"><b>DEBUG SQL:</b> <?=htmlspecialchars($debug_sql ?: '— —')?></div>
</div>
<?php include '_footer.php'; ?>
