<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MassCasualtyTriage extends Model
{
    use HasFactory;

    protected $table = 'mass_casualty_triages';

    protected $fillable = [
        'incident_name',
        'disaster_code',
        'victim_tag_number',
        'can_walk',
        'respiratory_rate',
        'radial_pulse_palpable',
        'mental_status',
        'start_triage_color',
        'assigned_zone',
        'primary_injuries',
        'triaged_by',
    ];

    protected $casts = [
        'can_walk' => 'boolean',
        'respiratory_rate' => 'integer',
        'radial_pulse_palpable' => 'boolean',
    ];

    /**
     * Thuật toán START (Simple Triage and Rapid Treatment) chuẩn quốc tế thảm họa
     * Phân loại màu: Green (Nhẹ), Red (Tối khẩn), Yellow (Trì hoãn), Black (Tử vong/Không hồi phục)
     */
    public static function calculateStartTriage(bool $canWalk, ?int $rr, bool $radialPulse, string $mentalStatus): array
    {
        // 1. Tự đi lại được -> Xanh
        if ($canWalk) {
            return [
                'color' => 'green_minor',
                'zone' => 'Khu Xanh (Nhẹ - Chờ xử lý sau)',
                'urgency' => 'Không đe dọa tính mạng ngay lập tức',
            ];
        }

        // 2. Hô hấp: Không thở -> Đen (hoặc nếu mở đường thở thở lại thì Đỏ, ở đây xét rr = 0)
        if ($rr === null || $rr === 0) {
            return [
                'color' => 'black_deceased',
                'zone' => 'Khu Đen (Tử vong / Tiên lượng không cứu sống)',
                'urgency' => 'Tử vong lâm sàng hoặc chấn thương trí mạng',
            ];
        }

        // Thở > 30 hoặc < 10 -> Đỏ khẩn cấp
        if ($rr > 30 || $rr < 10) {
            return [
                'color' => 'red_immediate',
                'zone' => 'Khu Đỏ (Hồi sức cấp cứu tối khẩn cấp)',
                'urgency' => 'Suy hô hấp cấp tính - Ưu tiên số 1',
            ];
        }

        // 3. Tuần hoàn: Không bắt được mạch quay -> Đỏ (Sốc)
        if (!$radialPulse) {
            return [
                'color' => 'red_immediate',
                'zone' => 'Khu Đỏ (Hồi sức cấp cứu tối khẩn cấp)',
                'urgency' => 'Sốc tuần hoàn mất bù - Cần truyền máu / dịch ngay',
            ];
        }

        // 4. Tri giác: Không thực hiện được y lệnh đơn giản -> Đỏ
        if ($mentalStatus !== 'follows_commands') {
            return [
                'color' => 'red_immediate',
                'zone' => 'Khu Đỏ (Hồi sức cấp cứu tối khẩn cấp)',
                'urgency' => 'Tổn thương thần kinh trung ương cấp',
            ];
        }

        // Các trường hợp còn lại -> Vàng
        return [
            'color' => 'yellow_delayed',
            'zone' => 'Khu Vàng (Cấp cứu có trì hoãn)',
            'urgency' => 'Nặng nhưng tuần hoàn, hô hấp và tri giác tạm ổn định',
        ];
    }
}
