# 🏥 ĐẶC TẢ NGHIỆP VỤ Y KHOA & HỆ THỐNG PHÂN HỆ BỆNH VIỆN ĐA KHOA TÂM ANH
## HỆ THỐNG QUẢN LÝ BỆNH VIỆN ĐA KHOA TÂM ANH (ENTERPRISE SMART HOSPITAL SUITE)

> **Dự án:** Hệ Thống Quản Lý Bệnh Viện Đa Khoa Tâm Anh  
> **Nhánh phát triển:** `feature/patients-and-reception`  
> **Khung pháp lý & Chuẩn mực:** Luật Khám bệnh, chữa bệnh 2023 • Nghị định 98/2021/NĐ-CP • Thông tư 32/2023/TT-BYT • Thông tư 43/2018/TT-BYT • Thông tư 20/2017/TT-BYT • Thông tư 26/2013/TT-BYT • Quyết định 4210/QĐ-BYT • Quyết định 5631/QĐ-BYT • Bộ 83 Tiêu chí Chất lượng BV Việt Nam • Chuẩn An toàn Người bệnh Quốc tế Joint Commission International (JCI 8th Ed) • WHO Surgical Safety Checklist • AAMI/ISO 23500 • IEC 62353.

---

## 🌟 1. TỔNG QUAN PHẠM VI NGHIỆP VỤ VÀ PHÂN HỆ Y KHOA

Hệ thống được thiết kế và triển khai phục vụ quy trình khám chữa bệnh ngoại trú và nội trú tại Bệnh viện Đa khoa Tâm Anh với kiến trúc module mở rộng:

```
╔═══════════════════════════════════════════════════════════════════════════════════════════════════════════════╗
║                  BẢN ĐỒ TỔNG THỂ CÁC PHÂN HỆ NGHIỆP VỤ Y TẾ BỆNH VIỆN ĐA KHOA TÂM ANH                         ║
╠═══════════════════════════════════════════════════════════════════════════════════════════════════════════════╣
║ 1. KIOSK & TIẾP ĐÓN: Check-in vãng lai/ca hẹn, Quét CCCD, In nhiệt 80mm có QR Live, Gọi loa AI đa giọng đọc  ║
║ 2. CẤP CỨU & TRIAGE: Triage ATS 5 cấp, Báo động đỏ toàn viện Code Red, NEWS2 & Đội phản ứng nhanh RRT (3 phút)║
║ 3. DƯỢC & AN TOÀN THUỐC: Tem nhãn phụ Smart Bottle, CDSS dị ứng chéo thuốc, Tủ thuốc độc TT20 ký duyệt kép 3 người║
║ 4. CẬN LÂM SÀNG & MÁU: Cảnh báo giá trị nguy kịch Panic Value, Truyền máu an toàn tại giường đối chiếu chéo Rh║
║ 5. CHUỖI LẠNH & TIỆT KHUẨN: Chuỗi lạnh IoT vắc-xin 2-8°C tự khóa an toàn, Quản lý mẻ mổ CSSD Autoclave/Plasma ║
║ 6. BUỒNG BỆNH & LỌC MÁU: Lọc máu thận chu kỳ UF, Sơ đồ buồng bệnh trực quan & Giường khử khuẩn Cloramin B     ║
║ 7. DẪN ĐƯỜNG & KHAI BÁO: Bản đồ trong nhà 3D Wayfinding SVG, Khai báo triệu chứng từ xa Pre-Visit đồng bộ EMR ║
║ 8. TÀI CHÍNH & BHYT: Giấy chuyển tuyến Mẫu 40/BYT, Giám định XML 4210, Ví tạm ứng viện phí VietQR Napas247    ║
║ 9. PHÁP LÝ & AN TOÀN: Sao lục bệnh án Đ.69 Luật KCB 2023, Niêm phong tài sản, Thẻ thăm JCI (max 2 người)     ║
║ 10. NGOẠI VIỆN & SỰ CỐ: Đội xe cấp cứu Mobile ICU tính cước km, Sự cố Near-Miss & RCA 5 Whys (TT 43/2018/TT-BYT)║
║ 11. PHÒNG MỔ SMART OR: Kíp mổ 7 bước, Kiểm đếm gạc RFID chống bỏ quên dị vật, Hồi tỉnh Aldrete, Sảnh chờ che tên║
║ 12. AI & Y SINH NÂNG CAO: PACS-AI ResNet-152 Triage X-quang, Sepsis qSOFA, Bản đồ gen PGx, CMMS bảo trì NĐ 98 ║
║ 13. DƯỢC LÂM SÀNG & TPN: TDM nồng độ hẹp Vancomycin, Dinh dưỡng tĩnh mạch All-in-One, Báo cáo ADR Naranjo     ║
║ 14. PHCN, YHCT & ESG: Thang FIM, Thuốc thang kỵ Thập Bát Phản, Nomogram Bhutani vàng da, Sữa mẹ Pasteur, ALARA║
║ 15. HỒI SỨC SỐNG CAO CẤP: Code Blue ACLS CPR chất lượng cao, Hệ thống khí y tế trung tâm LOX, ROTEM đông máu  ║
║ 16. ROBOT & HẬU CẦN TỰ ĐỘNG: Đội xe AGV/AMR tự gọi thang máy, Thoi khí nén PTS chống vỡ hồng cầu (G < 2.5G)  ║
║ 17. NĂNG LƯỢNG ESG & VI SÓNG: Microgrid Tier-4 ATS <= 10s, Nghiền hấp vi sóng rác thải y tế lây nhiễm tại chỗ  ║
║ 18. NƯỚC RO & TÀI SẢN BLE: Trạm nước siêu sạch lọc thận AAMI/ISO 23500, Định vị thiết bị di động BLE Beacon   ║
║ 19. BẾP ĂN HACCP & RFID ĐỒ VẢI: Bếp ăn dinh dưỡng tâm >= 75°C, Vòng đời đồ vải gắn chip RFID giặt 100 lần    ║
║ 20. CHỈ HUY THẢM HỌA & XUẤT VIỆN: Chỉ huy thảm họa HICS huy động 50 giường, Két niêm phong tư trang mở khóa kép║
║     Sinh thiết lạnh phòng mổ Frozen Section <= 20p, Giám sát mệt mỏi ca trực y bác sĩ, Meds-to-Beds trước 10h ║
╚═══════════════════════════════════════════════════════════════════════════════════════════════════════════════╝
```

