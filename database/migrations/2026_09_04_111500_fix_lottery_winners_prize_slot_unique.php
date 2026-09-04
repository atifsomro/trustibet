<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lottery_winners', function (Blueprint $table) {
            $table->dropUnique('lottery_winners_prize_slot_unique');

            // One prize slot per draw (supports recurring lottery rounds)
            $table->unique(
                ['draw_id', 'prize_category', 'prize_position'],
                'lottery_winners_draw_prize_slot_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('lottery_winners', function (Blueprint $table) {
            $table->dropUnique('lottery_winners_draw_prize_slot_unique');

            $table->unique(
                ['lottery_id', 'prize_category', 'prize_position'],
                'lottery_winners_prize_slot_unique'
            );
        });
    }
};
