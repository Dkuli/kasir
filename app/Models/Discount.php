<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'type',
        'value',
        'min_purchase',
        'max_discount',
        'start_date',
        'end_date',
        'is_active',
        'applies_to',
        'product_id',
        'category_id',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
        'value' => 'decimal:2',
        'min_purchase' => 'decimal:2',
        'max_discount' => 'decimal:2',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function isValid()
    {
        $now = now();
        return $this->is_active &&
               ($this->start_date === null || $this->start_date <= $now) &&
               ($this->end_date === null || $this->end_date >= $now);
    }

    public function calculateDiscount($price, $quantity = 1)
    {
        if (!$this->isValid()) {
            return 0;
        }

        $total = $price * $quantity;

        // Check minimum purchase requirement
        if ($total < $this->min_purchase) {
            return 0;
        }

        $discountAmount = 0;

        switch ($this->type) {
            case 'percentage':
                $discountAmount = $total * ($this->value / 100);
                break;
            case 'fixed':
                $discountAmount = $this->value;
                break;
            case 'buy_x_get_y':
                // Implementation for buy X get Y free logic
                // For example: Buy 2 get 1 free (value = 1)
                $setsCount = floor($quantity / ($this->value + 1));
                $discountAmount = $setsCount * $price;
                break;
        }

        // Apply max discount cap if set
        if ($this->max_discount !== null && $discountAmount > $this->max_discount) {
            $discountAmount = $this->max_discount;
        }

        return $discountAmount;
    }
}
