<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Store the sales period that produced each draw.
     *
     * A lottery can be reused indefinitely. These columns make every
     * LotteryDraw a historical snapshot of its own round.
     */
    public function up(): void
    {
        Schema::table('lottery_draws', function (Blueprint $table) {
            $table->dateTime('sales_start_at')->nullable()->after('lottery_id');
            $table->dateTime('sales_end_at')->nullable()->after('sales_start_at');

            $table->json('prize_snapshot')->nullable()->after('sales_end_at');

            $table->index(
                ['lottery_id', 'sales_start_at', 'sales_end_at'],
                'lottery_draws_round_index'
            );
        });
    }

    public function down(): void
    {
        Schema::table('lottery_draws', function (Blueprint $table) {
            $table->dropIndex('lottery_draws_round_index');
            $table->dropColumn([
                'sales_start_at',
                'sales_end_at',
                'prize_snapshot',
            ]);
        });
    }
};
