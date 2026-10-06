<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wallet_transactions', function (Blueprint $table) {
            // Powers the daily leaderboard: filter by date range + type, group by wallet.
            $table->index(['created_at', 'type', 'wallet_id'], 'wallet_tx_leaderboard_idx');
        });

        Schema::table('wallets', function (Blueprint $table) {
            if (! collect(Schema::getIndexes('wallets'))->contains(fn ($i) => $i['columns'] === ['user_id'])) {
                $table->index('user_id', 'wallets_user_id_idx');
            }
        });
    }

    public function down(): void
    {
        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->dropIndex('wallet_tx_leaderboard_idx');
        });

        Schema::table('wallets', function (Blueprint $table) {
            if (collect(Schema::getIndexes('wallets'))->contains(fn ($i) => $i['name'] === 'wallets_user_id_idx')) {
                $table->dropIndex('wallets_user_id_idx');
            }
        });
    }
};
