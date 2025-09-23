<?php
// Homepage -> always start at Login (or route the logged-in user appropriately)
require 'db.php';

// If not logged in, go to login page
if (empty($_SESSION['user'])) {
  header('Location: login.php');
  exit;
}

// If logged in as admin but not yet fully verified, go to OTP
if (($_SESSION['role'] ?? '') === 'admin' && empty($_SESSION['is_admin'])) {
  header('Location: otp.php');
  exit;
}

// Otherwise, go to the student's profile
$sid = $_SESSION['sid'] ?? '';
header('Location: profile.php?sid=' . $sid);
exit;