---

## 📊 2. DANH MỤC CHI TIẾT CÁC PHÂN HỆ, CƠ CHẾ RÀNG BUỘC & NGOẠI LỆ

| STT | Phân Hệ Nghiệp Vụ | Tiêu Chuẩn & Căn Cứ Y Khoa | Cơ Chế Bảo Vệ, Ràng Buộc & Ngoại Lệ Được Xử Lý |
| :---: | :--- | :--- | :--- |
| **1** | **Tiếp đón & Quản lý Hồ sơ Bệnh nhân** | Luật KCB 2023, JCI IPSG.1 | Chặn ngày sinh tương lai (`dob <= today`), Regex SĐT Việt Nam, Validate CCCD/BHYT không ký tự đặc biệt, Unique Patient Code chống race-condition. |
| **2** | **Đo chỉ số Sinh tồn (Vital Signs)** | Bộ 83 Tiêu chí BYT, JCI | Bắt buộc Huyết áp tâm thu > Huyết áp tâm trương, SpO2 trong khoảng 50-100%, Thân nhiệt 30-45°C, Nhịp tim 30-250 l/p, Cân nặng 1-300kg. |
| **3** | **Cảnh báo sớm NEWS2 & Đội phản ứng nhanh RRT** | Royal College of Physicians | Điểm NEWS2 $\ge 7$ hoặc 1 chỉ số đạt 3 điểm tự động kích hoạt Đội RRT đến hiện trường trong $\le 3$ phút, ghi nhận hồ sơ cấp cứu. |
| **4** | **An toàn Truyền máu tại giường** | Thông tư 26/2013/TT-BYT | Chặn tuyệt đối bất đồng ABO/Rh (Rh- không nhận Rh+), bắt buộc đối chiếu kép 2 Điều dưỡng (`different:nurse_1`), ghi nhận sinh hiệu phút 15. |
| **5** | **Tủ thuốc gây nghiện & Thuốc độc** | Thông tư 20/2017/TT-BYT | Bắt buộc ký duyệt kép 3 người (Bác sĩ, Điều dưỡng, Người làm chứng), khóa hàng `lockForUpdate()`, quy trình thu hồi và tiêu hủy vỏ ống thuốc. |
| **6** | **Chuỗi cung ứng lạnh Vắc-xin IoT** | Chuẩn GSP & WHO Cold Chain | Tự động giám sát nhiệt độ 2.0°C – 8.0°C. Khi vượt ngưỡng, tự động khóa tủ, ngăn chặn xuất cấp và phát cảnh báo đỏ SMS/Dashboard. |
| **7** | **Thẻ thăm bệnh điện tử JCI** | Kiểm soát nhiễm khuẩn JCI | Giới hạn tối đa 2 người thăm/giường bệnh nội trú cùng thời điểm. Yêu cầu sàng lọc triệu chứng nhiễm khuẩn hô hấp trước khi cấp thẻ. |
| **8** | **Ví viện phí Một chạm & Hoàn ứng** | Chuẩn Hạch toán kép | 100% sử dụng `DB::transaction`. Số tiền nạp tối thiểu 50.000 VNĐ; số tiền hoàn ứng ràng buộc không vượt quá số dư hiện tại (`max:balance`), không âm ví. |
| **9** | **Giám định BHYT thời gian thực** | Quyết định 4210/QĐ-BYT | Cảnh báo xuất toán khi chỉ định MRI/CT không khớp mã ICD-10, kháng sinh thế hệ 3/4 thiếu biên bản hội chẩn. Xuất chuẩn XML 4210 khi hồ sơ Clean. |
| **10**| **Sao lục Bệnh án điện tử** | Điều 69 Luật KCB 2023 | Ràng buộc chỉ 4 đối tượng hợp pháp được nộp đơn: Bản thân người bệnh, Người giám hộ, Cơ quan tố tụng, Giám định viên bảo hiểm. |
| **11**| **Báo cáo sự cố Near-Miss & RCA 5 Whys**| Thông tư 43/2018/TT-BYT | Phân cấp mức độ sự cố NC0-NC3, bảo mật danh tính người báo cáo (Anonymous reporting), quy trình phân tích nguyên nhân gốc rễ và khắc phục. |
| **12**| **Đội xe cấp cứu cơ động Mobile ICU** | Cấp cứu 115 Quốc gia | Tự động tính cước km minh bạch (Cước mở cửa chuẩn/ICU + 25.000 VNĐ/km), quản lý định vị và trạng thái kíp trực cấp cứu. |
| **13**| **Phòng mổ thông minh Smart OR** | WHO Surgical Safety | Bắt buộc thời gian nhịn ăn uống trước mổ 4-24h, Bảng kiểm đếm gạc RFID chống bỏ quên dị vật, Thang điểm hồi tỉnh Aldrete $\ge 9$ mới xuất viện. |
| **14**| **Dược lâm sàng TDM & Dị ứng chéo** | CPIC & Dược thư Quốc gia | TDM Vancomycin nồng độ đáy 15-20 mcg/mL, CDSS phát hiện dị ứng chéo Penicillin/Cephalosporin, Thập Bát Phản trong thuốc thang YHCT. |
| **15**| **Đội xe Robot tự hành AGV/AMR** | Hospital Logistics AMR | Pin $\le 20\%$ tự động ngắt hành trình quay về Dock sạc tầng hầm B1, cảm biến LIDAR tránh va chạm, tích hợp gọi thang máy số. |
| **16**| **Đường ống khí nén chuyển mẫu PTS** | ASTM F2369 / ISO 15189 | Vận tốc 6-8 m/s, cảm biến gia tốc $G$-force $< 2.5\text{ G}$ nhằm ngăn chặn vỡ màng hồng cầu (Hemolysis-free). |
| **17**| **Lưới điện vi mô Microgrid Tier-4** | NFPA 99 / NFPA 110 Tier-4 | Tự động chuyển nguồn ATS $\le 10$ giây khi mất lưới quốc gia, duy trì UPS phòng mổ $\ge 30$ phút, theo dõi tín chỉ giảm phát thải carbon ESG. |
| **18**| **Nghiền hấp vi sóng rác lây nhiễm** | Chuẩn Xử lý chất thải BYT | Vi sóng nhiệt độ $\ge 100^\circ\text{C}$, giảm $80\%$ thể tích rác lây nhiễm biến thành rác trơ vô trùng an toàn. |
| **19**| **Trạm nước siêu tinh khiết lọc thận** | AAMI/ISO 23500 | Độ dẫn $< 10\text{ }\mu\text{S/cm}$, vi khuẩn $< 100\text{ CFU/ml}$, Endotoxin $< 0.25\text{ EU/ml}$. Tự động ngắt khẩn cấp khi vi phạm. |
| **20**| **Quản lý thiết bị y sinh CMMS** | Tiêu chuẩn IEC 62353 | Dòng rò rỉ $< 100\text{ }\mu\text{A}$, điện trở đất an toàn $< 0.2\text{ }\Omega$. Quá ngưỡng tự động cách ly niêm phong thiết bị. |
| **21**| **Bếp ăn dinh dưỡng bệnh lý HACCP** | Tiêu chuẩn ISO 22000 & HACCP | Nhiệt độ nấu chín tâm $\ge 75^\circ\text{C}$, giữ nóng $\ge 65^\circ\text{C}$, lưu mẫu thực phẩm 24h đối chứng an toàn vệ sinh. |
| **22**| **Quản lý đồ vải gắn chip RFID** | Tiệt trùng chống nhiễm khuẩn | Chip RFID UHF chịu nhiệt $200^\circ\text{C}$, giới hạn 100 chu kỳ giặt, tự động loại bỏ đồ vải sờn rách hết hạn sử dụng. |
| **23**| **Trung tâm chỉ huy thảm họa HICS** | HICS IV Hospital Command | Kích hoạt kịch bản Code Silver/Black/Yellow/Orange, cơ chế huy động 50 giường cấp cứu trong 15 phút, hạ cấp an toàn về Standby. |
| **24**| **Két niêm phong tư trang mở khóa kép**| Bảo mật tài sản người bệnh | Túi niêm phong Tamper-Evident có barcode, mở khóa kép bắt buộc 2 nhân viên xác thực chéo, ký bàn giao thân nhân minh bạch. |
| **25**| **Sinh thiết lạnh tức thì Frozen Section**| Giải phẫu bệnh & Ngoại khoa | Vận chuyển mẫu $\le 3$ phút, Telepathology trả kết quả diện cắt âm tính $\le 20$ phút trực tiếp cho phẫu thuật viên trong phòng mổ. |
| **26**| **Giám sát mệt mỏi ca trực Y bác sĩ** | The Joint Commission Fatigue | Ca trực liên tục $\ge 18\text{h}$ hoặc Fatigue Risk Index $\ge 70$ bắt buộc kích hoạt lệnh tạm đình chỉ mổ để ngủ bù chống sai sót. |
| **27**| **Khu chờ xuất viện & Meds-to-Beds** | Quản trị năng lực giường bệnh| Giải phóng buồng bệnh trước 10h sáng, cấp phát thuốc tận ghế chờ xuất viện, tăng $25\%$ hiệu suất quay vòng giường bệnh nội viện. |

