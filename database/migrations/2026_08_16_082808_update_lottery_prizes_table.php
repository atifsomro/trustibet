<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lotteries', function (Blueprint $table) {
            /*
            |--------------------------------------------------------------------------
            | New prize categories
            |--------------------------------------------------------------------------
            */

            $table->decimal('fourth_prize', 12, 2)
                ->default(0)
                ->after('third_prize');

            $table->decimal('fifth_prize', 12, 2)
                ->default(0)
                ->after('fourth_prize');

            /*
            |--------------------------------------------------------------------------
            | Remove old configurable winner counts
            |--------------------------------------------------------------------------
            |
            | Every prize category now has exactly one winner.
            |
            */

            $table->dropColumn([
                'second_prize_winners',
                'third_prize_winners',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('lotteries', function (Blueprint $table) {
            $table->unsignedSmallInteger('second_prize_winners')
                ->default(1)
                ->after('second_prize');

            $table->unsignedSmallInteger('third_prize_winners')
                ->default(1)
                ->after('third_prize');

            $table->dropColumn([
                'fourth_prize',
                'fifth_prize',
            ]);
        });
    }
};