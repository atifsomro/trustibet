<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->unique(
                ['wallet_id', 'idempotency_key'],
                'wallet_transactions_wallet_id_idempotency_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->dropUnique(
                'wallet_transactions_wallet_id_idempotency_unique'
            );
        });
    }
};