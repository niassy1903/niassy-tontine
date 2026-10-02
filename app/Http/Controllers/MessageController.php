<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMessageRequest;
use App\Models\Tontine;
use App\Models\TontineMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MessageController extends Controller
{
    public function index(Tontine $tontine): View
    {
        $this->authorize('view', $tontine);
        $messages = $tontine->messages()->with('user')->latest()->paginate(20);
        return view('messages.index', compact('tontine', 'messages'));
    }

    public function store(StoreMessageRequest $request, Tontine $tontine): RedirectResponse
    {
        $this->authorize('createMessage', $tontine);
        $tontine->messages()->create($request->validated() + ['user_id' => auth()->id()]);
        return back()->with('success', 'Votre message a été publié dans la communauté.');
    }

    public function destroy(TontineMessage $message): RedirectResponse
    {
        $this->authorize('delete', $message);
        $message->delete();
        return back()->with('success', 'Le message a été supprimé.');
    }
}