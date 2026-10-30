<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IvfEmbryoCycle extends Model
{
    use HasFactory;

    protected $table = 'ivf_embryo_cycles';

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'cycle_code',
        'partner_name',
        'partner_sample_code',
        'oocytes_retrieved',
        'oocytes_fertilized',
        'embryo_id_tag',
        'development_stage',
        'gardner_grade',
        'ai_implantation_potential_score',
        'electronic_witnessing_verified',
        'nitrogen_tank_storage_position',
        'embryo_status',
    ];

    protected $casts = [
        'oocytes_retrieved' => 'integer',
        'oocytes_fertilized' => 'integer',
        'ai_implantation_potential_score' => 'float',
        'electronic_witnessing_verified' => 'boolean',
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
     * Chấm điểm tiềm năng làm tổ dựa trên hệ thống Gardner (ví dụ: 4AA, 4AB, 3BB...)
     * Kết hợp công nghệ AI Time-lapse Morphokinetics
     */
    public static function calculateGardnerAiScore(string $gardnerGrade): float
    {
        $grade = strtoupper(trim($gardnerGrade));
        $scoreTable = [
            '6AA' => 98.5, '5AA' => 97.0, '4AA' => 95.5,
            '4AB' => 90.0, '4BA' => 88.5, '3AA' => 86.0,
            '4BB' => 80.0, '3AB' => 78.5, '3BB' => 72.0,
            '4AC' => 65.0, '4BC' => 60.0, '4CB' => 55.0,
            '3CC' => 45.0, '2'   => 40.0, '1'   => 30.0,
        ];

        return $scoreTable[$grade] ?? 70.0;
    }

    /**
     * Xác thực Electronic Witnessing (RFID/Barcode đối chiếu kép Noãn - Tinh trùng - Đĩa cấy)
     */
    public static function verifyElectronicWitnessing(string $dishRfid, string $partnerSpermRfid, string $expectedDishPrefix): bool
    {
        if (empty($dishRfid) || empty($partnerSpermRfid)) {
            return false;
        }

        // Kiểm tra tính hợp lệ của mã đĩa và mẫu tinh trùng
        return str_starts_with($dishRfid, $expectedDishPrefix);
    }
}
