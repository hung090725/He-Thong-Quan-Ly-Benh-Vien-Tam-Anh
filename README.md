# HỆ THỐNG QUẢN LÝ BỆNH VIỆN TÂM ANH

> **Đồ án môn học:** Phát triển ứng dụng Web (PHP - Laravel - MySQL)  
> **Kho lưu trữ mã nguồn chính thức (Repository):** [https://github.com/quy0358/He-Thong-Quan-Ly-Benh-Vien-Tam-Anh](https://github.com/quy0358/He-Thong-Quan-Ly-Benh-Vien-Tam-Anh)  
> **Tài liệu kiến trúc chi tiết:** [ke_hoach_trien_khai_benh_vien_tam_anh.md](./ke_hoach_trien_khai_benh_vien_tam_anh.md)

---

## 👥 1. DANH SÁCH THÀNH VIÊN NHÓM VÀ PHÂN CÔNG VAI TRÒ

| STT | Họ và tên | Vai trò chính | Trách nhiệm trọng tâm |
| :---: | :--- | :--- | :--- |
| 1 | **Nguyễn Thành Quý** | **Technical Lead & System Core** | Kiến trúc hệ thống, Khởi tạo dự án, Auth & RBAC Middleware, Master Layouts, Engine Đặt lịch khám, Mail thông báo & Điều phối tích hợp Git |
| 2 | **Nguyễn Văn Đạt** | **Master Data & Public Portal** | Quản lý Chuyên khoa (CRUD), Hồ sơ Bác sĩ, Cấu hình Lịch trực ca trong tuần, Giao diện Trang chủ & Danh bạ Bác sĩ Responsive |
| 3 | **Nguyễn Văn Hưng** | **Patient Management & Reception** | Quản lý Hồ sơ bệnh nhân, Cấp mã bệnh nhân tự động (`BN-YYYYMMDD-XXXX`), Nghiệp vụ bàn tiếp nhận của Lễ tân, Tra cứu nhanh & Check-in |
| 4 | **Võ Lê Gia Bảo** | **Consultation, Records & Analytics** | Màn hình hàng đợi khám của Bác sĩ, Lập bệnh án & Kê đơn thuốc lặp, Upload/Download tệp y tế bảo mật (Policy), Dashboard Quản trị viên |

---

## 🤝 2. CONTRIBUTORS (DANH SÁCH THÀNH VIÊN ĐÓNG GÓP)

Dự án được xây dựng và đóng góp chính thức qua Pull Requests trên GitHub:

| Thành viên | GitHub Username | Vai trò & Đóng góp chính | Trạng thái tích hợp |
| :--- | :--- | :--- | :---: |
| **Nguyễn Thành Quý** | [@quy0358](https://github.com/quy0358) | **Project Lead & Core Architect**: Khung nền tảng Laravel 12, Auth & RBAC, Booking Engine, Master Layouts, Quản lý User | [![Merged](https://img.shields.io/badge/Status-Core%20Maintainer-success)]() |
| **Nguyễn Văn Hưng** | [@hung090725](https://github.com/hung090725) | **Patient Management & Reception Desk**: Hồ sơ bệnh nhân, Nghiệp vụ Check-in, Khám vãng lai, Bảng điện tử phòng chờ, Quầy Dược ngoại trú, Giấy chứng nhận nghỉ việc C65-HD, VietQR, Cảnh báo dị ứng CDSS & Vitals Tracker | [![PR #3 Merged](https://img.shields.io/badge/PR%20%233-Merged-blueviolet)]() |
| **Nguyễn Văn Đạt** | [@jtadd123](https://github.com/jtadd123) | **Master Data & Public Portal**: Quản lý Chuyên khoa (CRUD), Bác sĩ, Lịch trực ca tuần, Danh bạ bác sĩ công khai & Trang chủ | [![PR #1 Merged](https://img.shields.io/badge/PR%20%231-Merged-blueviolet)]() |
| **Võ Lê Gia Bảo** | `@vlgbao` | **Consultation, Records & Analytics**: Bàn khám bác sĩ, Bệnh án điện tử, Đơn thuốc lặp, File y tế bảo mật, Dashboard thống kê | [![In Progress](https://img.shields.io/badge/Status-In%20Progress-orange)]() |

---

## 🛠️ 3. CÔNG NGHỆ VÀ MÔI TRƯỜNG PHÁT TRIỂN

- **Ngôn ngữ & Nền tảng:** PHP 8.2+ (chạy qua XAMPP hoặc standalone)
- **Framework phía máy chủ:** Laravel 10 LTS / 11
- **Cơ sở dữ liệu:** MySQL 8.0 / MariaDB
- **Giao diện người dùng:** Laravel Blade Engine + Bootstrap 5.3 (CSS & JS Bundle) + Bootstrap Icons
- **Quản lý phiên & bảo mật:** Laravel Session, CSRF Protection, Password Hashing (Bcrypt), Form Requests Validation
- **Gửi Email thông báo:** Mailable / Queue (môi trường kiểm thử qua Mailpit hoặc Log driver)
- **Lưu trữ tệp tin y tế:** Laravel Local Storage (phân vùng riêng tư `storage/app/private/medical_records`, bảo vệ bằng Policy)
- **Quản lý mã nguồn:** Git & GitHub

---

## 🏛️ 4. SƠ ĐỒ CƠ SỞ DỮ LIỆU (ERD)

```mermaid
erDiagram
    USERS ||--o| DOCTORS : "has profile (1:1)"
    USERS ||--o| PATIENT_PROFILES : "has account (0..1:1)"
    USERS ||--o{ APPOINTMENTS : "created by (1:N)"
    SPECIALTIES ||--o{ DOCTORS : "belongs to (1:N)"
    SPECIALTIES ||--o{ APPOINTMENTS : "booked under (1:N)"
    DOCTORS ||--o{ DOCTOR_SCHEDULES : "has weekly shifts (1:N)"
    DOCTORS ||--o{ APPOINTMENTS : "assigned to (1:N)"
    DOCTORS ||--o{ MEDICAL_RECORDS : "examines (1:N)"
    PATIENT_PROFILES ||--o{ APPOINTMENTS : "schedules (1:N)"
    PATIENT_PROFILES ||--o{ MEDICAL_RECORDS : "receives (1:N)"
    APPOINTMENTS ||--o| MEDICAL_RECORDS : "results in (1:1)"
    MEDICAL_RECORDS ||--o{ PRESCRIPTIONS : "contains (1:N)"
    MEDICAL_RECORDS ||--o{ ATTACHMENTS : "attaches files (1:N)"
```

### Danh sách 9 bảng dữ liệu cốt lõi:
1. `users`: Tài khoản đăng nhập, mật khẩu mã hóa và vai trò (`admin`, `receptionist`, `doctor`, `patient`).
2. `specialties`: Danh mục chuyên khoa (Tim mạch, Nhi khoa, Tai mũi họng,...).
3. `doctors`: Thông tin bác sĩ, học vị, phòng khám, giá khám, liên kết 1-1 với `users`.
4. `patient_profiles`: Hồ sơ hành chính bệnh nhân, mã số `patient_code` duy nhất.
5. `doctor_schedules`: Lịch làm việc định kỳ theo thứ trong tuần và ca sáng/chiều.
6. `appointments`: Lịch khám bệnh, mã số hẹn, trạng thái (`pending`, `confirmed`, `completed`, `cancelled`).
7. `medical_records`: Hồ sơ bệnh án, chẩn đoán, triệu chứng, ghi chú của bác sĩ.
8. `prescriptions`: Chi tiết đơn thuốc điều trị (tên thuốc, liều lượng, cách dùng).
9. `attachments`: Quản lý tệp chụp X-quang, xét nghiệm bảo mật với phân quyền tải an toàn.

---

## 📋 5. BẢNG PHÂN CÔNG NHIỆM VỤ CHI TIẾT TỪNG THÀNH VIÊN

### 1. NGUYỄN THÀNH QUÝ (Technical Lead)
- **Phạm vi phụ trách:**
  - Khởi tạo khung dự án Laravel, Master Layouts (`layouts/master.blade.php`, `layouts/admin.blade.php`), Components dùng chung (`<x-alert>`, `<x-status-badge>`).
  - Lập trình Auth: Đăng nhập, đăng xuất, đổi mật khẩu, Remember Me.
  - Viết Middleware `CheckRole` phân quyền 4 vai trò.
  - Migration & Seeder bảng: `users`, `appointments`.
  - Xây dựng Booking Engine: Trang đặt lịch trực tuyến, kiểm tra tránh trùng ca và quá tải khám của bác sĩ (`BookingController`, `StoreAppointmentRequest`).
  - Cấu hình gửi mail xác nhận đặt lịch `AppointmentCreatedMail`.
  - Màn hình Portal lịch sử khám dành cho bệnh nhân.
- **Tiêu chí hoàn thành:** Đăng nhập đúng phân quyền, đặt lịch thành công sinh mã và gửi email, chặn trùng ca khám.

### 2. NGUYỄN VĂN ĐẠT (Chuyên khoa & Bác sĩ)
- **Phạm vi phụ trách:**
  - Migration & Seeder bảng: `specialties`, `doctors`, `doctor_schedules`.
  - Thiết lập quan hệ Eloquent giữa Chuyên khoa, Bác sĩ, Lịch trực và User.
  - Xây dựng chức năng Admin quản lý Chuyên khoa (`SpecialtyController`).
  - Xây dựng chức năng Admin quản lý Bác sĩ & Lịch trực tuần (`DoctorManagementController`).
  - Thiết kế và lập trình giao diện công khai: Trang chủ (`public.home`), Giới thiệu viện, Danh bạ bác sĩ có bộ lọc chuyên khoa (`DoctorPublicController`).
  - Viết API/Route nội bộ lấy bác sĩ theo chuyên khoa phục vụ dropdown đặt lịch.
- **Tiêu chí hoàn thành:** CRUD chuyên khoa và bác sĩ mượt mà, cấu hình lịch trực tuần chính xác, giao diện công khai responsive đẹp mắt.

### 3. NGUYỄN VĂN HƯNG (Hồ sơ Bệnh nhân & Quầy Lễ tân)
- **Phạm vi phụ trách:**
  - Migration & Seeder bảng: `patient_profiles`.
  - Viết thuật toán tự động sinh mã hồ sơ bệnh nhân duy nhất dạng `BN-YYYYMMDD-XXXX`.
  - Xây dựng trang quản lý danh sách bệnh nhân: Phân trang, tìm kiếm tức thì theo SĐT, Họ tên, CCCD (`PatientController`).
  - Lập trình giao diện tạo mới / cập nhật hồ sơ bệnh nhân kèm Server-side validation.
  - Xây dựng nghiệp vụ Bàn tiếp nhận của Lễ tân (`ReceptionAppointmentController`):
    - Xem danh sách ca khám theo ngày.
    - Check-in tiếp đón bệnh nhân đến viện -> Chuyển trạng thái sang `confirmed`.
    - Tiếp nhận khách vãng lai (walk-in) chưa đặt hẹn trước.
    - Đổi ca khám hoặc hủy lịch hẹn có ghi nhận lý do.
- **Tiêu chí hoàn thành:** Tra cứu bệnh nhân theo SĐT nhanh chóng, quy trình Check-in cập nhật tức thì trạng thái sang hàng đợi bác sĩ.

### 4. VÕ LÊ GIA BẢO (Khám bệnh, Bệnh án & Thống kê)
- **Phạm vi phụ trách:**
  - Migration & Seeder bảng: `medical_records`, `prescriptions`, `attachments`.
  - Xây dựng màn hình Hàng đợi ca khám của Bác sĩ trong ngày (`DoctorQueueController`).
  - Lập trình giao diện Khám bệnh, Chẩn đoán & Kê đơn thuốc lặp (`MedicalRecordController`, `Prescription`).
  - Xử lý lưu bệnh án và đơn thuốc trong một Database Transaction (`DB::transaction`).
  - Lập trình tính năng upload và download tệp tin y tế bảo mật (`AttachmentController`):
    - Kiểm duyệt định dạng (`pdf, jpg, png`) và kích thước (<3MB).
    - Viết `AttachmentPolicy` ngăn chặn người ngoài hoặc bệnh nhân khác tải trộm tài liệu y tế (HTTP 403).
  - Lập trình Dashboard trung tâm Admin: Thống kê số lượng bệnh nhân, bác sĩ, lịch hẹn, tỷ lệ khám hoàn thành (`AdminDashboardController`).
- **Tiêu chí hoàn thành:** Bác sĩ lập bệnh án trơn tru, file lưu private an toàn có phân quyền, số liệu Dashboard phản ánh chuẩn từ database.

---

## 🌿 6. QUY TRÌNH PHỐI HỢP GIT CỦA NHÓM (GIT WORKFLOW)

### Mô hình phân nhánh:
- `main`: Nhánh ổn định cao nhất, dùng để nộp bài và demo trước giảng viên.
- `develop`: Nhánh làm việc chung của nhóm. Tất cả tính năng đều phải merge vào đây.
- Các nhánh tính năng độc lập của từng thành viên:
  - `feature/auth-and-booking` *(Quý)*
  - `feature/doctors-and-specialties` *(Đạt)*
  - `feature/patients-and-reception` *(Hưng)*
  - `feature/examinations-and-reports` *(Bảo)*

### Các lệnh Git mẫu hàng ngày cho thành viên:

```bash
# 1. Chuyển sang nhánh develop và lấy code mới nhất về
git checkout develop
git pull origin develop

# 2. Tạo nhánh làm việc của mình (hoặc chuyển sang nhánh mình đang làm)
git checkout -b feature/ten-tinh-nang

# 3. Sau khi hoàn thành code và kiểm tra chạy thử không lỗi:
git add .
git commit -m "feat: [ten-thanh-vien] mo ta ngan gon chuc nang da lam"

# 4. Cập nhật nhánh của mình theo develop mới nhất trước khi push:
git checkout develop
git pull origin develop
git checkout feature/ten-tinh-nang
git merge develop

# 5. Đẩy code lên GitHub và tạo Pull Request vào nhánh develop:
git push origin feature/ten-tinh-nang
```

> **Lưu ý phòng tránh Conflict:** 
> - Mỗi thành viên làm việc trong thư mục Controller và View của riêng mình.
> - Tuyệt đối **không tự ý sửa các file Migration cũ** đã merge.
> - Mọi Pull Request vào nhánh `develop` sẽ do Tech Lead (Quý) kiểm tra và duyệt gộp.

---

## 🚀 7. HƯỚNG DẪN CÀI ĐẶT DỰ ÁN TRÊN MÁY WINDOWS (XAMPP)

### Yêu cầu tiên quyết:
- Đã cài đặt XAMPP (chạy Apache và MySQL).
- Phiên bản PHP tối thiểu 8.2 (đã bật các extension: `pdo_mysql`, `mbstring`, `fileinfo`, `openssl`).
- Đã cài Composer trên máy.

### Các bước cài đặt:

```bash
# Bước 1: Clone mã nguồn về máy
git clone https://github.com/quy0358/He-Thong-Quan-Ly-Benh-Vien-Tam-Anh.git
cd He-Thong-Quan-Ly-Benh-Vien-Tam-Anh

# Bước 2: Cài đặt các thư viện phụ thuộc của Laravel
composer install

# Bước 3: Tạo file cấu hình môi trường .env
copy .env.example .env
php artisan key:generate

# Bước 4: Cấu hình cơ sở dữ liệu trong file .env
# Mở file .env và chỉnh sửa:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=db_benh_vien_tam_anh
# DB_USERNAME=root
# DB_PASSWORD=

# Bước 5: Tạo liên kết thư mục lưu trữ file
php artisan storage:link

# Bước 6: Khởi chạy Migration và nạp dữ liệu mẫu
php artisan migrate:fresh --seed

# Bước 7: Khởi chạy máy chủ phát triển
php artisan serve
```

Truy cập hệ thống tại: `http://localhost:8000`

---

## 🔑 8. TÀI KHOẢN MẪU DÙNG ĐỂ KIỂM THỬ VÀ TRÌNH DIỄN (SEED DATA)

Mật khẩu mặc định cho tất cả các tài khoản thử nghiệm: **`123456`**

| Vai trò | Email đăng nhập | Quyền hạn chính |
| :--- | :--- | :--- |
| **Quản trị viên (Admin)** | `admin@tamanh.vn` | Quản trị tài khoản, chuyên khoa, bác sĩ, xem dashboard thống kê |
| **Nhân viên Lễ tân (Receptionist)** | `receptionist@tamanh.vn` | Quản lý hồ sơ bệnh nhân, check-in tiếp đón, điều phối lịch hẹn |
| **Bác sĩ (Doctor)** | `doctor@tamanh.vn` | Xem hàng đợi khám, lập bệnh án, kê đơn thuốc, đính kèm kết quả xét nghiệm |
| **Bệnh nhân (Patient)** | `patient@gmail.com` | Tra cứu bác sĩ, đặt lịch khám trực tuyến, xem lịch sử khám bệnh |

---

## 📅 9. TIẾN ĐỘ THỰC HIỆN 4 TUẦN

- **Tuần 1:** Thống nhất kiến trúc, tạo khung Laravel, chạy thành công 9 migration & seeders, dựng Auth & Master Layouts.
- **Tuần 2:** Lập trình các module độc lập: Chuyên khoa/Bác sĩ (Đạt), Hồ sơ bệnh nhân/Lễ tân (Hưng), Đặt lịch khám (Quý), Bàn khám/Bệnh án (Bảo).
- **Tuần 3:** Tích hợp chuỗi nghiệp vụ liền mạch: Đặt hẹn -> Check-in -> Khám bệnh -> Lưu bệnh án; Tích hợp upload file an toàn & Gửi email.
- **Tuần 4:** Rà soát bảo mật, tối ưu Responsive, chuẩn bị kịch bản thuyết trình và tổng duyệt demo trước giảng viên.

---
*Dự án phục vụ mục đích học tập và nghiên cứu môn học Phát triển ứng dụng Web.*
