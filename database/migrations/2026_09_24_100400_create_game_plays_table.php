<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('game_plays', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('game_id')->constrained()->cascadeOnDelete();
            $table->foreignId('game_package_id')->constrained()->cascadeOnDelete();
            $table->foreignId('game_round_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('game_prize_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('fee_amount', 12, 2)->default(0);
            $table->decimal('prize_amount', 12, 2)->default(0);
            $table->string('status', 16)->default('pending');
            $table->json('selection')->nullable();
            $table->json('outcome')->nullable();
            $table->unsignedBigInteger('bet_transaction_id')->nullable();
            $table->unsignedBigInteger('win_transaction_id')->nullable();
            $table->string('idempotency_key')->nullable();
            $table->timestamps();

            $table->foreign('bet_transaction_id')
                ->references('id')
                ->on('wallet_transactions')
                ->nullOnDelete();
            $table->foreign('win_transaction_id')
                ->references('id')
                ->on('wallet_transactions')
                ->nullOnDelete();

            $table->unique(['user_id', 'idempotency_key']);
            $table->index(['game_id', 'status']);
            $table->index(['user_id', 'created_at']);
            $table->index(['game_round_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('game_plays');
    }
};
