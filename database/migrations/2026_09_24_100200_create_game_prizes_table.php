<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('game_prizes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_package_id')->constrained()->cascadeOnDelete();
            $table->string('label');
            $table->decimal('prize_amount', 12, 2)->default(0);
            $table->unsignedInteger('weight')->default(1);
            $table->json('meta')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['game_package_id', 'is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('game_prizes');
    }
};
