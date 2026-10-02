<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Tontine;
use App\Models\ContributionPeriod;
use App\Models\Contribution;
use App\Models\Payment;
use App\Models\Expense;
use App\Models\Beneficiary;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::factory()->create(['name' => 'Administrateur Niassy', 'email' => 'admin@niassytontine.com', 'role' => 'super_admin', 'password' => Hash::make(env('NIASSY_ADMIN_PASSWORD', Str::random(32)))]);
        $awa = User::factory()->create(['name' => 'Awa Ndiaye', 'email' => 'awa@niassytontine.com', 'phone' => '+221 77 000 00 01']);
        $moussa = User::factory()->create(['name' => 'Moussa Diop', 'email' => 'moussa@niassytontine.com', 'phone' => '+221 77 000 00 02']);
        $tontine = Tontine::create(['owner_id' => $awa->id, 'name' => 'Solidarité Parcelles', 'slug' => 'solidarite-parcelles', 'description' => 'Une communauté qui avance ensemble, mois après mois.', 'type' => 'association', 'visibility' => 'public', 'currency' => 'XOF', 'frequency' => 'monthly', 'contribution_amount' => 25000, 'starts_at' => now()->startOfMonth(), 'status' => 'active']);
        $tontine->members()->attach([$awa->id => ['role' => 'owner', 'status' => 'active', 'joined_at' => now()], $moussa->id => ['role' => 'treasurer', 'status' => 'active', 'joined_at' => now()]]);
        $period = ContributionPeriod::create(['tontine_id' => $tontine->id, 'starts_at' => now()->startOfMonth(), 'ends_at' => now()->endOfMonth(), 'due_at' => now()->addDays(10), 'expected_amount' => 25000, 'status' => 'open']);
        $c1 = Contribution::create(['tontine_id' => $tontine->id, 'contribution_period_id' => $period->id, 'user_id' => $awa->id, 'expected_amount' => 25000, 'status' => 'paid']);
        $c2 = Contribution::create(['tontine_id' => $tontine->id, 'contribution_period_id' => $period->id, 'user_id' => $moussa->id, 'expected_amount' => 25000, 'status' => 'partial']);
        Payment::create(['tontine_id' => $tontine->id, 'contribution_id' => $c1->id, 'user_id' => $awa->id, 'amount' => 25000, 'method' => 'wave', 'reference' => 'WAVE-2026-001', 'paid_at' => now()->subDays(2), 'status' => 'approved', 'validated_by' => $moussa->id, 'validated_at' => now()->subDay()]);
        Payment::create(['tontine_id' => $tontine->id, 'contribution_id' => $c2->id, 'user_id' => $moussa->id, 'amount' => 10000, 'method' => 'cash', 'paid_at' => now()->subDay(), 'status' => 'pending']);
        Expense::create(['tontine_id' => $tontine->id, 'author_id' => $awa->id, 'reason' => 'Aide sociale à un membre', 'amount' => 15000, 'category' => 'social', 'spent_at' => now()->subDays(3)]);
        Beneficiary::create(['tontine_id' => $tontine->id, 'user_id' => $awa->id, 'position' => 1, 'scheduled_for' => now()->startOfMonth(), 'status' => 'current']);
        ActivityLog::create(['user_id' => $awa->id, 'tontine_id' => $tontine->id, 'action' => 'created', 'module' => 'tontines', 'description' => 'Tontine Solidarité Parcelles créée', 'ip_address' => '127.0.0.1']);
    }
}
