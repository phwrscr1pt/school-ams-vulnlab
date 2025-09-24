<?php
require 'db.php';
$error = '';
$debug_sql = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = $_POST['username'] ?? '';
    $p = $_POST['password'] ?? '';

    $sql = "SELECT * FROM users WHERE username = '$u' AND password = '$p'";
    $debug_sql = $sql;
    $res = $conn->query($sql);

    if ($res && $res->num_rows === 1) {
        $row = $res->fetch_assoc();
        $_SESSION['user'] = $row['username'];
        $_SESSION['role'] = $row['role'];
        $_SESSION['sid']  = $row['student_id'];
        if ($row['role'] === 'admin') {
            $_SESSION['admin_pending'] = true;
            header('Location: otp.php'); exit;
        } else {
            header('Location: profile.php?sid=' . $row['student_id']); exit;
        }
    } else {
        $error = 'เข้าสู่ระบบไม่สำเร็จ';
    }
}
include '_header.php';
?>
<div class="card">
  <h1>เข้าสู่ระบบ</h1>
  <?php if($error): ?><p class="notice"><?=htmlspecialchars($error)?></p><?php endif; ?>
  <form method="post">
    <label>ชื่อผู้ใช้</label>
    <input name="username" required>
    <label>รหัสผ่าน</label>
    <input type="password" name="password" required>
    <button class="btn" type="submit">Login</button>
  </form>
  <div class="debug"><b>DEBUG SQL:</b> <?=htmlspecialchars($debug_sql ?: '— —')?></div>
  <div class="hint">Demo User <b>usersname : s0001 password : 1234 </b> </div>
</div>
<?php include '_footer.php'; ?>
