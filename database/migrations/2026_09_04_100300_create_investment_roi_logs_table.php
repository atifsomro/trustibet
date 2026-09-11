<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('investment_roi_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_investment_id');
            $table->unsignedBigInteger('user_id');
            $table->date('roi_date');
            $table->decimal('amount', 15, 2);
            $table->string('status', 20)->default('pending');
            $table->timestamp('claimed_at')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->unsignedBigInteger('claim_transaction_id')->nullable();
            $table->timestamps();

            $table->unique(['user_investment_id', 'roi_date']);
            $table->index(['user_id', 'status', 'roi_date']);
            $table->index(['status', 'roi_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('investment_roi_logs');
    }
};
