<?php

namespace App\Http\Controllers;

use App\Models\Tontine;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $tontines = $user->tontines()->withCount('members')->with(['owner'])->latest()->get();
        $contributions = $user->contributions()->with(['tontine', 'period'])->latest()->take(5)->get();
        return view('dashboard.index', [
            'tontines' => $tontines,
            'contributions' => $contributions,
            'totalPaid' => $user->payments()->where('status', 'approved')->sum('amount'),
            'pendingPayments' => $user->payments()->where('status', 'pending')->count(),
            'overdue' => $user->contributions()->where('status', 'overdue')->count(),
            'publicTontines' => Tontine::where('visibility', 'public')->where('status', 'active')->withCount('members')->latest()->take(4)->get(),
        ]);
    }
}