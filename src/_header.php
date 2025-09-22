<?php if (session_status()===PHP_SESSION_NONE) session_start(); ?>
<!doctype html>
<html lang="th">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>ระบบบริหารงานวิชาการ • AMS</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
  <div class="navbar">
    <img class="logo" src="assets/BARUI.png" onerror="this.src='assets/logo.svg'" alt="logo">
    <div class="brand">ระบบบริหารงานวิชาการ <span class="small">Academic Management System</span></div>
    <div class="tabs" style="margin-left:auto">
      <a href="index.php" class="<?=basename($_SERVER['PHP_SELF'])==='index.php'?'active':''?>">หน้าแรก</a>
      <a href="profile.php" class="<?=basename($_SERVER['PHP_SELF'])==='profile.php'?'active':''?>">ข้อมูลส่วนตัว</a>
      <a href="advisors.php" class="<?=basename($_SERVER['PHP_SELF'])==='advisors.php'?'active':''?>">ค้นหาอาจารย์ที่ปรึกษา</a>
      <a href="request.php" class="<?=basename($_SERVER['PHP_SELF'])==='request.php'?'active':''?>">ยื่นคำร้อง</a>
      <?php if(isset($_SESSION['user'])): ?>
        <span class="badge" style="margin-left:8px"><?=htmlspecialchars($_SESSION['user'])?></span>
        <a href="logout.php" class="btn btn-secondary" style="margin-left:10px">ออกจากระบบ</a>
      <?php else: ?>
        <a href="login.php" class="btn btn-secondary" style="margin-left:10px">เข้าสู่ระบบ</a>
      <?php endif; ?>
    </div>
  </div>
  <div class="container">
