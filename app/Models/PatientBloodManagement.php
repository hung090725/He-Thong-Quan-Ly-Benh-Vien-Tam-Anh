<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientBloodManagement extends Model
{
    use HasFactory;

    protected $table = 'patient_blood_managements';

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'preop_hemoglobin_g_dl',
        'preop_ferritin_ng_ml',
        'preop_anemia_detected',
        'tranexamic_acid_used',
        'cell_saver_used',
        'autologous_blood_salvaged_ml',
        'postop_hemoglobin_g_dl',
        'allogeneic_transfusion_units',
        'pbm_protocol_bundle_compliant',
        'clinical_outcome',
    ];

    protected $casts = [
        'preop_hemoglobin_g_dl' => 'float',
        'preop_ferritin_ng_ml' => 'float',
        'preop_anemia_detected' => 'boolean',
        'tranexamic_acid_used' => 'boolean',
        'cell_saver_used' => 'boolean',
        'autologous_blood_salvaged_ml' => 'float',
        'postop_hemoglobin_g_dl' => 'float',
        'allogeneic_transfusion_units' => 'integer',
        'pbm_protocol_bundle_compliant' => 'boolean',
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
     * Sàng lọc thiếu máu tiền phẫu và dự phòng theo 3 trụ cột PBM WHO
     */
    public static function detectPreopAnemia(float $hb, ?float $ferritin): array
    {
        $isAnemic = ($hb < 12.0); // Chuẩn WHO: Nữ < 12g/dL, Nam < 13g/dL
        $ironDeficiency = ($ferritin !== null && $ferritin < 30.0);

        $type = 'Không thiếu máu';
        if ($isAnemic && $ironDeficiency) {
            $type = 'Thiếu máu thiếu sắt tiền phẫu';
        } elseif ($isAnemic) {
            $type = 'Thiếu máu bệnh mạn tính / chưa rõ nguyên nhân';
        }

        return [
            'is_anemic' => $isAnemic,
            'iron_deficiency' => $ironDeficiency,
            'anemia_type' => $type,
            'action' => $ironDeficiency ? 'Bù sắt tĩnh mạch (Ferric carboxymaltose 1000mg) trước mổ 2-4 tuần' : 'Tiếp tục theo dõi thường quy',
        ];
    }
}
