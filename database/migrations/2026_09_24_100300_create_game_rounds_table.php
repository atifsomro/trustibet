<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('game_rounds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('round_number');
            $table->string('status', 16)->default('betting');
            $table->string('result_color')->nullable();
            $table->timestamp('starts_at');
            $table->timestamp('locks_at');
            $table->timestamp('ends_at');
            $table->timestamp('settled_at')->nullable();
            $table->timestamps();

            $table->unique(['game_id', 'round_number']);
            $table->index(['game_id', 'status']);
            $table->index(['status', 'locks_at', 'ends_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('game_rounds');
    }
};
