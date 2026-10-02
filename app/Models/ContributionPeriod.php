<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContributionPeriod extends Model
{
    protected $fillable = ['tontine_id', 'starts_at', 'ends_at', 'due_at', 'expected_amount', 'status'];
    protected function casts(): array { return ['starts_at' => 'date', 'ends_at' => 'date', 'due_at' => 'date', 'expected_amount' => 'decimal:2']; }
    public function tontine(): BelongsTo { return $this->belongsTo(Tontine::class); }
    public function contributions(): HasMany { return $this->hasMany(Contribution::class); }
}