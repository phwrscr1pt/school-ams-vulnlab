<?php
require 'db.php';
$error = '';
$debug_sql = '';

// --- Select SQLi training level (controlled by instructor) ---
$LEVEL = current_training_level();

// Instructor can persistently change level with: ?set_level=low|medium|higher&token=YOUR_SECRET
if (isset($_GET['set_level'], $_GET['token']) && hash_equals(INSTRUCTOR_TOKEN, $_GET['token'])) {
    if (set_training_level($_GET['set_level'])) {
        $LEVEL = current_training_level();
    }
    // Optional: redirect to clean URL
    // header('Location: login.php'); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Intentionally no sanitization for training purposes
    $u = $_POST['username'] ?? '';
    $p = $_POST['password'] ?? '';

    if ($LEVEL === 'low') {
        // LOW: เปราะเหมือนเดิม (string concat) แต่เลือกแถวให้สมจริงขึ้น
        $sql = "SELECT * FROM users WHERE username = '$u' AND password = '$p'";
        $debug_sql = $sql;
        $res = $conn->query($sql);

        if ($res && $res->num_rows > 0) {
            // --- NEW: เลือกแถวที่ username ตรงก่อน ถ้าไม่เจอค่อย fallback ---
            $rows = [];
            while ($r = $res->fetch_assoc()) { $rows[] = $r; }

            $picked = null;

            // 1) ถ้าในผลลัพธ์มีแถวที่ username == $u ให้เลือกแถวนั้น (สมจริง: “ล็อกอินเป็นคนที่เรากรอกชื่อมา”)
            foreach ($rows as $r) {
                if (isset($r['username']) && $r['username'] === $u) { $picked = $r; break; }
            }

            // 2) ถ้ายังไม่เจอ เลือกแถวแรกที่ "ไม่ใช่ admin" (หลีกเลี่ยงยกระดับสิทธิ์โดยไม่ตั้งใจ)
            if ($picked === null) {
                foreach ($rows as $r) {
                    if (isset($r['role']) && $r['role'] !== 'admin') { $picked = $r; break; }
                }
            }

            // 3) สุดท้ายจริง ๆ ค่อยยอมเลือกแถวแรก (กรณีเหลือแต่ admin จริง ๆ)
            if ($picked === null) { $picked = $rows[0]; }

            // --- ดำเนินการล็อกอินตามแถวที่เลือก ---
            $_SESSION['user'] = $picked['username'];
            $_SESSION['role'] = $picked['role'];
            $_SESSION['sid']  = $picked['student_id'];
            if ($picked['role'] === 'admin') {
                $_SESSION['admin_pending'] = true;
                header('Location: otp.php'); exit;
            } else {
                header('Location: profile.php?sid=' . $picked['student_id']); exit;
            }
        } else {
            $error = 'เข้าสู่ระบบไม่สำเร็จ';
        }

    } elseif ($LEVEL === 'medium') {
        // MEDIUM: Still vulnerable (string concat), but enforce single-row with LIMIT 1
        $sql = "SELECT * FROM users WHERE username = '$u' AND password = '$p' LIMIT 1";
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

    } else { // higher
        // HIGHER: Pretend-hardened (MD5) but still concatenated => injectable
        $sql = "SELECT * FROM users WHERE username = '$u' AND password = MD5('$p')";
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

  <div class="hint">
    Demo User <b>usersname : s0001 password : 1234</b>
    <span style="margin-left:8px;padding:.15rem .45rem;border:1px solid #2a2f52;border-radius:6px;font-size:.85em;opacity:.9">
      level: <?=htmlspecialchars($LEVEL ?? 'low')?>
    </span>
  </div>
</div>
<?php include '_footer.php'; ?>
