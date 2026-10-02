<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Beneficiary extends Model
{
    protected $fillable = ['tontine_id', 'user_id', 'position', 'scheduled_for', 'status'];
    protected function casts(): array { return ['scheduled_for' => 'date']; }
    public function tontine(): BelongsTo { return $this->belongsTo(Tontine::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}