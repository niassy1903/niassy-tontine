<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContributionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\TontineController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate'])->name('login.store');
    Route::get('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/register', [AuthController::class, 'store'])->name('register.store');
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');
Route::get('/tontines', [TontineController::class, 'publicIndex'])->name('tontines.public');
Route::get('/invitations/{token}', [InvitationController::class, 'show'])->name('invitations.show');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::get('/my-tontines', [TontineController::class, 'index'])->name('tontines.index');
    Route::get('/tontines/create', [TontineController::class, 'create'])->name('tontines.create');
    Route::post('/tontines', [TontineController::class, 'store'])->name('tontines.store');
    Route::get('/tontines/{tontine}/edit', [TontineController::class, 'edit'])->name('tontines.edit');
    Route::put('/tontines/{tontine}', [TontineController::class, 'update'])->name('tontines.update');
    Route::get('/tontines/{tontine}', [TontineController::class, 'show'])->name('tontines.show');
    Route::get('/tontines/{tontine}/members', [TontineController::class, 'members'])->name('tontines.members');
    Route::post('/tontines/{tontine}/members', [TontineController::class, 'addMember'])->name('tontines.members.add');
    Route::post('/tontines/{tontine}/invitations', [InvitationController::class, 'store'])->name('tontines.invitations.store');
    Route::post('/invitations/{token}/accept', [InvitationController::class, 'accept'])->name('invitations.accept');
    Route::post('/invitations/{token}/decline', [InvitationController::class, 'decline'])->name('invitations.decline');
    Route::post('/tontines/{tontine}/contributions', [ContributionController::class, 'store'])->name('tontines.contributions.store');
    Route::get('/contributions', [ContributionController::class, 'index'])->name('contributions.index');
    Route::post('/contributions/{contribution}/payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::post('/payments/{payment}/approve', [PaymentController::class, 'approve'])->name('payments.approve');
    Route::post('/payments/{payment}/reject', [PaymentController::class, 'reject'])->name('payments.reject');
    Route::get('/expenses', [ExpenseController::class, 'index'])->name('expenses.index');
    Route::post('/tontines/{tontine}/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
    Route::get('/tontines/{tontine}/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::post('/tontines/{tontine}/messages', [MessageController::class, 'store'])->name('messages.store');
    Route::delete('/messages/{message}', [MessageController::class, 'destroy'])->name('messages.destroy');
    Route::get('/tontines/{tontine}/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/tontines/{tontine}/reports/export', [ReportController::class, 'export'])->name('reports.export');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.read-all');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'read'])->name('notifications.read');
});

Route::prefix('admin')->middleware(['auth', 'superadmin'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
    Route::post('/users/{user}/toggle', [AdminController::class, 'toggleUser'])->name('admin.users.toggle');
});
