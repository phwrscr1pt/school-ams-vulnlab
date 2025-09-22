<?php require 'db.php'; include '_header.php'; 
if(empty($_SESSION['admin_pending'])){ header("Location: login.php"); exit; } ?>
<div class="card">
  <h1>ยืนยัน OTP (admin)</h1>
  <p class="small">ใส่รหัส 4 หลัก ช่วง 0000–0200 (ไม่มี rate limit)</p>
  <form method="post">
    <input name="otp" maxlength="4" placeholder="0000">
    <button class="btn" type="submit">Verify</button>
  </form>
  <?php
  if($_POST){
    $otp = preg_replace('/\D/','', $_POST['otp'] ?? '');
    // Intentionally weak: accept any code that exactly equals stored admin.otp_code OR in [0000..0200]
    $rs = $conn->query("SELECT otp_code FROM admins WHERE username='admin'");
    $ok = false;
    if($rs && $r=$rs->fetch_assoc()){
        if ($otp === $r['otp_code']) $ok = true;
    }
    // fallback acceptance to whole range for brute-force training
    if (!$ok && strlen($otp)===4 && intval($otp) >= 0 && intval($otp) <= 200) $ok = true;
    if($ok){
        $_SESSION['is_admin']=true;
        unset($_SESSION['admin_pending']);
        header("Location: admin.php"); exit;
    } else {
        echo "<p class='notice'>OTP ไม่ถูกต้อง</p>";
    }
  } ?>
</div>
<?php include '_footer.php'; ?>
