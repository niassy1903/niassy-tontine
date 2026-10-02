<?php

namespace App\Http\Controllers;

use App\Models\Contribution;
use App\Models\ContributionPeriod;
use App\Models\Tontine;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ContributionController extends Controller
{
    public function index(): View
    {
        $contributions = auth()->user()->contributions()->with(['tontine', 'period'])->latest()->paginate(15);
        return view('contributions.index', compact('contributions'));
    }

    public function store(Tontine $tontine): RedirectResponse
    {
        abort_unless($tontine->canManage(auth()->user()), 403);
        $data = request()->validate(['due_at' => ['required', 'date'], 'expected_amount' => ['required', 'numeric', 'min:1']]);
        DB::transaction(function () use ($tontine, $data) {
            $period = ContributionPeriod::create([
                'tontine_id' => $tontine->id, 'starts_at' => now()->startOfMonth(), 'ends_at' => now()->endOfMonth(),
                'due_at' => $data['due_at'], 'expected_amount' => $data['expected_amount'], 'status' => 'open',
            ]);
            foreach ($tontine->members()->wherePivot('status', 'active')->get() as $member) {
                Contribution::create(['tontine_id' => $tontine->id, 'contribution_period_id' => $period->id, 'user_id' => $member->id, 'expected_amount' => $data['expected_amount'], 'status' => 'pending']);
            }
        });
        return back()->with('success', 'La période de cotisation a été ouverte.');
    }
}