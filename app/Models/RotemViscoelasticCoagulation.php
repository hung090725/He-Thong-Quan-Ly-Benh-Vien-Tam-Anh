<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RotemViscoelasticCoagulation extends Model
{
    use HasFactory;

    protected $table = 'rotem_viscoelastic_coagulations';

    protected $fillable = [
        'patient_id',
        'anesthesiologist_id',
        'clinical_scenario',
        'extem_ct_seconds',
        'extem_cft_seconds',
        'extem_alpha_angle_degrees',
        'extem_mcf_mm',
        'extem_ml_pct',
        'fibtem_mcf_mm',
        'aptem_mcf_mm',
        'hyperfibrinolysis_detected',
        'targeted_blood_product_recommendation',
        'tranexamic_acid_indicated',
    ];

    protected $casts = [
        'extem_ct_seconds' => 'integer',
        'extem_cft_seconds' => 'integer',
        'extem_alpha_angle_degrees' => 'float',
        'extem_mcf_mm' => 'integer',
        'extem_ml_pct' => 'float',
        'fibtem_mcf_mm' => 'integer',
        'aptem_mcf_mm' => 'integer',
        'hyperfibrinolysis_detected' => 'boolean',
        'tranexamic_acid_indicated' => 'boolean',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(PatientProfile::class, 'patient_id');
    }

    public function anesthesiologist(): BelongsTo
    {
        return $this->belongsTo(Doctor::class, 'anesthesiologist_id');
    }

    /**
     * Thuật toán hướng dẫn truyền máu và chế phẩm theo biểu đồ ROTEM:
     * - EXTEM CT > 80s: Thiếu yếu tố đông máu -> Truyền Huyết tương đông lạnh (FFP)
     * - FIBTEM MCF < 10mm: Thiếu Fibrinogen -> Truyền Kết tủa lạnh (Cryoprecipitate) / Fibrinogen đậm đặc
     * - EXTEM MCF < 45mm nhưng FIBTEM >= 10mm: Thiếu tiểu cầu -> Truyền Khối tiểu cầu
     * - EXTEM ML > 15%: Tăng tiêu sợi huyết -> Dùng Acid Tranexamic (TXA)
     */
    public static function analyzeRotemParameters(int $ct, int $mcf, int $fibtemMcf, float $ml): array
    {
        $recommendations = [];
        $txaNeeded = ($ml > 15.0);

        if ($txaNeeded) {
            $recommendations[] = 'Acid Tranexamic 1g tiêm tĩnh mạch (Tăng tiêu sợi huyết ML > 15%)';
        }

        if ($fibtemMcf < 10) {
            $recommendations[] = 'Kết tủa lạnh Cryoprecipitate 10 đơn vị hoặc Fibrinogen 2g (FIBTEM MCF < 10mm)';
        }

        if ($ct > 80) {
            $recommendations[] = 'Huyết tương tươi đông lạnh FFP 15 mL/kg hoặc PCC (EXTEM CT kéo dài > 80s)';
        }

        if ($mcf < 45 && $fibtemMcf >= 10) {
            $recommendations[] = 'Khối tiểu cầu gạn tách 1 kít (EXTEM MCF giảm do giảm số lượng/chức năng tiểu cầu)';
        }

        if (empty($recommendations)) {
            $recommendations[] = 'Đông máu ổn định trong giới hạn bình thường. Không cần bù chế phẩm máu.';
        }

        return [
            'hyperfibrinolysis' => $txaNeeded,
            'txa_indicated' => $txaNeeded,
            'advice_list' => $recommendations,
            'summary' => implode(' | ', $recommendations),
        ];
    }
}
