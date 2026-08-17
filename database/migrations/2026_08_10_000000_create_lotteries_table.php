<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lotteries', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->decimal('ticket_price', 12, 2);
            $table->string('currency', 3)->default('USD');
            $table->dateTime('sales_start_at')->nullable();
            $table->dateTime('sales_end_at');
            $table->dateTime('draw_at')->nullable();

            $table->decimal('first_prize', 12, 2);
            $table->decimal('second_prize', 12, 2);
            $table->unsignedSmallInteger('second_prize_winners')->nullable();
            $table->decimal('third_prize', 12, 2);
            $table->unsignedSmallInteger('third_prize_winners')->nullable();
            $table->unsignedInteger('max_tickets')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->text('description')->nullable();
            $table->string('status', 20)->default('draft');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['status', 'is_active']);
            $table->index('sales_end_at');
            $table->index('sort_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lotteries');
    }
};
