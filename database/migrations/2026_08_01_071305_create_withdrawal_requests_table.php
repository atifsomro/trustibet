<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('withdrawal_requests', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('user_id');

            $table->bigInteger('wallet_id');

            // Stored in cents
            $table->decimal('amount', 15, 2);

            // pending, approved, rejected, cancelled
            $table->string('status', 20)->default('pending');

            $table->string('payment_method')->nullable();

            $table->json('account_details')->nullable();

            $table->bigInteger('approved_by')->nullable();

            $table->text('remarks')->nullable();

            $table->timestamp('requested_at')->nullable();

            $table->timestamp('processed_at')->nullable();

            $table->timestamps();

            $table->index('user_id');
            $table->index('status');
            $table->index('requested_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('withdrawal_requests');
    }
};