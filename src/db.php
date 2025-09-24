<?php
if (session_status()===PHP_SESSION_NONE) session_start();
$DB_HOST = getenv('DB_HOST') ?: '127.0.0.1';
$DB_USER = getenv('DB_USER') ?: 'labuser';
$DB_PASS = getenv('DB_PASS') ?: 'labpass';
$DB_NAME = getenv('DB_NAME') ?: 'schoollab';
$conn = @new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($conn->connect_errno) { die("DB connection failed: ".$conn->connect_error); }
$conn->set_charset('utf8mb4');

/* ====================
   SQLi Training Level Control (Instructor)
   - Set the level once; persists to training_level.cfg
   - Levels: low | medium | higher
   - Change the secret below before class.
   ==================== */

if (!defined('INSTRUCTOR_TOKEN')) {
    // TODO: CHANGE THIS SECRET to a long random string before using in class
    define('INSTRUCTOR_TOKEN', 'f3a77f0f18c44c0e9b40a2d1dcf2a6d1a1a2c763e3af4b51a0df4c6fcd2be91e');
}
if (!defined('TRAINING_LEVEL_DEFAULT')) {
    // Default level if no file/env set. You can also set env SQLI_LEVEL
    define('TRAINING_LEVEL_DEFAULT', getenv('SQLI_LEVEL') ?: 'low');
}

$__LEVEL_FILE = __DIR__ . '/training_level.cfg';

function __level_is_valid($lvl) {
    return in_array($lvl, ['low','medium','higher'], true);
}

function current_training_level() {
    global $__LEVEL_FILE;
    if (is_readable($__LEVEL_FILE)) {
        $lvl = trim(@file_get_contents($__LEVEL_FILE));
        if (__level_is_valid($lvl)) return $lvl;
    }
    return TRAINING_LEVEL_DEFAULT;
}

function set_training_level($lvl) {
    global $__LEVEL_FILE;
    if (__level_is_valid($lvl)) {
        @file_put_contents($__LEVEL_FILE, $lvl . PHP_EOL, LOCK_EX);
        return true;
    }
    return false;
}
?>