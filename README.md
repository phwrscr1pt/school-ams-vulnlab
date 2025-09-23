
# AMS Cybersecurity Lab (PHP + Docker, Modern UI)

> ใช้เพื่อการศึกษา/ฝึก Security เท่านั้น — ห้ามเปิดสาธารณะ

## รัน
```bash
docker compose up -d --build
```
- Web: http://localhost:8080
- Adminer: http://localhost:8081  (Server: `db`, User: `labuser`, Pass: `labpass`, DB: `schoollab`)

## บัญชีทดสอบ
- Admin: `admin` / `admin123` → ไป `otp.php` (OTP เริ่มต้น `0042` และยอมรับ **0000–0200**)
- นักเรียน: `s0001`..`s0010` / `1234` → ไป `profile.php?sid=<SID>`

## จุดฝึก
- Login SQLi (login.php), Admin OTP brute-force (otp.php), IDOR (profile.php?sid=), UNION-based SQLi (advisors.php, columns=6), File Upload bypass MIME (request.php + uploads/.htaccess)

## ฟังก์ชัน Admin (เดโมความสำคัญของสิทธิ์)
- Dashboard: เห็น OTP ปัจจุบัน, ตัวเลขสรุประบบ, ข้อมูลอ่อนไหว 10 รายการ, รายการไฟล์อัปโหลดล่าสุด
- Export Students (CSV)
- Reset รหัสผ่านนักเรียน
- หมุน/ตั้งค่า OTP (4 หลัก)
- Student Management: ค้นหา + แก้ไขข้อมูลส่วนตัว/ชื่อ/GPA รายคน

> UI สมัยใหม่ + Dark Mode toggle (จำค่าด้วย localStorage)
