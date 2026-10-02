<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TontineMessage extends Model
{
    use SoftDeletes;

    protected $fillable = ['tontine_id', 'user_id', 'type', 'body', 'pinned', 'edited_at'];

    protected function casts(): array
    {
        return ['pinned' => 'boolean', 'edited_at' => 'datetime'];
    }

    public function tontine(): BelongsTo { return $this->belongsTo(Tontine::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}