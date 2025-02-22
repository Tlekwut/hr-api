# Laravel HR Api

Overview
API Backend ที่ใช้ Laravel 10 เพื่อรองรับการทำงานของระบบ HR ที่รวมถึงการลงทะเบียนพนักงาน, การเข้าสู่ระบบด้วย JWT, การจัดการโปรไฟล์

# Features
การลงทะเบียนและเข้าสู่ระบบพนักงาน

ระบบยืนยันตัวตนด้วย JWT Authentication

การจัดการโปรไฟล์พนักงาน (เปลี่ยนรหัสผ่าน, อัปเดตข้อมูลส่วนตัว)

# Installation
1. Clone the repository
2. Install dependencies
3. Configure .env file
   แก้ไขไฟล์ .env และกำหนดค่าต่างๆ เช่น ฐานข้อมูล, JWT_SECRET
4. Run database migrations หรือ Import ฐานข้อมูลจากไฟล์ hr_app_DB.sql
5. Start the development server
