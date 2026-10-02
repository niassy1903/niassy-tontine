<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    protected $fillable = ['tontine_id', 'author_id', 'reason', 'amount', 'category', 'spent_at', 'receipt_path', 'comment'];
    protected function casts(): array { return ['amount' => 'decimal:2', 'spent_at' => 'date']; }
    public function tontine(): BelongsTo { return $this->belongsTo(Tontine::class); }
    public function author(): BelongsTo { return $this->belongsTo(User::class, 'author_id'); }
}