<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();

            $table->uuid('uuid')->unique();

            $table->bigInteger('wallet_id');

            $table->integer('bonus_id')->nullable();

            // withdrawable | bonus
            $table->string('balance_type', 20);

            // game_win, deposit, bonus_granted etc.
            $table->string('type', 50);

            // Stored in cents
            $table->decimal('amount', 15, 2);

            // Balance after this transaction
            $table->decimal('balance_after', 15, 2);

            $table->nullableMorphs('reference');

            $table->string('idempotency_key')->nullable()->unique();

            $table->json('meta')->nullable();

            $table->timestamp('created_at')->useCurrent();

            $table->index('wallet_id');
            $table->index('balance_type');
            $table->index('type');
            $table->index(['wallet_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};