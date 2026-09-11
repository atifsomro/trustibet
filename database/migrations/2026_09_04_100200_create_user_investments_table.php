<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_investments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('investment_package_id')->nullable();

            // Snapshots at purchase time
            $table->string('package_name');
            $table->decimal('price', 15, 2);
            $table->decimal('daily_roi', 15, 2);
            $table->unsignedInteger('total_days');
            $table->text('description')->nullable();

            $table->date('starts_at');
            $table->date('ends_at');
            $table->string('status', 20)->default('active');

            $table->unsignedBigInteger('purchase_transaction_id')->nullable();
            $table->unsignedBigInteger('principal_return_transaction_id')->nullable();

            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['status', 'ends_at']);
            $table->index('investment_package_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_investments');
    }
};
