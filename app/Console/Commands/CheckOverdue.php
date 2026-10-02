<?php

namespace App\Console\Commands;

use App\Models\Contribution;
use Illuminate\Console\Command;

class CheckOverdue extends Command
{
    protected $signature = 'tontine:check-overdue';
    protected $description = 'Marque les cotisations dont l’échéance est dépassée.';

    public function handle(): int
    {
        $count = Contribution::whereIn('status', ['pending', 'partial'])
            ->whereHas('period', fn ($query) => $query->whereDate('due_at', '<', today()))
            ->update(['status' => 'overdue']);
        $this->info("{$count} cotisation(s) marquée(s) en retard.");
        return self::SUCCESS;
    }
}