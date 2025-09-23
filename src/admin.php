<?php
require 'db.php';
if (empty($_SESSION['is_admin'])) { header('Location: login.php'); exit; }
function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

$notice=''; $error='';

// ---- Actions (unchanged) ----
if ($_SERVER['REQUEST_METHOD']==='POST') {
  $action = $_POST['action'] ?? '';
  if ($action==='reset_password') {
    $username = $_POST['username'] ?? '';
    $newpass  = $_POST['newpass'] ?? '';
    if ($username && $newpass) {
      $u=$conn->real_escape_string($username); $p=$conn->real_escape_string($newpass);
      if ($conn->query("UPDATE users SET password='$p' WHERE username='$u' AND role='student'")) {
        $notice = "เปลี่ยนรหัสผ่านให้ <b>".h($username)."</b> เรียบร้อย";
      } else { $error='อัปเดตไม่สำเร็จ'; }
    } else { $error='กรอกข้อมูลให้ครบ'; }
  }
  if ($action==='rotate_otp') {
    $code = $_POST['otp'] ?? '';
    if ($code==='' || $code==='random') {
      $code = str_pad((string)random_int(0,9999), 4, '0', STR_PAD_LEFT);
    } else {
      $code = preg_replace('/\D/','', $code);
      $code = str_pad(substr($code,0,4), 4, '0', STR_PAD_LEFT);
    }
    if ($conn->query("UPDATE admins SET otp_code='$code' WHERE username='admin'")) {
      $notice = "อัปเดต OTP ใหม่สำหรับ admin: <b>".h($code)."</b>";
    } else { $error='อัปเดต OTP ไม่สำเร็จ'; }
  }
}

// ---- Stats / lookups ----
$tot_users    = ($r=$conn->query("SELECT COUNT(*) c FROM users"))    ? ($r->fetch_assoc()['c'] ?? 0) : 0;
$tot_students = ($r=$conn->query("SELECT COUNT(*) c FROM students")) ? ($r->fetch_assoc()['c'] ?? 0) : 0;
$tot_advisors = ($r=$conn->query("SELECT COUNT(*) c FROM advisors")) ? ($r->fetch_assoc()['c'] ?? 0) : 0;
$otp_row=$conn->query("SELECT otp_code FROM admins WHERE username='admin'")->fetch_assoc();
$current_otp=$otp_row?$otp_row['otp_code']:'----';
$student_opts=$conn->query("SELECT username, student_id FROM users WHERE role='student' ORDER BY student_id ASC");

// ---- Pagination for sensitive table ----
$perPage = 10;
$pages = max(1, (int)ceil($tot_students / $perPage));
$page  = isset($_GET['p']) ? (int)$_GET['p'] : 1;
if ($page < 1) $page = 1;
if ($page > $pages) $page = $pages;
$offset = ($page - 1) * $perPage;

$sen_sql = "SELECT student_id, fullname, citizen_id, dob, faculty, major, gpa
            FROM students
            ORDER BY student_id ASC
            LIMIT $perPage OFFSET $offset";
$sens = $conn->query($sen_sql);

function page_link($n){
  $params = ['p'=>$n];
  return 'admin.php?' . http_build_query($params) . '#sensitive';
}

