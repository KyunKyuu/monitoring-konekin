<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IdealPosition extends Model
{
    use HasFactory;

    protected $fillable = [
        'function_name',
        'position_name',
        'goal',
        'responsibilities',
        'required_count',
        'status',
    ];

    public function candidates(): HasMany
    {
        return $this->hasMany(PositionCandidate::class)->latest();
    }
}
