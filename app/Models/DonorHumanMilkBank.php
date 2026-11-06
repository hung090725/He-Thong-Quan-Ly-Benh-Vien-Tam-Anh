<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DonorHumanMilkBank extends Model
{
    use HasFactory;

    protected $table = 'donor_human_milk_banks';

    protected $fillable = [
        'milk_batch_code',
        'anonymous_donor_code',
        'dhmb_technician_id',
        'batch_volume_ml',
        'pasteurization_holder_completed_at',
        'pre_pasteurization_culture_passed',
        'post_pasteurization_culture_sterile',
        'energy_kcal_per_100ml',
        'protein_grams_per_100ml',
        'fat_grams_per_100ml',
        'expiration_frozen_date',
        'assigned_recipient_infant_id',
        'milk_batch_status',
    ];

    protected $casts = [
        'batch_volume_ml' => 'float',
        'energy_kcal_per_100ml' => 'float',
        'protein_grams_per_100ml' => 'float',
        'fat_grams_per_100ml' => 'float',
        'pasteurization_holder_completed_at' => 'datetime',
        'expiration_frozen_date' => 'date',
        'pre_pasteurization_culture_passed' => 'boolean',
        'post_pasteurization_culture_sterile' => 'boolean',
    ];

    public function technician(): BelongsTo
    {
        return $this->belongsTo(Doctor::class, 'dhmb_technician_id');
    }

    public function recipientInfant(): BelongsTo
    {
        return $this->belongsTo(PatientProfile::class, 'assigned_recipient_infant_id');
    }

    /**
     * Kiểm định an toàn vi sinh sữa mẹ thanh trùng theo phương pháp Holder Pasteurization:
     * - Cấy vi khuẩn sau thanh trùng phải 100% vô khuẩn (Sterile)
     */
    public static function assessMilkBatchSafety(bool $preCulture, bool $postCulture, float $energyKcal): array
    {
        $approved = ($preCulture && $postCulture);

        return [
            'approved_for_nicu' => $approved,
            'energy_density' => $energyKcal >= 65.0 ? 'optimal' : 'standard',
            'summary' => $approved 
                ? "Mẻ sữa mẹ hiến tặng đạt chuẩn an toàn sinh học vô khuẩn tuyệt đối! Năng lượng: {$energyKcal} kcal/100mL. Sẵn sàng cấp phát cho trẻ sinh non NICU." 
                : 'MẺ SỮA KHÔNG ĐẠT TIÊU CHUẨN VI SINH: Kết quả cấy sau thanh trùng dương tính. Hủy toàn bộ mẻ sữa theo quy trình an toàn sinh học.',
        ];
    }
}
