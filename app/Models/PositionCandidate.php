<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PositionCandidate extends Model
{
    use HasFactory;

    protected $fillable = [
        'ideal_position_id',
        'member_id',
        'assigned_by',
        'status',
        'notes',
    ];

    public function idealPosition(): BelongsTo
    {
        return $this->belongsTo(IdealPosition::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function assigner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
