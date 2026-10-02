<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Invitation extends Model
{
    protected $fillable = ['tontine_id', 'invited_by', 'email', 'token', 'role', 'expires_at', 'accepted_at', 'declined_at'];

    protected function casts(): array
    {
        return ['expires_at' => 'datetime', 'accepted_at' => 'datetime', 'declined_at' => 'datetime'];
    }

    protected static function booted(): void
    {
        static::creating(function (Invitation $invitation) {
            $invitation->token ??= Str::random(64);
            $invitation->expires_at ??= now()->addDays(7);
        });
    }

    public function tontine(): BelongsTo { return $this->belongsTo(Tontine::class); }
    public function inviter(): BelongsTo { return $this->belongsTo(User::class, 'invited_by'); }
    public function isPending(): bool { return is_null($this->accepted_at) && is_null($this->declined_at) && $this->expires_at->isFuture(); }
}