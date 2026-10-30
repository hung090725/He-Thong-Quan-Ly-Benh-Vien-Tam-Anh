<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndoorAirQualityHvac extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_sensor_code',
        'room_type',
        'pm2_5_ug_m3',
        'co2_ppm',
        'tvoc_ppb',
        'temperature_celsius',
        'relative_humidity_pct',
        'hvac_damper_fresh_air_pct',
        'iaq_index_rating',
    ];

    protected $casts = [
        'pm2_5_ug_m3' => 'float',
        'co2_ppm' => 'integer',
        'tvoc_ppb' => 'integer',
        'temperature_celsius' => 'float',
        'relative_humidity_pct' => 'float',
        'hvac_damper_fresh_air_pct' => 'integer',
    ];

    public function computeIaqRating(): string
    {
        // Chuẩn ASHRAE 62.1: PM2.5 < 15, CO2 < 800, TVOC < 220
        if ($this->pm2_5_ug_m3 <= 15.0 && $this->co2_ppm <= 800 && $this->tvoc_ppb <= 220) {
            $rating = 'excellent_green';
        } elseif ($this->pm2_5_ug_m3 <= 35.0 && $this->co2_ppm <= 1000) {
            $rating = 'moderate_yellow';
            $this->hvac_damper_fresh_air_pct = min(100, $this->hvac_damper_fresh_air_pct + 20);
        } else {
            $rating = 'poor_unhealthy_red';
            $this->hvac_damper_fresh_air_pct = 100; // Mở tối đa 100% gió tươi
        }

        $this->update([
            'iaq_index_rating' => $rating,
            'hvac_damper_fresh_air_pct' => $this->hvac_damper_fresh_air_pct,
        ]);

        return $rating;
    }
}
