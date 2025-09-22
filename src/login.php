<?php require 'db.php'; include '_header.php'; ?>
<div class="card">
  <h1>เข้าสู่ระบบ</h1>
  <form method="post">
    <label>ชื่อผู้ใช้</label>
    <input name="username" required>
    <label>รหัสผ่าน</label>
    <input type="password" name="password" required>
    <button class="btn" type="submit">Login</button>
  </form>
<?php
if($_POST){
  $u = $_POST['username'] ?? '';
  $p = $_POST['password'] ?? '';
  // intentionally vulnerable: direct string interpolation with quotes
  $sql = "SELECT * FROM users WHERE username = '$u' AND password = '$p'"; 
  $res = $conn->query($sql);
  echo "<div class='debug'><b>DEBUG SQL:</b> ".htmlspecialchars($sql)."</div>";
  if($res && $res->num_rows === 1){
      $row = $res->fetch_assoc();
      $_SESSION['user'] = $row['username'];
      $_SESSION['role'] = $row['role'];
      $_SESSION['sid']  = $row['student_id'];
      if($row['role']==='admin'){
         $_SESSION['admin_pending']=true;
         header("Location: otp.php"); exit;
      } else {
         header("Location: profile.php?sid=".$row['student_id']); exit;
      }
  } else {
      echo "<p class='notice'>เข้าสู่ระบบไม่สำเร็จ</p>";
  }
}
?>
</div>
<?php include '_footer.php'; ?>
