<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExpenseRequest;
use App\Models\Expense;
use App\Models\Tontine;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    public function index(): View
    {
        $expenses = Expense::whereIn('tontine_id', auth()->user()->tontines()->select('tontines.id'))->with('tontine')->latest()->paginate(15);
        return view('expenses.index', compact('expenses'));
    }

    public function store(StoreExpenseRequest $request, Tontine $tontine): RedirectResponse
    {
        abort_unless($tontine->canManage(auth()->user()), 403);
        $tontine->expenses()->create($request->validated() + ['author_id' => auth()->id()]);
        return back()->with('success', 'Dépense enregistrée dans la trésorerie.');
    }
}