<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrphanAntidoteRegistry extends Model
{
    use HasFactory;

    protected $table = 'orphan_antidote_registries';

    protected $fillable = [
        'antidote_name',
        'indication_poison',
        'stock_vials_available',
        'minimum_safety_threshold',
        'storage_location_cabinet',
        'earliest_expiry_date',
        'immediate_procurement_alert',
        'first_dose_administration_protocol',
    ];

    protected $casts = [
        'stock_vials_available' => 'integer',
        'minimum_safety_threshold' => 'integer',
        'earliest_expiry_date' => 'date',
        'immediate_procurement_alert' => 'boolean',
    ];

    /**
     * Kiểm tra trạng thái tồn kho thuốc giải độc hiếm và phác đồ liều đầu cấp cứu
     */
    public static function checkAntidoteStockStatus(int $available, int $threshold): bool
    {
        return $available <= $threshold;
    }

    /**
     * Phác đồ giải độc chuẩn theo hướng dẫn Trung tâm Chống độc Quốc gia
     */
    public static function getFirstDoseProtocol(string $antidoteName): string
    {
        $protocols = [
            'Intralipid 20%' => 'Bolus 1.5 mL/kg trong 1 phút, sau đó truyền liên tục 0.25 mL/kg/phút. Chỉ định ngộ độc thuốc tê toàn thân LAST.',
            'BAT (Botulism Antitoxin Heptavalent)' => 'Tiêm truyền tĩnh mạch chậm 1 lọ pha loãng trong NaCl 0.9% theo tỷ lệ 1:10 trong 30-60 phút. Chỉ định ngộ độc Botulinum.',
            'N-acetylcysteine (NAC)' => 'Liều nạp 150 mg/kg truyền TM trong 60 phút, tiếp theo 50 mg/kg trong 4 giờ. Chỉ định ngộ độc cấp Paracetamol.',
            'Fomepizole' => 'Liều nạp 15 mg/kg truyền TM trong 30 phút. Chỉ định ngộ độc Methanol hoặc Ethylene glycol.',
        ];

        return $protocols[$antidoteName] ?? 'Theo dõi sát sinh hiệu, điện giải đồ và hội chẩn khẩn cấp chuyên gia Trung tâm Chống độc.';
    }
}
