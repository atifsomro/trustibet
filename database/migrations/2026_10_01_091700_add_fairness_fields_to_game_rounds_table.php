<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('game_rounds', function (Blueprint $table) {
            $table->string('server_seed_hash', 64)->nullable()->after('result_color');
            $table->string('server_seed', 64)->nullable()->after('server_seed_hash');
            $table->unsignedInteger('fairness_roll')->nullable()->after('server_seed');
        });
    }

    public function down(): void
    {
        Schema::table('game_rounds', function (Blueprint $table) {
            $table->dropColumn(['server_seed_hash', 'server_seed', 'fairness_roll']);
        });
    }
};