---

## 🧪 3. HỆ THỐNG KIỂM THỬ TỰ ĐỘNG (AUTOMATED TEST SUITE)

Toàn bộ hệ thống được bảo vệ bởi bộ kịch bản kiểm thử tự động (Unit & Feature Tests), bao phủ toàn bộ các luồng nghiệp vụ thành công lẫn các trường hợp ngoại lệ (Edge cases / Validation failures / Unauthorized access):

```bash
php artisan test --stop-on-failure
```

Các luồng kiểm thử cốt lõi:
- **Tiếp đón & Quản lý Người bệnh:** Validation CCCD/BHYT, sinh mã BN duy nhất, in vòng đeo tay mã vạch 128.
- **Kiosk tự phục vụ:** Quét CCCD/BHYT, lấy số thứ tự tự động nhiệt 80mm, hiển thị số đợi trực quan.
- **Dược phẩm & Tồn kho:** Quản lý hạn dùng FEFO, duyệt đơn thuốc, cảnh báo dị ứng chéo thuốc.
- **Cấp cứu & An toàn lâm sàng:** Triage ATS 5 cấp, kích hoạt Code Red, cảnh báo sớm NEWS2 & Đội RRT.
- **Viện phí & Hóa đơn:** Tính toán đồng chi trả BHYT, xuất mã VietQR Napas247, ví tạm ứng một chạm.
- **Spotlight Search toàn viện:** Tìm kiếm tức thì Bệnh nhân, Bác sĩ, Thuốc, Hóa đơn, Buồng bệnh, Xét nghiệm, Lịch hẹn qua phím tắt `Ctrl + K`.

