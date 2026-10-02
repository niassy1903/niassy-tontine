<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\Tontine;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function dashboard(): View
    {
        return view('admin.dashboard', [
            'usersCount' => User::count(), 'activeUsers' => User::where('status', 'active')->count(),
            'tontinesCount' => Tontine::count(), 'paymentsTotal' => Payment::where('status', 'approved')->sum('amount'),
            'expensesTotal' => Expense::sum('amount'), 'recentActivity' => ActivityLog::with('user')->latest()->take(8)->get(),
        ]);
    }

    public function users(Request $request): View
    {
        $users = User::query()->when($request->string('search')->isNotEmpty(), fn ($q) => $q->where(fn ($q) => $q->where('name', 'like', '%'.$request->string('search').'%')->orWhere('email', 'like', '%'.$request->string('search').'%')))->latest()->paginate(15)->withQueryString();
        return view('admin.users', compact('users'));
    }

    public function toggleUser(User $user): RedirectResponse
    {
        abort_if($user->isSuperAdmin(), 403);
        $user->update(['status' => $user->status === 'active' ? 'suspended' : 'active']);
        return back()->with('success', 'Statut utilisateur mis à jour.');
    }
}