<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AntibiogramHaiSurveillance extends Model
{
    use HasFactory;

    protected $table = 'antibiogram_hai_surveillances';

    protected $fillable = [
        'department_name',
        'pathogen_name',
        'antibiotic_tested',
        'total_isolates_tested',
        'susceptibility_pct',
        'resistance_classification',
        'outbreak_cluster_alert',
        'hai_infection_site',
    ];

    protected $casts = [
        'total_isolates_tested' => 'integer',
        'susceptibility_pct' => 'float',
        'outbreak_cluster_alert' => 'boolean',
    ];

    /**
     * Đánh giá nguy cơ bùng phát chùm ca bệnh nhiễm khuẩn bệnh viện (Outbreak Cluster)
     * Dựa trên chuẩn CLSI M39: Mẫu phân lập >= 30, tỷ lệ kháng thuốc > 70% hoặc chủng toàn kháng XDR/PDR
     */
    public static function evaluateResistanceRisk(string $classification, float $susceptibility, int $isolates): array
    {
        $isOutbreak = in_array($classification, ['xdr_extensively_drug_resistant', 'pdr_pandrug_resistant']) || ($susceptibility < 20.0 && $isolates >= 10);

        return [
            'outbreak_alert' => $isOutbreak,
            'summary' => $isOutbreak 
                ? 'BÁO ĐỘNG ĐỎ KIỂM SOÁT NHIỄM KHUẨN: Phát hiện chủng siêu vi khuẩn đa kháng cao (XDR/PDR) lây lan nội viện. Cách ly buồng bệnh ngay lập tức!' 
                : 'Tỷ lệ nhạy cảm kháng sinh nằm trong xu hướng dịch tễ kiểm soát được.',
        ];
    }
}
