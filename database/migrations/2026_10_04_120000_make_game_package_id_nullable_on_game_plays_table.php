<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('game_plays', function (Blueprint $table) {
            $table->dropForeign(['game_package_id']);
        });

        Schema::table('game_plays', function (Blueprint $table) {
            $table->unsignedBigInteger('game_package_id')->nullable()->change();
            $table->foreign('game_package_id')
                ->references('id')
                ->on('game_packages')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('game_plays', function (Blueprint $table) {
            $table->dropForeign(['game_package_id']);
        });

        Schema::table('game_plays', function (Blueprint $table) {
            $table->unsignedBigInteger('game_package_id')->nullable(false)->change();
            $table->foreign('game_package_id')
                ->references('id')
                ->on('game_packages')
                ->cascadeOnDelete();
        });
    }
};
