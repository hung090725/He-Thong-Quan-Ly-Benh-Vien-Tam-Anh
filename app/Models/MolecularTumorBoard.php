<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MolecularTumorBoard extends Model
{
    use HasFactory;

    protected $table = 'molecular_tumor_boards';

    protected $fillable = [
        'patient_id',
        'chairperson_doctor_id',
        'board_session_code',
        'primary_cancer_diagnosis',
        'actionable_mutations_json',
        'tumor_mutational_burden_tmb',
        'msi_status',
        'off_label_or_trial_therapies_proposed',
        'mtb_consensus_decision',
        'consensus_rational_summary',
    ];

    protected $casts = [
        'actionable_mutations_json' => 'array',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(PatientProfile::class, 'patient_id');
    }

    public function chairperson(): BelongsTo
    {
        return $this->belongsTo(Doctor::class, 'chairperson_doctor_id');
    }

    /**
     * Đồng thuận hội đồng ung bướu phân tử (MTB Consensus):
     * Khớp biến dị phức tạp với bằng chứng lâm sàng NCCN / ESMO Level 1-2
     */
    public static function synthesizeMtbRecommendation(array $mutations, string $msiStatus, ?string $tmb): array
    {
        $recommendations = [];

        if ($msiStatus === 'msi_high') {
            $recommendations[] = 'Khối u MSI-High / dMMR: Chỉ định liệu pháp miễn dịch ức chế điểm kiểm soát Pembrolizumab (Keytruda).';
        }

        $upperMutations = array_map('strtoupper', $mutations);
        if (in_array('BRAF_V600E', $upperMutations)) {
            $recommendations[] = 'Đột biến BRAF V600E: Phối hợp Dabrafenib + Trametinib hoặc Encorafenib + Cetuximab.';
        }
        if (in_array('NTRK_FUSION', $upperMutations)) {
            $recommendations[] = 'Hợp nhất gen NTRK: Đáp ứng ngoạn mục với thuốc ức chế TRK đặc hiệu Larotrectinib.';
        }

        return [
            'decision' => !empty($recommendations) ? 'unanimous_approved' : 'further_genomic_testing_needed',
            'summary' => !empty($recommendations) ? implode(' | ', $recommendations) : 'Chưa tìm thấy biến dị đích cấp 1 (Actionable Tier 1). Xem xét gửi mẫu giải trình tự toàn bộ hệ exome (WES).',
        ];
    }
}
