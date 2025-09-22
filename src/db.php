<?php
if (session_status()===PHP_SESSION_NONE) session_start();
$DB_HOST = getenv('DB_HOST') ?: '127.0.0.1';
$DB_USER = getenv('DB_USER') ?: 'labuser';
$DB_PASS = getenv('DB_PASS') ?: 'labpass';
$DB_NAME = getenv('DB_NAME') ?: 'schoollab';
$conn = @new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($conn->connect_errno) { die("DB connection failed: ".$conn->connect_error); }
$conn->set_charset('utf8mb4');
?>
