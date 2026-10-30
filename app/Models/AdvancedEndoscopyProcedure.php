<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdvancedEndoscopyProcedure extends Model
{
    use HasFactory;

    protected $table = 'advanced_endoscopy_procedures';

    protected $fillable = [
        'patient_id',
        'endoscopist_id',
        'procedure_category',
        'lesion_organ_location',
        'lesion_size_mm',
        'en_bloc_resection_achieved',
        'r0_clear_margins_confirmed',
        'asge_difficulty_grade',
        'prophylactic_pancreatic_stent_placed',
        'rectal_indomethacin_administered',
        'perforation_or_bleeding_occurred',
    ];

    protected $casts = [
        'lesion_size_mm' => 'float',
        'en_bloc_resection_achieved' => 'boolean',
        'r0_clear_margins_confirmed' => 'boolean',
        'prophylactic_pancreatic_stent_placed' => 'boolean',
        'rectal_indomethacin_administered' => 'boolean',
        'perforation_or_bleeding_occurred' => 'boolean',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(PatientProfile::class, 'patient_id');
    }

    public function endoscopist(): BelongsTo
    {
        return $this->belongsTo(Doctor::class, 'endoscopist_id');
    }

    /**
     * Đánh giá chất lượng can thiệp nội soi nâng cao:
     * - ESD/POEM: Cắt trọn khối (En-bloc) và diện cắt sạch tế bào ung thư R0
     * - ERCP: Dự phòng viêm tụy cấp sau can thiệp (PEP) bằng Indomethacin đặt hậu môn và Stent ống tụy
     */
    public static function assessEndoscopicQuality(string $category, bool $enBloc, bool $r0, bool $rectalIndo, bool $stentPlaced): array
    {
        if (str_contains($category, 'esd') || str_contains($category, 'poem')) {
            $isCurative = ($enBloc && $r0);
            return [
                'success' => $isCurative,
                'summary' => $isCurative 
                    ? 'Can thiệp cắt tách dưới niêm mạc triệt căn hoàn hảo (En-bloc R0 Resection).' 
                    : 'Cắt không trọn khối hoặc bờ diện cắt còn tế bào dị sản. Cần theo dõi sinh thiết lại sau 3 tháng.',
            ];
        }

        $pepProtected = ($rectalIndo || $stentPlaced);
        return [
            'success' => $pepProtected,
            'summary' => $pepProtected 
                ? 'Đã thực hiện đầy đủ phác đồ dự phòng viêm tụy cấp sau ERCP (PEP) theo chuẩn ESGE/ASGE.' 
                : 'CẢNH BÁO: Chưa dùng thuốc chống viêm tụy dự phòng cho ca can thiệp đường mật độ khó cao!',
        ];
    }
}
