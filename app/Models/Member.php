<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'email',
        'phone',
        'address',
        'points',
        'tier_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'points' => 'integer',
    ];

    public function tier()
    {
        return $this->belongsTo(MemberTier::class, 'tier_id');
    }

    public function pointTransactions()
    {
        return $this->hasMany(PointTransaction::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public static function generateCode()
    {
        $prefix = 'MBR';
        $timestamp = now()->format('ymd');

        $lastMember = self::orderBy('id', 'desc')->first();
        $lastId = $lastMember ? $lastMember->id : 0;

        $sequence = str_pad($lastId + 1, 4, '0', STR_PAD_LEFT);

        return $prefix . $timestamp . $sequence;
    }

    public function addPoints($points, $transactionId = null, $description = null)
    {
        if ($points <= 0) return;

        // Apply tier multiplier if applicable
        $adjustedPoints = round($points * $this->tier->points_multiplier);

        // Create point transaction record
        $this->pointTransactions()->create([
            'transaction_id' => $transactionId,
            'points' => $adjustedPoints,
            'type' => 'earn',
            'description' => $description ?? 'Points earned from purchase',
        ]);

        // Update member points balance
        $this->increment('points', $adjustedPoints);

        // Check if member qualifies for next tier
        $this->checkAndUpdateTier();

        return $adjustedPoints;
    }

    public function redeemPoints($points, $transactionId = null, $description = null)
    {
        if ($points <= 0 || $points > $this->points) {
            return false;
        }

        // Create point transaction record
        $this->pointTransactions()->create([
            'transaction_id' => $transactionId,
            'points' => -$points,
            'type' => 'redeem',
            'description' => $description ?? 'Points redeemed for discount',
        ]);

        // Update member points balance
        $this->decrement('points', $points);

        return true;
    }

    public function checkAndUpdateTier()
    {
        // Find the appropriate tier based on points
        $newTier = MemberTier::where('min_points', '<=', $this->points)
            ->orderBy('min_points', 'desc')
            ->first();

        if ($newTier && $this->tier_id != $newTier->id) {
            $this->tier_id = $newTier->id;
            $this->save();

            // Could add notification logic here
            return true;
        }

        return false;
    }
}