---

## 🎯 4. HƯỚNG DẪN KIỂM CHỨNG TRỰC QUAN TRÊN GIAO DIỆN

1. **Khám phá Spotlight Search toàn viện:**
   - Nhấn phím tắt **`Ctrl + K`** hoặc nhấp vào thanh tìm kiếm ở Header.
   - Nhập từ khóa bất kỳ (có dấu hoặc không dấu): `Nguyen`, `BN-`, `thuoc`, `paracetamol`, `giuong`, `P999`, `xet nghiem`, `cap cuu`...
   - Hệ thống tự động phân loại theo các danh mục: Bệnh nhân, Bác sĩ, Thuốc & Dược, Viện phí, Buồng & Giường, Xét nghiệm, Lịch hẹn, Chức năng.
2. **Thử nghiệm các trường hợp biên (Negative Testing) trên Web UI:**
   - Nhập ngày sinh bệnh nhân ở tương lai $\rightarrow$ Hệ thống lập tức chặn và báo lỗi thân thiện.
   - Nhập chỉ số huyết áp tâm thu $\le$ tâm trương $\rightarrow$ Hệ thống phát hiện bất thường lâm sàng.
   - Cấp thẻ thăm bệnh thứ 3 cho cùng 1 bệnh nhân $\rightarrow$ Hệ thống tự động từ chối để kiểm soát nhiễm khuẩn JCI.
   - Truyền máu túi Rh(+) cho người Rh(-) $\rightarrow$ Hệ thống chặn khẩn cấp để phòng tai biến tiêu huyết.
   - Xuất thuốc gây nghiện không đủ chữ ký 3 bên $\rightarrow$ Hệ thống chặn theo Thông tư 20/2017/TT-BYT.

---
*Tài liệu phục vụ công tác đối chiếu nghiệp vụ y khoa và nghiệm thu kỹ thuật hệ thống.*
