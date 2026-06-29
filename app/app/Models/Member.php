<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'gender',
        'status',
        'target_role',
        'target_function',
        'note_priority',
    ];

    public function activities(): BelongsToMany
    {
        return $this->belongsToMany(Activity::class)
            ->withPivot(['role_in_activity', 'attendance_status'])
            ->withTimestamps();
    }

    public function notes(): HasMany
    {
        return $this->hasMany(MemberNote::class)->latest();
    }

    public function developmentTargets(): HasMany
    {
        return $this->hasMany(DevelopmentTarget::class)->latest();
    }

    public function progressUpdates(): HasMany
    {
        return $this->hasMany(ProgressUpdate::class)->latest();
    }

    public function contributions(): HasMany
    {
        return $this->hasMany(Contribution::class)->latest();
    }

    public function positionCandidates(): HasMany
    {
        return $this->hasMany(PositionCandidate::class)->latest();
    }
}
