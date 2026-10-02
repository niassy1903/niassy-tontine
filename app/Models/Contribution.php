<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contribution extends Model
{
    protected $fillable = ['tontine_id', 'contribution_period_id', 'user_id', 'expected_amount', 'status'];
    protected function casts(): array { return ['expected_amount' => 'decimal:2']; }
    public function tontine(): BelongsTo { return $this->belongsTo(Tontine::class); }
    public function period(): BelongsTo { return $this->belongsTo(ContributionPeriod::class, 'contribution_period_id'); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function payments(): HasMany { return $this->hasMany(Payment::class); }
    public function getPaidAmountAttribute(): float { return (float) $this->payments()->where('status', 'approved')->sum('amount'); }
    public function getRemainingAmountAttribute(): float { return max(0, (float) $this->expected_amount - $this->paid_amount); }
}