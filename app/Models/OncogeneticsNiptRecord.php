<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OncogeneticsNiptRecord extends Model
{
    use HasFactory;

    protected $table = 'oncogenetics_nipt_records';

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'test_category',
        'sample_type',
        'identified_variants',
        'fda_approved_targeted_therapies',
        'nipt_fetal_fraction_pct',
        'nipt_aneuploidy_risk',
        'genetic_counselor_summary',
    ];

    protected $casts = [
        'identified_variants' => 'array',
        'nipt_fetal_fraction_pct' => 'float',
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
     * Khớp đột biến gen Somatic/Germline với các thuốc đích FDA phê duyệt
     */
    public static function matchTargetedTherapies(array $variants): array
    {
        $therapies = [];
        $geneList = array_map('strtoupper', $variants);

        if (in_array('EGFR_L858R', $geneList) || in_array('EGFR_EXON19DEL', $geneList) || in_array('EGFR', $geneList)) {
            $therapies[] = 'Osimertinib (Tagrisso) 80mg - Đích thụ thể EGFR';
        }
        if (in_array('KRAS_G12C', $geneList)) {
            $therapies[] = 'Sotorasib (Lumakras) 960mg - Ức chế KRAS G12C';
        }
        if (in_array('HER2_AMPLIFIED', $geneList) || in_array('ERBB2', $geneList)) {
            $therapies[] = 'Trastuzumab Deruxtecan (Enhertu) - Kháng thể liên hợp ADC';
        }
        if (in_array('BRCA1', $geneList) || in_array('BRCA2', $geneList)) {
            $therapies[] = 'Olaparib (Lynparza) - Ức chế PARP';
        }

        return $therapies;
    }

    /**
     * Đánh giá chất lượng mẫu NIPT (Fetal fraction >= 4.0%)
     */
    public static function validateNiptFetalFraction(?float $fraction): bool
    {
        return $fraction !== null && $fraction >= 4.0;
    }
}
