<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = ['tontine_id', 'contribution_id', 'user_id', 'amount', 'method', 'reference', 'paid_at', 'proof_path', 'comment', 'status', 'validated_by', 'validated_at'];
    protected function casts(): array { return ['amount' => 'decimal:2', 'paid_at' => 'datetime', 'validated_at' => 'datetime']; }
    public function tontine(): BelongsTo { return $this->belongsTo(Tontine::class); }
    public function contribution(): BelongsTo { return $this->belongsTo(Contribution::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function validator(): BelongsTo { return $this->belongsTo(User::class, 'validated_by'); }
}