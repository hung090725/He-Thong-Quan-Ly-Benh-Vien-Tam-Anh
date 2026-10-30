<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BiobankStemCellUnit extends Model
{
    use HasFactory;

    protected $table = 'biobank_stem_cell_units';

    protected $fillable = [
        'patient_id',
        'specimen_code',
        'stem_cell_source',
        'nitrogen_tank_id',
        'canister_rack_id',
        'box_slot_coordinate',
        'total_nucleated_cells_10e8',
        'cd34_positive_count_10e6',
        'cell_viability_pct',
        'sterility_clearance_passed',
        'storage_status',
    ];

    protected $casts = [
        'total_nucleated_cells_10e8' => 'float',
        'cd34_positive_count_10e6' => 'float',
        'cell_viability_pct' => 'float',
        'sterility_clearance_passed' => 'boolean',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(PatientProfile::class, 'patient_id');
    }

    /**
     * Thẩm định chất lượng mẫu tế bào gốc lưu trữ theo tiêu chuẩn FACT-JACIE:
     * - Tỷ lệ tế bào sống >= 85.0% (tốt nhất >= 90%)
     * - Kết quả cấy vi sinh kỵ khí và hiếu khí âm tính (Vô trùng tuyệt đối)
     */
    public static function assessViabilityAndSterility(float $viability, bool $sterility): array
    {
        $passed = ($viability >= 85.0 && $sterility);

        return [
            'qualified_for_transplant' => $passed,
            'status' => $passed ? 'stored_cryo' : 'quarantined',
            'assessment' => $passed 
                ? 'Đơn vị tế bào gốc đạt chuẩn chất lượng FACT-JACIE, sẵn sàng cho ghép tự thân/đồng loại.' 
                : 'Mẫu tế bào gốc không đạt tiêu chuẩn sống hoặc nghi ngờ tạp nhiễm. Chuyển diện cách ly theo dõi.',
        ];
    }
}
