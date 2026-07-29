<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('deposits', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            // Bank account selected by the user
            $table->integer('bank_account_id');
            $table->decimal('amount', 12, 2);
            $table->string('currency', 10)->default('USD');
            // Optional bank transaction/reference number
            $table->string('reference_number')->nullable();
            // Uploaded payment proof
            $table->string('payment_proof');
            // User remarks
            $table->text('remarks')->nullable();
            $table->enum('status', [
                'pending',
                'approved',
                'rejected',
            ])->default('pending');
            // Admin details
            $table->integer('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('admin_remarks')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'status']);
            $table->index('bank_account_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deposits');
    }
};