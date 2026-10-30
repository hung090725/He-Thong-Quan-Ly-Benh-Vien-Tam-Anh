<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IvYsiteIncompatibility extends Model
{
    use HasFactory;

    protected $table = 'iv_ysite_incompatibilities';

    protected $fillable = [
        'primary_drug_name',
        'secondary_drug_name',
        'carrier_solution',
        'incompatibility_type',
        'clinical_hazard_summary',
        'recommended_action',
        'evidence_source',
    ];

    /**
     * Tra cứu tương kỵ Y-site hai thuốc theo cơ sở dữ liệu Trissel's
     */
    public static function checkCompatibility(string $drugA, string $drugB): array
    {
        $a = mb_strtolower(trim($drugA), 'UTF-8');
        $b = mb_strtolower(trim($drugB), 'UTF-8');

        $incompatPairs = [
            ['furosemide', 'midazolam', 'precipitate_formation', 'Tạo tủa đục dạng bông ngay lập tức ở chạc ba Y-site', 'Cấm truyền chung. Tách đường truyền hoặc tráng rửa NaCl 0.9% trước khi tiêm.'],
            ['ciprofloxacin', 'heparin', 'precipitate_formation', 'Tạo kết tủa do tương tác ion acid-base', 'Truyền cách nhau ít nhất 1 giờ hoặc dùng 2 catheter riêng biệt.'],
            ['ceftriaxone', 'calcium', 'precipitate_formation', 'Tủa muối Ceftriaxone-Canxi nguy cơ tắc mạch phổi tử vong', 'Chống chỉ định tuyệt đối dùng chung kể cả qua dây truyền khác nhau trong vòng 48h.'],
            ['noradrenaline', 'sodium bicarbonate', 'chemical_degradation', 'Môi trường kiềm oxy hóa làm mất hoạt tính co mạch', 'Truyền đường truyền trung tâm chuyên biệt.'],
        ];

        foreach ($incompatPairs as $p) {
            if ((str_contains($a, $p[0]) && str_contains($b, $p[1])) || (str_contains($a, $p[1]) && str_contains($b, $p[0]))) {
                return [
                    'type' => $p[2],
                    'hazard' => $p[3],
                    'recommendation' => $p[4],
                ];
            }
        }

        return [
            'type' => 'compatible',
            'hazard' => 'Không phát hiện tương kỵ vật lý/hóa học ghi nhận trong 4 giờ tiếp xúc',
            'recommendation' => 'Có thể truyền đồng thời qua chạc chữ Y, tiếp tục theo dõi biến đổi màu sắc dịch truyền.',
        ];
    }
}
