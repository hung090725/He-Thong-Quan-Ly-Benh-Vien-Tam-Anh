<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medication extends Model
{
    use HasFactory;

    protected $fillable = [
        'medication_code',
        'name',
        'active_ingredient',
        'dosage_form',
        'unit',
        'stock_quantity',
        'min_stock_alert',
        'unit_price',
        'expiry_date',
        'batch_number',
        'dosage_default',
        'frequency_default',
        'duration_default',
        'instructions_default',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'stock_quantity' => 'integer',
            'min_stock_alert' => 'integer',
            'unit_price' => 'float',
            'expiry_date' => 'date',
            'duration_default' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function isLowStock(): bool
    {
        return $this->stock_quantity > 0 && $this->stock_quantity <= $this->min_stock_alert;
    }

    public function isOutOfStock(): bool
    {
        return $this->stock_quantity <= 0;
    }

    public function isExpiringSoon(): bool
    {
        if (!$this->expiry_date) {
            return false;
        }

        return $this->expiry_date->isFuture() && $this->expiry_date->diffInDays(Carbon::today()) <= 90;
    }

    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->unit_price, 0, ',', '.') . ' đ';
    }

    public function getStockBadgeAttribute(): string
    {
        if ($this->isOutOfStock()) {
            return '<span class="badge bg-danger text-white border border-danger">Hết hàng (0 ' . $this->unit . ')</span>';
        }

        if ($this->isLowStock()) {
            return '<span class="badge bg-warning text-dark border border-warning">Sắp hết (' . $this->stock_quantity . ' ' . $this->unit . ')</span>';
        }

        return '<span class="badge bg-success-subtle text-success border border-success-subtle">Còn ' . $this->stock_quantity . ' ' . $this->unit . '</span>';
    }

    public static function generateMedicationCode(): string
    {
        $prefix = 'MED-';
        $latest = self::where('medication_code', 'LIKE', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->first();

        if ($latest) {
            $lastNumber = (int) preg_replace('/[^0-9]/', '', substr($latest->medication_code, 4));
            $nextNumber = str_pad((string)($lastNumber + 1), 4, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '0001';
        }

        return "{$prefix}{$nextNumber}";
    }
}
