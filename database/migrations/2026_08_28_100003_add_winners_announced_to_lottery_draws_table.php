<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lottery_draws', function (Blueprint $table) {
            $table->boolean('winners_announced')
                ->default(true)
                ->after('total_winners');
        });
    }

    public function down(): void
    {
        Schema::table('lottery_draws', function (Blueprint $table) {
            $table->dropColumn('winners_announced');
        });
    }
};
