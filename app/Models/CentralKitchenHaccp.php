<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CentralKitchenHaccp extends Model
{
    use HasFactory;

    protected $fillable = [
        'menu_batch_code',
        'diet_category',
        'cooking_core_temp_celsius',
        'rethermalization_hot_temp',
        'rethermalization_cold_temp',
        'food_sample_refrigerated_24h',
        'haccp_audit_passed',
        'executive_chef_dietitian',
    ];

    protected $casts = [
        'cooking_core_temp_celsius' => 'float',
        'rethermalization_hot_temp' => 'float',
        'rethermalization_cold_temp' => 'float',
        'food_sample_refrigerated_24h' => 'boolean',
        'haccp_audit_passed' => 'boolean',
    ];

    public function auditHaccpFoodSafety(): bool
    {
        // Chuẩn HACCP: nấu chín tâm >= 75°C, giữ nóng >= 65°C, giữ lạnh <= 4°C, lưu mẫu 24h
        if ($this->cooking_core_temp_celsius >= 75.0 && 
            $this->rethermalization_hot_temp >= 65.0 && 
            $this->rethermalization_cold_temp <= 4.0 && 
            $this->food_sample_refrigerated_24h) {
            $this->update(['haccp_audit_passed' => true]);
            return true;
        }

        $this->update(['haccp_audit_passed' => false]);
        return false;
    }
}
