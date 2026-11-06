<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClinicalTrialManagement extends Model
{
    use HasFactory;

    protected $table = 'clinical_trial_managements';

    protected $fillable = [
        'protocol_code',
        'patient_id',
        'principal_investigator_id',
        'trial_title',
        'study_phase',
        'randomization_arm',
        'electronic_informed_consent_signed',
        'study_visit_number',
        'protocol_adherence_rate_pct',
        'serious_adverse_event_sae_occurred',
        'sae_reported_to_irb_within_24h_at',
        'subject_status',
        'gcp_compliance_audit_notes',
    ];

    protected $casts = [
        'study_visit_number' => 'integer',
        'protocol_adherence_rate_pct' => 'float',
        'electronic_informed_consent_signed' => 'boolean',
        'serious_adverse_event_sae_occurred' => 'boolean',
        'sae_reported_to_irb_within_24h_at' => 'datetime',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(PatientProfile::class, 'patient_id');
    }

    public function principalInvestigator(): BelongsTo
    {
        return $this->belongsTo(Doctor::class, 'principal_investigator_id');
    }

    /**
     * Đánh giá tuân thủ thử nghiệm lâm sàng chuẩn GCP và giám sát an toàn biến cố bất lợi nghiêm trọng (SAE):
     * - Bắt buộc ký e-Consent trước khi tiến hành can thiệp
     * - Nếu có SAE phải báo cáo khẩn trong vòng 24 giờ cho Hội đồng Đạo đức (IRB)
     */
    public static function auditTrialCompliance(bool $consentSigned, float $adherencePct, bool $saeOccurred, ?string $saeReportedAt): array
    {
        $compliant = ($consentSigned && $adherencePct >= 80.0);
        $saeReportTimely = true;

        if ($saeOccurred && !$saeReportedAt) {
            $saeReportTimely = false;
        }

        return [
            'gcp_compliant' => $compliant,
            'sae_reported_on_time' => $saeReportTimely,
            'summary' => ($saeOccurred && !$saeReportTimely) 
                ? 'VI PHẠM QUY CHUẨN GCP: Biến cố bất lợi nghiêm trọng (SAE) chưa được báo cáo cho Hội đồng Đạo đức trong vòng 24 giờ!' 
                : "Hồ sơ đối tượng nghiên cứu tuân thủ tốt chuẩn GCP: Tỷ lệ tuân thủ {$adherencePct}%, e-Consent hợp lệ." . ($saeOccurred ? ' Đã báo cáo SAE khẩn cấp cho IRB.' : ''),
        ];
    }
}
