<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tontines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('type')->default('association');
            $table->string('visibility')->default('private')->index();
            $table->string('currency', 3)->default('XOF');
            $table->string('frequency')->default('monthly');
            $table->decimal('contribution_amount', 14, 2)->default(0);
            $table->date('starts_at');
            $table->text('rules')->nullable();
            $table->string('status')->default('active')->index();
            $table->timestamps();
        });

        Schema::create('tontine_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tontine_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('role')->default('member');
            $table->string('status')->default('active');
            $table->timestamp('joined_at')->nullable();
            $table->timestamps();
            $table->unique(['tontine_id', 'user_id']);
        });

        Schema::create('contribution_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tontine_id')->constrained()->cascadeOnDelete();
            $table->date('starts_at');
            $table->date('ends_at');
            $table->date('due_at');
            $table->decimal('expected_amount', 14, 2);
            $table->string('status')->default('open')->index();
            $table->timestamps();
        });

        Schema::create('contributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tontine_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contribution_period_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('expected_amount', 14, 2);
            $table->string('status')->default('pending')->index();
            $table->timestamps();
            $table->unique(['contribution_period_id', 'user_id']);
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tontine_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contribution_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 14, 2);
            $table->string('method')->default('cash');
            $table->string('reference')->nullable();
            $table->dateTime('paid_at');
            $table->string('proof_path')->nullable();
            $table->text('comment')->nullable();
            $table->string('status')->default('pending')->index();
            $table->foreignId('validated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('validated_at')->nullable();
            $table->timestamps();
        });

        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tontine_id')->constrained()->cascadeOnDelete();
            $table->foreignId('author_id')->constrained('users')->cascadeOnDelete();
            $table->string('reason');
            $table->decimal('amount', 14, 2);
            $table->string('category')->default('other');
            $table->date('spent_at');
            $table->string('receipt_path')->nullable();
            $table->text('comment')->nullable();
            $table->timestamps();
        });

        Schema::create('beneficiaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tontine_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('position');
            $table->date('scheduled_for');
            $table->string('status')->default('upcoming');
            $table->timestamps();
            $table->unique(['tontine_id', 'position']);
            $table->unique(['tontine_id', 'scheduled_for']);
        });

        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('tontine_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action');
            $table->string('module');
            $table->text('description');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
            $table->index(['module', 'created_at']);
        });

        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('price', 14, 2)->default(0);
            $table->string('currency', 3)->default('XOF');
            $table->string('period')->default('monthly');
            $table->unsignedInteger('max_tontines')->nullable();
            $table->unsignedInteger('max_members')->nullable();
            $table->unsignedInteger('max_operations')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('plan_id')->constrained()->restrictOnDelete();
            $table->date('starts_at');
            $table->date('ends_at')->nullable();
            $table->string('status')->default('active')->index();
            $table->decimal('price', 14, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('plans');
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('beneficiaries');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('contributions');
        Schema::dropIfExists('contribution_periods');
        Schema::dropIfExists('tontine_members');
        Schema::dropIfExists('tontines');
    }
};