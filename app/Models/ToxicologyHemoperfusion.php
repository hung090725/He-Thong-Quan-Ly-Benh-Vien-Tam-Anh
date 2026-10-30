<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ToxicologyHemoperfusion extends Model
{
    use HasFactory;

    protected $table = 'toxicology_hemoperfusions';

    protected $fillable = [
        'patient_id',
        'toxicologist_id',
        'toxin_or_drug_name',
        'estimated_ingestion_hours_ago',
        'ingested_dose_mg_or_ml',
        'serum_toxin_concentration_mcg_ml',
        'rumack_matthew_toxicity_risk',
        'specific_antidote_given',
        'extracorporeal_modality',
        'toxin_clearance_ml_min',
        'recommended_duration_hours',
        'hemodialysis_indicated',
        'hepatotoxicity_or_nephrotoxicity_prevented',
        'toxicology_clinical_assessment',
    ];

    protected $casts = [
        'estimated_ingestion_hours_ago' => 'float',
        'ingested_dose_mg_or_ml' => 'float',
        'serum_toxin_concentration_mcg_ml' => 'float',
        'toxin_clearance_ml_min' => 'float',
        'recommended_duration_hours' => 'float',
        'hemodialysis_indicated' => 'boolean',
        'hepatotoxicity_or_nephrotoxicity_prevented' => 'boolean',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(PatientProfile::class, 'patient_id');
    }

    public function toxicologist(): BelongsTo
    {
        return $this->belongsTo(Doctor::class, 'toxicologist_id');
    }

    /**
     * Đánh giá chỉ định lọc máu hấp phụ hoặc chạy thận nhân tạo khi ngộ độc cấp:
     * - Paracetamol: Nồng độ tại giờ thứ 4 > 150 mcg/mL (đường Rumack-Matthew) -> Cần NAC và lọc máu nếu nặng
     * - Paraquat / Barbiturate / Digoxin: Lọc máu hấp phụ quả resin sớm
     */
    public static function evaluateToxicologyIntervention(string $toxin, float $hoursPostIngestion, float $serumLevel): array
    {
        $toxinLower = strtolower($toxin);
        $needsExtracorporeal = false;
        $antidote = 'Theo dõi triệu chứng & Rửa dạ dày than hoạt';

        if (str_contains($toxinLower, 'paracetamol') || str_contains($toxinLower, 'acetaminophen')) {
            $threshold = 150.0 * pow(0.5, max(0.0, ($hoursPostIngestion - 4.0) / 4.0));
            if ($serumLevel >= $threshold) {
                $needsExtracorporeal = ($serumLevel >= 300.0);
                $antidote = 'N-acetylcysteine (NAC) phác đồ 21 giờ truyền tĩnh mạch';
            }
        } elseif (str_contains($toxinLower, 'paraquat')) {
            $needsExtracorporeal = true;
            $antidote = 'Methylprednisolone liều cao + Lọc máu hấp phụ Resin khẩn cấp';
        } elseif (str_contains($toxinLower, 'digoxin')) {
            $needsExtracorporeal = ($serumLevel > 4.0);
            $antidote = 'Kháng thể đơn dòng gắn Digoxin (DigiFab)';
        } else {
            $needsExtracorporeal = ($serumLevel >= 100.0);
        }

        return [
            'extracorporeal_indicated' => $needsExtracorporeal,
            'antidote_recommended' => $antidote,
            'summary' => $needsExtracorporeal 
                ? "CHỈ ĐỊNH LỌC MÁU HẤP PHỤ KHẨN CẤP: Nồng độ {$serumLevel} mcg/mL sau {$hoursPostIngestion}h vượt ngưỡng độc tính nghiêm trọng. Dùng ngay {$antidote}." 
                : "Nguy cơ độc tính kiểm soát được. Tiến hành phác đồ giải độc: {$antidote}.",
        ];
    }
}
