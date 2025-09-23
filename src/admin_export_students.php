<?php
require 'db.php';
if (empty($_SESSION['is_admin'])) { http_response_code(403); echo "Forbidden"; exit; }
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="students_export.csv"');
$out = fopen('php://output', 'w');
fputcsv($out, ['student_id','fullname','faculty','major','program','dob','citizen_id','nationality','religion','blood','parent_father','parent_mother','address','gpa']);
$res=$conn->query("SELECT student_id, fullname, faculty, major, program, dob, citizen_id, nationality, religion, blood, parent_father, parent_mother, address, gpa FROM students ORDER BY student_id ASC");
while($res && $row=$res->fetch_row()){ fputcsv($out, $row); }
fclose($out);
