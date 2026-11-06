<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CarTCellTherapy extends Model
{
    use HasFactory;

    protected $table = 'car_t_cell_therapies';

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'cart_product_name',
        'target_antigen',
        'infusion_date',
        'peak_temperature_c',
        'systolic_bp',
        'spo2_pct',
        'astct_crs_grade',
        'icans_neurotoxicity_grade',
        'tocilizumab_indicated',
        'tocilizumab_doses_given',
        'dexamethasone_given',
        'therapy_outcome',
    ];

    protected $casts = [
        'infusion_date' => 'datetime',
        'peak_temperature_c' => 'float',
        'systolic_bp' => 'float',
        'spo2_pct' => 'float',
        'tocilizumab_indicated' => 'boolean',
        'tocilizumab_doses_given' => 'integer',
        'dexamethasone_given' => 'boolean',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(PatientProfile::class, 'patient_id');
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class, 'doctor_id');
    }

    /**
     * Phân độ hội chứng giải phóng bão Cytokine (CRS) theo chuẩn đồng thuận ASTCT 2019:
     * - Grade 1: Sốt >= 38.0°C đơn thuần, không tụt HA, không thiếu oxy
     * - Grade 2: Sốt + Tụt huyết áp đáp ứng bù dịch hoặc thở oxy cannula liều thấp
     * - Grade 3: Tụt huyết áp cần thuốc vận mạch (1 loại) hoặc thở oxy mặt nạ lưu lượng cao
     * - Grade 4: Cần nhiều thuốc vận mạch liều cao hoặc thở máy xâm lấn
     */
    public static function gradeCrs(float $temp, float $sbp, float $spo2, bool $needsVasopressor = false, bool $needsVentilator = false): array
    {
        if ($temp < 38.0) {
            return [
                'grade' => 'grade_0_none',
                'tocilizumab' => false,
                'action' => 'Không có bão Cytokine CRS. Tiếp tục theo dõi thân nhiệt và sinh hiệu định kỳ 4h/lần.',
            ];
        }

        if ($needsVentilator) {
            return [
                'grade' => 'grade_4_mechanical_ventilation',
                'tocilizumab' => true,
                'action' => 'CRS ĐỘ 4 CỰC KỲ NGUY HIỂM: Truyền ngay Tocilizumab 8mg/kg IV kết hợp Methylprednisolone 1000mg/ngày. Chuyển ICU thở máy bảo vệ phổi.',
            ];
        }

        if ($needsVasopressor || $spo2 < 90.0) {
            return [
                'grade' => 'grade_3_vasopressors_high_o2',
                'tocilizumab' => true,
                'action' => 'CRS ĐỘ 3 NẶNG: Chỉ định dùng Tocilizumab liều 8mg/kg truyền TM trong 1h, lặp lại sau 8h nếu chưa kiểm soát được huyết động.',
            ];
        }

        if ($sbp < 90.0 || $spo2 < 93.0) {
            return [
                'grade' => 'grade_2_hypotension_fluids',
                'tocilizumab' => true,
                'action' => 'CRS ĐỘ 2: Bù dịch tinh thể 500-1000mL. Xem xét chỉ định Tocilizumab sớm nếu không nâng được huyết áp.',
            ];
        }

        return [
            'grade' => 'grade_1_fever_only',
            'tocilizumab' => false,
            'action' => 'CRS ĐỘ 1: Sốt nhẹ do tăng sinh tế bào T. Dùng Paracetamol hạ sốt, cấy máu loại trừ nhiễm trùng cơ hội, theo dõi sát sinh hiệu.',
        ];
    }
}
