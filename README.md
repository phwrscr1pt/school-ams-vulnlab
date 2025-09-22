
# AMS Cybersecurity Lab (PHP + Docker)

**สำคัญ:** โปรเจกต์นี้ตั้งใจทำให้ *เปราะบาง* เพื่อการฝึกในสภาพแวดล้อมที่ควบคุมได้เท่านั้น อย่านำขึ้นอินเทอร์เน็ตสาธารณะ

## รันอย่างรวดเร็ว (Docker)
1) ติดตั้ง Docker และ Docker Compose
2) แตกไฟล์ zip แล้วเปิดโฟลเดอร์ `cyberlab-ams`
3) รัน:
```bash
docker compose up -d
```
4) เปิดเว็บ:
- Web App: http://localhost:8080
- Adminer (DB): http://localhost:8081  (Server: `db`, User: `labuser`, Pass: `labpass`, DB: `schoollab`)

## บัญชีตัวอย่าง
- Admin: `admin` / `admin123` → จะถูกบังคับไปที่ `otp.php` (OTP ใน DB ตั้งต้นคือ `0042` และ **ยอมรับทุกค่า 0000–0200**)
- นักเรียน: `s0001`..`s0010` / `1234` → ไปหน้า `profile.php?sid=<SID>`

## จุดฝึก
- **Login bypass**: ฟอร์ม login ใช้ query แบบต่อสตริง (`login.php`)
- **Admin OTP brute force**: ไม่มี rate limit (`otp.php`)
- **IDOR**: `profile.php?sid=S0001` สามารถแก้เป็น SID อื่นได้ (ไม่ตรวจว่าเป็นเจ้าของ)
- **SQLi (UNION)**: `advisors.php?q=...` (columns = 6)
- **File Upload → RCE**: `request.php` เช็ค MIME จากผู้ใช้ อัปโหลด `shell.phtml` โดยตั้ง `Content-Type: application/pdf` ได้ และใน `uploads/.htaccess` เปิดให้รัน PHP

> หมายเหตุ: UI โทนสีม่วงเลียนแบบแถบเมนูในภาพแนบ (assets/BARUI.png).

