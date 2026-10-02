<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tontine extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id', 'name', 'slug', 'description', 'type', 'visibility',
        'currency', 'frequency', 'contribution_amount', 'starts_at', 'rules', 'status',
    ];

    protected function casts(): array
    {
        return ['starts_at' => 'date', 'contribution_amount' => 'decimal:2'];
    }

    public function owner(): BelongsTo { return $this->belongsTo(User::class, 'owner_id'); }
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'tontine_members')
            ->withPivot(['role', 'status', 'joined_at'])->withTimestamps();
    }
    public function periods(): HasMany { return $this->hasMany(ContributionPeriod::class); }
    public function contributions(): HasMany { return $this->hasMany(Contribution::class); }
    public function payments(): HasMany { return $this->hasMany(Payment::class); }
    public function expenses(): HasMany { return $this->hasMany(Expense::class); }
    public function beneficiaries(): HasMany { return $this->hasMany(Beneficiary::class); }
    public function messages(): HasMany { return $this->hasMany(TontineMessage::class); }

    public function isMember(User $user): bool
    {
        return $this->members()->whereKey($user->id)->wherePivot('status', 'active')->exists();
    }

    public function canManage(User $user): bool
    {
        if ($this->owner_id === $user->id) return true;
        return $this->members()->whereKey($user->id)
            ->wherePivotIn('role', ['admin', 'treasurer'])->wherePivot('status', 'active')->exists();
    }

    public function getBalanceAttribute(): float
    {
        return (float) $this->payments()->where('status', 'approved')->sum('amount')
            - (float) $this->expenses()->sum('amount');
    }
}