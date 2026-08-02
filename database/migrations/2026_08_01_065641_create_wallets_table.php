<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('user_id');
            // Cached balances
            $table->decimal('withdrawable_balance', 15, 2)->default(0);
            $table->decimal('bonus_balance', 15, 2)->default(0);
            $table->decimal('locked_balance', 15, 2)->default(0);

            $table->char('currency', 3)->default('USD');

            // Optimistic locking
            $table->unsignedInteger('version')->default(1);

            $table->timestamps();

            $table->index('currency');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallets');
    }
};