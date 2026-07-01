<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'email', 'username', 'role', 'is_active', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_SUPER_ADMIN = 'super_admin';
    public const ROLE_MENTOR = 'mentor';
    public const ROLE_KEUANGAN = 'pengurus_keuangan';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_active' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function createdActivities(): HasMany
    {
        return $this->hasMany(Activity::class, 'created_by');
    }

    public function memberNotes(): HasMany
    {
        return $this->hasMany(MemberNote::class, 'author_id');
    }

    public function assignedTargets(): HasMany
    {
        return $this->hasMany(DevelopmentTarget::class, 'assigned_by');
    }

    public function progressRecords(): HasMany
    {
        return $this->hasMany(ProgressUpdate::class, 'recorded_by');
    }

    public function recordedContributionPayments(): HasMany
    {
        return $this->hasMany(ContributionPayment::class, 'recorded_by');
    }

    public function recordedCashTransactions(): HasMany
    {
        return $this->hasMany(CashTransaction::class, 'recorded_by');
    }

    public function assignedPositionCandidates(): HasMany
    {
        return $this->hasMany(PositionCandidate::class, 'assigned_by');
    }

    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function hasAnyRole(array $roles): bool
    {
        return in_array($this->role, $roles, true);
    }

    public function roleLabel(): string
    {
        return match ($this->role) {
            self::ROLE_SUPER_ADMIN => 'Super Admin',
            self::ROLE_MENTOR => 'Mentor',
            self::ROLE_KEUANGAN => 'Pengurus Keuangan',
            default => ucfirst(str_replace('_', ' ', $this->role)),
        };
    }
}
