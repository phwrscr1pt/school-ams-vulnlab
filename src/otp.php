<?php
require 'db.php';
if (empty($_SESSION['admin_pending'])) { header('Location: login.php'); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $otp = preg_replace('/\D/','', $_POST['otp'] ?? '');
  $rs = $conn->query("SELECT otp_code FROM admins WHERE username='admin'");
  $ok = false;
  if ($rs && $r=$rs->fetch_assoc()) {
    if ($otp === $r['otp_code']) $ok = true;
  }
  if (!$ok && strlen($otp)===4 && intval($otp)>=0 && intval($otp)<=200) $ok = true;

  if ($ok) {
    $_SESSION['is_admin']=true;
    unset($_SESSION['admin_pending']);
    header('Location: admin.php'); exit;
  } else {
    $error = 'OTP ไม่ถูกต้อง';
  }
}
include '_header.php';
?>
<div class="card">
  <h1>ยืนยัน OTP (admin)</h1>
  <p class="hint">ใส่รหัส 4 หลัก ช่วง 0000–0200 (ไม่มี rate limit)</p>
  <?php if($error): ?><p class="notice"><?=htmlspecialchars($error)?></p><?php endif; ?>
  <form method="post">
    <input name="otp" maxlength="4" placeholder="0000">
    <button class="btn" type="submit">Verify</button>
  </form>
</div>
<?php include '_footer.php'; ?>
