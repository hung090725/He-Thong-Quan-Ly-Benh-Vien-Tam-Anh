<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CoronaryPhysiologyImaging extends Model
{
    use HasFactory;

    protected $table = 'coronary_physiology_imagings';

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'target_artery',
        'ffr_ratio',
        'ifr_ratio',
        'imaging_modality',
        'minimal_lumen_area_mm2',
        'plaque_burden_pct',
        'stent_malapposition_detected',
        'stent_underexpansion_detected',
        'hemodynamic_significance',
        'operator_notes',
    ];

    protected $casts = [
        'ffr_ratio' => 'float',
        'ifr_ratio' => 'float',
        'minimal_lumen_area_mm2' => 'float',
        'plaque_burden_pct' => 'float',
        'stent_malapposition_detected' => 'boolean',
        'stent_underexpansion_detected' => 'boolean',
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
     * Đánh giá thiếu máu cơ tim và chỉ định can thiệp tái tưới máu (PCI / CABG)
     * Dựa trên FFR (<= 0.80), iFR (<= 0.89) hoặc IVUS MLA (Thân chung LM <= 6.0mm2, LAD/RCA <= 3.5mm2)
     */
    public static function assessIschemiaSignificance(
        string $artery,
        ?float $ffr,
        ?float $ifr,
        ?float $mla
    ): array {
        $isFfrIschemic = ($ffr !== null && $ffr <= 0.80);
        $isIfrIschemic = ($ifr !== null && $ifr <= 0.89);
        
        $isMlaCritical = false;
        if ($mla !== null) {
            if (strtoupper($artery) === 'LM' && $mla <= 6.0) {
                $isMlaCritical = true;
            } elseif ($mla <= 3.5) {
                $isMlaCritical = true;
            }
        }

        if ($isFfrIschemic || $isIfrIschemic || $isMlaCritical) {
            return [
                'hemodynamic' => 'ischemic_revascularization_indicated',
                'decision' => 'Có ý nghĩa huyết động gây thiếu máu cơ tim. Chỉ định tái thông mạch vành can thiệp (PCI đặt Stent tối ưu hóa bằng hình ảnh học IVUS/OCT).',
            ];
        }

        return [
            'hemodynamic' => 'non_ischemic_medical_therapy',
            'decision' => 'Không có ý nghĩa huyết động gây thiếu máu cơ tim. Trì hoãn can thiệp, tiếp tục điều trị nội khoa tối ưu (GDMT).',
        ];
    }
}
