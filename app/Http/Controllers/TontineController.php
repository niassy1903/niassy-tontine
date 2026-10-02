<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTontineRequest;
use App\Http\Requests\UpdateTontineRequest;
use App\Models\ActivityLog;
use App\Models\ContributionPeriod;
use App\Models\Tontine;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TontineController extends Controller
{
    public function index(): View
    {
        $tontines = auth()->user()->tontines()->withCount('members')->latest()->paginate(9);
        return view('tontines.index', compact('tontines'));
    }

    public function publicIndex(): View
    {
        $tontines = Tontine::where('visibility', 'public')->where('status', 'active')->withCount('members')->latest()->paginate(12);
        return view('tontines.public', compact('tontines'));
    }

    public function create(): View { return view('tontines.create'); }

    public function edit(Tontine $tontine): View
    {
        $this->authorize('manage', $tontine);
        return view('tontines.edit', compact('tontine'));
    }

    public function store(StoreTontineRequest $request): RedirectResponse
    {
        $tontine = Tontine::create($request->validated() + [
            'owner_id' => auth()->id(),
            'slug' => Str::slug($request->string('name')).'-'.Str::lower(Str::random(5)),
            'currency' => 'XOF',
        ]);
        $tontine->members()->attach(auth()->id(), ['role' => 'owner', 'status' => 'active', 'joined_at' => now()]);
        ContributionPeriod::create([
            'tontine_id' => $tontine->id, 'starts_at' => $tontine->starts_at,
            'ends_at' => $tontine->starts_at->copy()->addMonth()->subDay(), 'due_at' => $tontine->starts_at->copy()->addMonth()->subDay(),
            'expected_amount' => $tontine->contribution_amount, 'status' => 'open',
        ]);
        ActivityLog::create(['user_id' => auth()->id(), 'tontine_id' => $tontine->id, 'action' => 'created', 'module' => 'tontines', 'description' => 'Création de la tontine '.$tontine->name, 'ip_address' => request()->ip(), 'user_agent' => request()->userAgent()]);
        return redirect()->route('tontines.show', $tontine)->with('success', 'Votre tontine a été créée.');
    }

    public function update(UpdateTontineRequest $request, Tontine $tontine): RedirectResponse
    {
        $this->authorize('manage', $tontine);
        $tontine->update($request->validated());
        ActivityLog::create([
            'user_id' => auth()->id(),
            'tontine_id' => $tontine->id,
            'action' => 'updated',
            'module' => 'tontines',
            'description' => 'Paramètres de la tontine '.$tontine->name.' mis à jour',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
        return redirect()->route('tontines.show', $tontine)->with('success', 'Les paramètres de la tontine ont été enregistrés.');
    }

    public function show(Tontine $tontine): View
    {
        $this->authorize('view', $tontine);
        $tontine->loadCount('members');
        $contributions = $tontine->contributions()->with(['user', 'period'])->latest()->take(8)->get();
        $payments = $tontine->payments()->with('user')->latest()->take(6)->get();
        $pendingPayments = $tontine->payments()->where('status', 'pending')->with(['user', 'contribution'])->latest()->get();
        $expenses = $tontine->expenses()->with('author')->latest()->take(6)->get();
        $currentBeneficiary = $tontine->beneficiaries()->where('status', 'current')->with('user')->first();
        return view('tontines.show', compact('tontine', 'contributions', 'payments', 'pendingPayments', 'expenses', 'currentBeneficiary'));
    }

    public function members(Tontine $tontine): View
    {
        $this->authorize('manage', $tontine);
        $members = $tontine->members()->latest('tontine_members.created_at')->paginate(15);
        return view('tontines.members', compact('tontine', 'members'));
    }

    public function addMember(Tontine $tontine): RedirectResponse
    {
        $this->authorize('manage', $tontine);
        request()->validate(['email' => ['required', 'email', 'exists:users,email']]);
        $user = \App\Models\User::where('email', request('email'))->firstOrFail();
        $tontine->members()->syncWithoutDetaching([$user->id => ['role' => 'member', 'status' => 'active', 'joined_at' => now()]]);
        return back()->with('success', 'Le membre a été ajouté à la tontine.');
    }

}