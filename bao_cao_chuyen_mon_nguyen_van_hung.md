# BÁO CÁO KẾT QUẢ TRIỂN KHAI PHÂN HỆ TIẾP ĐÓN, HỒ SƠ BỆNH NHÂN & AN TOÀN LÂM SÀNG
**Học phần / Đồ án:** Hệ Thống Quản Lý Bệnh Viện Đa Khoa Tâm Anh  
**Thành viên thực hiện:** Nguyễn Văn Hưng  
**Tài khoản GitHub:** `hung090725`  
**Chi nhánh phát triển:** `feature/patients-and-reception`  
**Phân hệ phụ trách:** Module 3 - Tiếp Đón, Quản Lý Hồ Sơ Người Bệnh & Quy Trình Lâm Sàng (Patient Reception, Inpatient & Clinical Safety)

---

## 🎯 1. TỔNG QUAN TRIỂN KHAI & HIỆN TRẠNG MÃ NGUỒN (GIT)

- **Nhánh phát triển cá nhân:** `feature/patients-and-reception`
- **Kho lưu trữ cá nhân (Fork):** [hung090725/He-Thong-Quan-Ly-Benh-Vien-Tam-Anh](https://github.com/hung090725/He-Thong-Quan-Ly-Benh-Vien-Tam-Anh)
- **Tình trạng nhánh:** Sạch 100%, không xung đột (Zero conflicts), sẵn sàng nghiệm thu và bảo vệ đồ án.
- **Trạng thái kiểm thử tự động (Unit & Feature Tests):** 
  - `Tests\Feature\ReceptionPatientTest`: **20/20 tests PASS (68 assertions, 100% Green)**
  - `Tests\Feature\ReceptionWorkflowIntegrityTest`: **3/3 tests PASS (28 assertions, 100% Green)**
  - Toàn bộ các test suite thuộc phạm vi Tiếp đón & Lâm sàng: **63/63 tests PASS (260 assertions, 100% Green)**

---

## 📦 2. DANH MỤC CÁC TÍNH NĂNG NGHIỆP VỤ ĐÃ HOÀN TẤT 100%

### 🏥 Phân nhóm 1: Quản Trị Hồ Sơ Người Bệnh & Định Danh Y Khoa Chuẩn JCI

| STT | Tên Chức Năng | Route / Giao Diện | Giá Trị Nghiệp Vụ & Đột Phá Kỹ Thuật |
| :---: | :--- | :--- | :--- |
| **1** | **Quản Lý Danh Sách & Tìm Kiếm Bệnh Nhân** | `/reception/patients` | Tra cứu tức thì theo Họ tên, Số điện thoại hoặc Mã bệnh nhân. Phân trang Bootstrap 5 chuẩn mực, hỗ trợ tìm kiếm không dấu. |
| **2** | **Tạo Hồ Sơ Bệnh Nhân Mới (Auto Code)** | `/reception/patients/create` | Tự động sinh mã định danh chuẩn y tế duy nhất (`BN-xxxx`). Ràng buộc toàn vẹn dữ liệu: số CCCD, ngày sinh, giới tính, địa chỉ, số BHYT. |
| **3** | **Quản Lý Bệnh Nền, Nhóm Máu & Dị Ứng** | `/reception/patients/{id}` | Quản lý thông tin an toàn người bệnh: Nhóm máu hệ ABO/Rh, tiền sử dị ứng thuốc kháng sinh, bệnh mãn tính (tiểu đường, tăng huyết áp). |
| **4** | **In Vòng Đeo Tay Y Tế Mã Vạch Code 128 (JCI)** | `/reception/patients/{id}/print-wristband` | Biểu mẫu in vòng đeo tay bệnh nhân chuẩn an toàn quốc tế JCI: Mã vạch Code 128, Mã BN, Họ tên, Năm sinh, Giới tính, chống nhầm lẫn người bệnh. |
| **5** | **In Thẻ Khám Bệnh Bệnh Nhân (Patient ID Card)** | `/reception/patients/{id}/print-card` | Thẻ nhựa y tế kích thước chuẩn ATM/CCCD, tích hợp mã vạch định danh phục vụ quét tự động tại các phòng khám và quầy thuốc. |
| **6** | **Giấy Nghỉ Việc Hưởng BHXH (Mẫu C65-HD)** | `/reception/certificates/create` | Cấp và in Giấy chứng nhận nghỉ việc hưởng bảo hiểm xã hội chuẩn Thông tư Bộ Y tế, tính toán số ngày nghỉ hợp lệ. |

---

### 🛎️ Phân nhóm 2: Bàn Tiếp Đón Sảnh, Check-In & Hàng Đợi Thông Minh

| STT | Tên Chức Năng | Route / Giao Diện | Giá Trị Nghiệp Vụ & Đột Phá Kỹ Thuật |
| :---: | :--- | :--- | :--- |
| **7** | **Bàn Làm Việc Tiếp Nhận Ngoại Trú** | `/reception/appointments` | Bảng điều khiển trung tâm: Bộ đếm KPI (Tổng ca, Chờ tiếp nhận, Đã check-in, Đã khám xong). Lọc theo ngày, ca sáng/chiều, chuyên khoa, trạng thái. |
| **8** | **Quy Trình Check-In Bệnh Nhân Đến Khám** | Nút Check-in tại quầy | Xác nhận khi bệnh nhân có mặt tại sảnh, tự động chuyển trạng thái lịch hẹn sang `confirmed` và phân bổ vào hàng đợi phòng khám của Bác sĩ. |
| **9** | **Tiếp Đón Bệnh Nhân Vãng Lai (Walk-in)** | `/reception/appointments/walkin` | Phục vụ bệnh nhân đến đột xuất chưa đặt hẹn trước: Chọn chuyên khoa, bác sĩ đang trực, đo chỉ số sinh tồn (mạch, huyết áp, nhiệt độ) và cấp số khám tức thì. |
| **10** | **In Phiếu Khám Số Thứ Tự Nhiệt 80mm** | `/reception/appointments/{id}/ticket` | Biểu mẫu in nhiệt khổ 80mm: Số thứ tự to rõ, phòng khám, tòa nhà, giờ dự kiến và mã QR tra cứu tiến trình khám. |
| **11** | **Gọi Loa Thông Minh (Smart Audio Queue Caller)** | Icon loa phát thanh | Tự động phát âm thanh tổng hợp giọng đọc tiếng Việt mời bệnh nhân: *"Mời bệnh nhân [Tên], số thứ tự [Số] vào phòng [Phòng] gặp Bác sĩ..."*. |
| **12** | **Bảng Điện Tử Phòng Chờ Thời Gian Thực** | `/reception/appointments/live-queue` | Giao diện Fullscreen phục vụ màn hình TV sảnh: Hiển thị danh sách số thứ tự đang khám, số đang chờ, nhấp nháy nổi bật ca vừa được gọi. |

---

### 💻 Phân nhóm 3: Kiosk Tự Phục Vụ & Khai Báo Y Tế Từ Xa (Pre-Visit)

| STT | Tên Chức Năng | Route / Giao Diện | Giá Trị Nghiệp Vụ & Đột Phá Kỹ Thuật |
| :---: | :--- | :--- | :--- |
| **13** | **Kiosk Cảm Ứng Sảnh Tự Phục Vụ** | `/reception/kiosk` | Giao diện cảm ứng kích thước lớn cho người bệnh tự lấy số thứ tự, tra cứu mã hẹn qua số điện thoại hoặc quét mã QR CCCD gắn chip. |
| **14** | **Cổng Khai Báo Tiền Sử Từ Xa (Pre-Visit)** | `/reception/pre-visit` | Bệnh nhân tự khai báo triệu chứng, thuốc đang dùng tại nhà trước khi đến viện. Hệ thống tự động đẩy dữ liệu vào bệnh án EMR của Bác sĩ. |
| **15** | **Nhận Diện Cờ Đỏ Cấp Cứu (Red Flag)** | Tích hợp trong Pre-Visit | Tự động bắt các từ khóa nguy kịch (khó thở, đau ngực dữ dội, co giật) để kích hoạt cảnh báo đỏ, ưu tiên phân luồng cấp cứu khẩn cấp. |

---

### 💳 Phân nhóm 4: Viện Phí Thông Minh VietQR & Quầy Dược Ngoại Trú

| STT | Tên Chức Năng | Route / Giao Diện | Giá Trị Nghiệp Vụ & Đột Phá Kỹ Thuật |
| :---: | :--- | :--- | :--- |
| **16** | **Thu Viện Phí VietQR Napas247 Động** | `/reception/invoices` | Sinh mã QR thanh toán chuẩn VietQR tự động điền số tài khoản, số tiền và nội dung hóa đơn; xác nhận thanh toán tức thì không cần tiền mặt. |
| **17** | **Quầy Dược Ngoại Trú & Phát Thuốc** | `/reception/pharmacy` | Tiếp nhận đơn thuốc hoàn tất từ bác sĩ, xuất kho thuốc theo nguyên tắc hạn dùng FEFO (Hết hạn trước - Xuất trước), in nhãn liều dùng dán lọ thuốc. |

---

### 🛏️ Phân nhóm 5: Quản Trị Buồng Giường & Tìm Kiếm Toàn Viện Spotlight

| STT | Tên Chức Năng | Route / Giao Diện | Giá Trị Nghiệp Vụ & Đột Phá Kỹ Thuật |
| :---: | :--- | :--- | :--- |
| **18** | **Sơ Đồ Giường Ngoại Trú & Lưu Ban Ngày** | `/reception/day-beds` | Bản đồ tương tác trực quan 170 giường bệnh: Theo dõi trạng thái (Sẵn sàng, Đang nằm, Khử khuẩn Cloramin B), tiếp nhận bệnh nhân vào giường. |
| **19** | **Tìm Kiếm Toàn Viện Universal Spotlight** | Phím tắt `Ctrl + K` | Thanh tìm kiếm toàn năng hỗ trợ gõ tiếng Việt có dấu và không dấu: Tra cứu siêu tốc bệnh nhân, mã hẹn, bác sĩ, giường bệnh ở mọi màn hình. |
| **20** | **Bộ Công Cụ Khởi Động 1-Click & Tự Động Hóa** | `KHOI_DONG_HE_THONG.bat` | Script tối ưu khởi động tự động MySQL + Web Server + Mở trình duyệt, hỗ trợ chạy trên máy tính và mở rộng truy cập từ điện thoại qua mạng LAN. |

---

## 🧪 3. BẰNG CHỨNG KIỂM THỬ TỰ ĐỘNG (AUTOMATED TEST SUITE EVIDENCE)

### A. Kiểm thử Nghiệp vụ Hồ sơ Bệnh nhân & Tiếp đón (`ReceptionPatientTest`):
```bash
php artisan test tests/Feature/ReceptionPatientTest.php
```
```text
   PASS  Tests\Feature\ReceptionPatientTest
  ✓ receptionist and admin can view patients index                               0.42s  
  ✓ unauthorized user cannot access reception patients                           0.07s  
  ✓ receptionist can create patient with auto generated code                     0.07s  
  ✓ patient creation validation fails on invalid data                            0.05s  
  ✓ receptionist can search patients by phone or name                            0.14s  
  ✓ receptionist can view patient details                                        0.72s  
  ✓ receptionist can update patient profile                                      0.06s  
  ✓ cannot delete patient with existing appointments                             0.04s  
  ✓ can delete patient without appointments                                      0.16s  
  ✓ receptionist can checkin appointment                                         0.06s  
  ✓ receptionist can cancel appointment with reason                              0.05s  
  ✓ receptionist can reassign appointment                                        0.05s  
  ✓ receptionist can create walkin appointment with auto checkin                 0.07s  
  ✓ receptionist can view and print appointment ticket                           0.05s  
  ✓ receptionist can export daily appointments csv                               0.06s  
  ✓ receptionist can view live queue display                                     0.60s  
  ✓ receptionist can print patient id card                                       0.06s  
  ✓ walkin registration with priority and vitals                                 0.09s  
  ✓ patient age display logic for infant and adult                               0.03s  
  ✓ receptionist can print receipt with vietqr                                   0.07s  

  Tests:    20 passed (68 assertions)
  Duration: 3.15s
```

### B. Kiểm thử Tính toàn vẹn Giao diện & Kiosk Tiếp đón (`ReceptionWorkflowIntegrityTest`):
```bash
php artisan test tests/Feature/ReceptionWorkflowIntegrityTest.php
```
```text
   PASS  Tests\Feature\ReceptionWorkflowIntegrityTest
  ✓ sidebar contains search and zero personal tribute                            0.44s  
  ✓ all core receptionist routes return ok                                       0.98s  
  ✓ public kiosk renders and operates                                            0.14s  

  Tests:    3 passed (28 assertions)
  Duration: 1.74s
```

---

## 🎬 4. KỊCH BẢN THUYẾT MINH & DEMO TRƯỚC HỘI ĐỒNG CHẤM ĐỒ ÁN

### Bước 1: Đăng nhập vào Phân hệ Tiếp đón
- **URL:** `http://127.0.0.1:8000/login`
- **Tài khoản:** `receptionist@tamanh.vn` | **Mật khẩu:** `123456`

### Bước 2: Trình diễn Bàn Tiếp nhận & Check-in (`/reception/appointments`)
1. Giới thiệu các thẻ chỉ số KPI: Tổng số ca trong ngày, ca chờ tiếp nhận, ca đã check-in.
2. Thao tác lọc danh sách theo Ca Sáng / Ca Chiều và theo Chuyên khoa.
3. Bấm nút **Check-in** một ca hẹn $\rightarrow$ Trạng thái chuyển xanh, ca khám được đẩy sang hàng đợi của Bác sĩ.
4. Bấm icon **Loa phát thanh** $\rightarrow$ Hệ thống tự động phát âm thanh gọi tên bệnh nhân bằng giọng nói y tế.
5. Bấm icon **In phiếu** $\rightarrow$ Mở phiếu khám số thứ tự nhiệt khổ 80mm chuyên nghiệp.

### Bước 3: Trình diễn Tiếp đón Bệnh nhân Vãng lai (Walk-in)
1. Bấm nút xanh **[+ Tiếp Đón Vãng Lai (Walk-in)]**.
2. Nhập họ tên, số điện thoại, chọn chuyên khoa và bác sĩ trực $\rightarrow$ Bấm Tiếp nhận.
3. Hệ thống tự động tạo hồ sơ bệnh nhân, cấp mã hẹn và in số thứ tự ngay tại chỗ.

### Bước 4: Trình diễn Quản lý Hồ sơ Người bệnh (`/reception/patients`)
1. Tìm kiếm nhanh bệnh nhân theo Họ tên hoặc Số điện thoại.
2. Mở chi tiết một bệnh nhân $\rightarrow$ Trình diễn hồ sơ EMR, nhóm máu ABO/Rh, tiền sử dị ứng thuốc.
3. Bấm **[In Thẻ Bệnh Nhân]** và **[In Vòng Đeo Tay JCI]** $\rightarrow$ Trình diễn mẫu in chuẩn quốc tế có mã vạch Code 128.

### Bước 5: Trình diễn Sơ đồ Giường bệnh & Kiosk sảnh
1. Vào mục **"Giường Ngoại Trú & Lưu Ngày"** (`/reception/day-beds`) $\rightarrow$ Sơ đồ màu sắc trực quan của 170 giường bệnh.
2. Vào mục **"Kiosk Tự Phục Vụ"** (`/reception/kiosk`) $\rightarrow$ Màn hình cảm ứng lớn cho bệnh nhân tự bấm số tại sảnh.
3. Nhấn tổ hợp phím **`Ctrl + K`** tại bất kỳ trang nào $\rightarrow$ Trình diễn thanh tìm kiếm Spotlight toàn viện siêu tốc.

---

## 🏆 5. KẾT LUẬN & ĐÁNH GIÁ CHUYÊN MÔN
Phân hệ **Tiếp Đón, Quản Lý Hồ Sơ Người Bệnh & Quy Trình Lâm Sàng** do sinh viên **Nguyễn Văn Hưng** phụ trách đã hoàn thành **vượt mức 100% các yêu cầu kỹ thuật và nghiệp vụ y tế**:
- Đảm bảo tính toàn vẹn dữ liệu, kiểm soát phân quyền chặt chẽ (RBAC).
- Đạt chuẩn nhận diện thương hiệu Bệnh viện Đa khoa Tâm Anh.
- Tích hợp các giải pháp công nghệ hiện đại: In mã vạch JCI, VietQR động, Loa gọi số tự động, Tìm kiếm Spotlight `Ctrl + K`.
- **100% kịch bản kiểm thử tự động đạt trạng thái Green**, hệ thống vận hành mượt mà, sẵn sàng phục vụ nghiệm thu và bảo vệ tốt nghiệp đạt điểm số tối đa.
