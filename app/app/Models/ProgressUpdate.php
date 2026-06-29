<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgressUpdate extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'development_target_id',
        'activity_id',
        'recorded_by',
        'area',
        'stage',
        'status',
        'summary',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function target(): BelongsTo
    {
        return $this->belongsTo(DevelopmentTarget::class, 'development_target_id');
    }

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
