<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<style>
  body { font-family: 'Segoe UI', Arial, sans-serif; font-size: 11pt; color: #1e293b; }
  table { border-collapse: collapse; width: 100%; margin-top: 10px; }
  th { background-color: #0284c7; color: #ffffff; font-weight: bold; border: 1px solid #94a3b8; padding: 10px 8px; text-align: center; }
  td { border: 1px solid #cbd5e1; padding: 8px 6px; vertical-align: middle; }
  .text-center { text-align: center; }
  .text-right { text-align: right; }
  .title-header { font-size: 16pt; font-weight: bold; color: #0284c7; text-align: center; padding: 10px; }
  .subtitle { font-size: 10.5pt; color: #64748b; text-align: center; font-style: italic; padding-bottom: 8px; }
  .badge-valid { background-color: #dcfce7; color: #15803d; font-weight: bold; text-align: center; }
  .badge-warning { background-color: #fef9c3; color: #a16207; font-weight: bold; text-align: center; }
  .badge-danger { background-color: #fee2e2; color: #b91c1c; font-weight: bold; text-align: center; }
  .badge-available { background-color: #e0f2fe; color: #0369a1; font-weight: bold; text-align: center; }
</style>
</head>
<body>
<table>
  <tr>
    <td colspan="15" class="title-header">HỆ THỐNG BỆNH VIỆN ĐA KHOA TÂM ANH</td>
  </tr>
  <tr>
    <td colspan="15" class="subtitle">BẢNG TỔNG HỢP DANH BẠ ĐỘI NGŨ BÁC SĨ & PHÁP LÝ CHỨNG CHỈ HÀNH NGHỀ (CCHN) &mdash; Xuất ngày {{ date('d/m/Y H:i') }}</td>
  </tr>
  <tr>
    <td colspan="15" style="height: 10px;"></td>
  </tr>
  <thead>
    <tr>
      <th style="width: 40px;">STT</th>
      <th style="width: 70px;">Mã BS</th>
      <th style="width: 220px;">Họ và Tên Bác Sĩ</th>
      <th style="width: 100px;">Học Vị</th>
      <th style="width: 220px;">Email Công Vụ</th>
      <th style="width: 120px;">Số Điện Thoại</th>
      <th style="width: 180px;">Chuyên Khoa</th>
      <th style="width: 120px;">Phòng Khám</th>
      <th style="width: 120px;">Vị Trí Tòa / Tầng</th>
      <th style="width: 160px;">Số CCHN</th>
      <th style="width: 140px;">Cơ Quan Cấp</th>
      <th style="width: 110px;">Ngày Cấp</th>
      <th style="width: 110px;">Ngày Hết Hạn</th>
      <th style="width: 130px;">Tình Trạng Pháp Lý</th>
      <th style="width: 140px;">Giá Khám (VNĐ)</th>
      <th style="width: 130px;">Trạng Thái Khám</th>
      <th style="width: 80px;">Đánh Giá</th>
    </tr>
  </thead>
  <tbody>
    @foreach($doctors as $index => $d)
      @php
        $isExpiring = $d->is_cchn_expiring_soon;
        $isExpired = $d->is_cchn_expired;
      @endphp
      <tr>
        <td class="text-center">{{ $index + 1 }}</td>
        <td class="text-center">BS-{{ str_pad($d->id, 4, '0', STR_PAD_LEFT) }}</td>
        <td style="font-weight: bold;">{{ $d->full_name_with_title }}</td>
        <td class="text-center">{{ $d->qualification ?: 'BS' }}</td>
        <td>{{ $d->user?->email }}</td>
        <td class="text-center">{{ $d->user?->phone ?: 'Chưa cập nhật' }}</td>
        <td>{{ $d->specialty?->name ?: 'Chưa phân khoa' }}</td>
        <td class="text-center">{{ $d->room_number }}</td>
        <td class="text-center">{{ $d->building ?: 'Tòa A' }} - {{ $d->floor ?: 'Tầng 2' }}</td>
        <td class="text-center" style="font-family: Consolas, monospace; font-weight: bold;">{{ $d->cchn_number ?: 'Chưa cập nhật' }}</td>
        <td class="text-center">{{ $d->cchn_issuer ?: 'Bộ Y Tế' }}</td>
        <td class="text-center">{{ $d->cchn_issued_date ? $d->cchn_issued_date->format('d/m/Y') : '—' }}</td>
        <td class="text-center">{{ $d->cchn_expiry_date ? $d->cchn_expiry_date->format('d/m/Y') : '—' }}</td>
        <td class="{{ $isExpired ? 'badge-danger' : ($isExpiring ? 'badge-warning' : 'badge-valid') }}">
          {{ $isExpired ? 'ĐÃ QUÁ HẠN' : ($isExpiring ? 'SẮP HẾT HẠN' : 'HỢP LỆ') }}
        </td>
        <td class="text-right" style="font-weight: bold;">{{ number_format($d->consultation_fee, 0, ',', '.') }}</td>
        <td class="badge-available">
          {{ $d->current_leave ? 'Nghỉ phép' : ($d->is_available ? 'Sẵn sàng' : 'Tạm dừng') }}
        </td>
        <td class="text-center" style="font-weight: bold; color: #d97706;">{{ number_format($d->rating_avg, 1) }} ★</td>
      </tr>
    @endforeach
  </tbody>
</table>
</body>
</html>
