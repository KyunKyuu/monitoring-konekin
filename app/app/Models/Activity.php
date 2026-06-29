<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by',
        'category_id',
        'subcategory_id',
        'category',
        'sub_category',
        'title',
        'theme',
        'scheduled_at',
        'location',
        'status',
        'summary_note',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function categoryModel(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function subcategoryModel(): BelongsTo
    {
        return $this->belongsTo(Subcategory::class, 'subcategory_id');
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(Member::class)
            ->withPivot(['role_in_activity', 'attendance_status'])
            ->withTimestamps();
    }

    public function notes(): HasMany
    {
        return $this->hasMany(MemberNote::class)->latest();
    }

    public function progressUpdates(): HasMany
    {
        return $this->hasMany(ProgressUpdate::class)->latest();
    }

    public function cashTransactions(): HasMany
    {
        return $this->hasMany(CashTransaction::class)->latest();
    }
}
