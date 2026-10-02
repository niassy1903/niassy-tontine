<?php

namespace App\Http\Controllers;

use App\Models\Tontine;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Tontine $tontine): View
    {
        $this->authorize('view', $tontine);
        $tontine->loadCount('members');
        $monthExpression = DB::connection()->getDriverName() === 'mysql'
            ? "DATE_FORMAT(paid_at, '%Y-%m')"
            : "strftime('%Y-%m', paid_at)";
        return view('reports.index', [
            'tontine' => $tontine,
            'totalContributions' => $tontine->payments()->where('status', 'approved')->sum('amount'),
            'totalExpenses' => $tontine->expenses()->sum('amount'),
            'paidContributions' => $tontine->contributions()->where('status', 'paid')->count(),
            'overdueContributions' => $tontine->contributions()->where('status', 'overdue')->count(),
            'monthlyPayments' => $tontine->payments()->where('status', 'approved')->selectRaw("{$monthExpression} as month, SUM(amount) as total")->groupBy('month')->orderBy('month')->get(),
        ]);
    }

    public function export(Tontine $tontine): Response
    {
        $this->authorize('view', $tontine);
        $rows = [['Date', 'Type', 'Description', 'Membre', 'Montant', 'Statut']];
        foreach ($tontine->payments()->with(['user', 'contribution'])->latest()->get() as $payment) {
            $rows[] = [$payment->paid_at->format('Y-m-d'), 'Paiement', $payment->reference ?: 'Cotisation', $payment->user->name, $payment->amount, $payment->status];
        }
        foreach ($tontine->expenses()->with('author')->latest()->get() as $expense) {
            $rows[] = [$expense->spent_at->format('Y-m-d'), 'Dépense', $expense->reason, $expense->author->name, -$expense->amount, 'approved'];
        }
        $csv = collect($rows)->map(fn (array $row) => collect($row)->map(fn ($value) => '"'.str_replace('"', '""', (string) $value).'"')->implode(';'))->implode("\n");
        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="rapport-'.$tontine->slug.'-'.now()->format('Y-m-d').'.csv"',
        ]);
    }
}