include '_header.php';
?>
<div class="grid">
  <div class="card col-12">
    <h1>ผู้ดูแลระบบ (Admin)</h1>
    <!-- <p class="hint">ตัวอย่างอำนาจของ Admin: ดูข้อมูลอ่อนไหว, ส่งออกข้อมูล, รีเซ็ตรหัสผ่านผู้ใช้, และหมุนรหัส OTP</p> -->
    <div class="row mt-12">
      <span class="badge ok">ผู้ใช้ทั้งหมด: <?=h($tot_users)?></span>
      <span class="badge">นักเรียน: <?=h($tot_students)?></span>
      <span class="badge">อาจารย์ที่ปรึกษา: <?=h($tot_advisors)?></span>
      <span class="badge warn">OTP ปัจจุบัน (admin): <?=h($current_otp)?></span>
    </div>
    <?php if($notice): ?><p class="notice mt-12"><?= $notice ?></p><?php endif; ?>
    <?php if($error): ?><p class="notice mt-12" style="border-color:#ff8;"><b>ข้อผิดพลาด:</b> <?= h($error) ?></p><?php endif; ?>
  </div>

  <!-- Sensitive table with Tabs (10 per tab) -->
  <div class="card col-8" id="sensitive">
    <h2> ข้อมูลนักเรียน</h2>

    <!-- Tabs (Top) -->
    <div class="row" role="tablist" style="flex-wrap:wrap; gap:8px; margin-bottom:10px">
      <?php for($i=1;$i<=$pages;$i++): ?>
        <a href="<?=h(page_link($i))?>"
           class="btn <?= $i===$page ? '' : 'btn-secondary' ?>"
           role="tab" aria-selected="<?= $i===$page ? 'true':'false' ?>"
        >หน้า <?=h($i)?></a>
      <?php endfor; ?>
    </div>

    <table class="table">
      <thead><tr><th>SID</th><th>ชื่อ-สกุล</th><th>เลขบัตร ปชช.</th><th>วันเกิด</th><th>คณะ</th><th>สาขา</th><th>GPA</th></tr></thead>
      <tbody>
      <?php while($sens && $row=$sens->fetch_assoc()): ?>
        <tr>
          <td><?=h($row['student_id'])?></td>
          <td><?=h($row['fullname'])?></td>
          <td><?=h($row['citizen_id'])?></td>
          <td><?=h($row['dob'])?></td>
          <td><?=h($row['faculty'])?></td>
          <td><?=h($row['major'])?></td>
          <td><?=h($row['gpa'])?></td>
        </tr>
      <?php endwhile; ?>
      </tbody>
    </table>

    <!-- Tabs (Bottom) -->
    <!-- <div class="row" role="tablist" style="flex-wrap:wrap; gap:8px; margin-top:12px">
      <?php for($i=1;$i<=$pages;$i++): ?>
        <a href="<?=h(page_link($i))?>"
           class="btn <?= $i===$page ? '' : 'btn-secondary' ?>"
           role="tab" aria-selected="<?= $i===$page ? 'true':'false' ?>"
        >หน้า <?=h($i)?></a>
      <?php endfor; ?>
    </div> -->

    <!-- <div class="debug mt-12"><b>DEBUG SQL:</b> <?=h($sen_sql)?></div> -->

    <div class="row mt-12">
      <a class="btn" href="admin_export_students.php">Export Students (CSV)</a>
      <!-- <span class="hint">* ตัวอย่างการ “เข้าถึง/ส่งออกข้อมูล” ของผู้ดูแล</span> -->
    </div>
  </div>

  <div class="card col-4">
    <h2>Reset รหัสผ่านนักเรียน</h2>
    <form method="post" class="mt-8">
      <input type="hidden" name="action" value="reset_password">
      <label>เลือกรหัสนักศึกษา</label>
      <select name="username">
        <?php while($student_opts && $opt=$student_opts->fetch_assoc()): ?>
          <option value="<?=h($opt['username'])?>"><?=h($opt['student_id'])?> (<?=h($opt['username'])?>)</option>
        <?php endwhile; ?>
      </select>
      <label>รหัสผ่านใหม่</label>
      <input name="newpass" placeholder="เช่น temp1234" required>
      <button class="btn mt-12" type="submit">เปลี่ยนรหัสผ่าน</button>
    </form>
    <!-- <p class="hint mt-12">* แสดงอำนาจของแอดมินในการจัดการบัญชีผู้ใช้</p> -->
  </div>

  <div class="card col-6">
    <h2>ตั้งค่า OTP ของ Admin</h2>
    <form method="post" class="mt-8">
      <input type="hidden" name="action" value="rotate_otp">
      <div class="row">
        <input name="otp" placeholder="ปล่อยว่างเพื่อสุ่ม หรือใส่ 4 หลัก">
        <button class="btn" type="submit">อัปเดต OTP</button>
      </div>
      <!-- <p class="hint mt-8">* ยกตัวอย่างว่าผู้ดูแลสามารถเปลี่ยนปัจจัยยืนยันตัวตนขั้นที่สองได้</p> -->
    </form>
  </div>

  <div class="card col-6">
    <h2>ไฟล์ที่อัปโหลดล่าสุด</h2>
    <table class="table">
      <thead><tr><th>ไฟล์</th><th>ขนาด</th></tr></thead>
      <tbody>
      <?php
        $up = __DIR__ . '/uploads';
        if (is_dir($up)) {
          $list = array_diff(scandir($up), ['.','..']);
          foreach (array_slice(array_reverse($list), 0, 10) as $f) {
            $path = $up . '/' . $f;
            if (is_file($path)) {
              $sz = filesize($path);
              echo '<tr><td><a target="_blank" href="uploads/'.h($f).'">'.h($f).'</a></td><td>'.number_format($sz).' bytes</td></tr>';
            }
          }
        } else {
          echo '<tr><td colspan="2">ไม่มีโฟลเดอร์ uploads</td></tr>';
        }
      ?>
      </tbody>
    </table>
    <!-- <p class="hint">* แสดงผลกระทบจากการเข้าถึงไฟล์ของระบบ</p> -->
  </div>

  <div class="card col-12">
    <h2>Student Management</h2>
    <p class="hint">แก้ไขข้อมูลส่วนตัว/ชื่อ/GPA ของนักเรียน</p>
    <a class="btn" href="admin_students.php">ไปที่หน้าจัดการนักเรียน</a>
  </div>
</div>
<?php include '_footer.php'; ?>
