<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberTier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'min_points',
        'points_multiplier',
        'benefits',
    ];

    public function members()
    {
        return $this->hasMany(Member::class, 'tier_id');
    }
}
