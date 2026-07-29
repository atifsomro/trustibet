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
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // e.g. Meezan Bank PKR
            $table->string('bank_name');
            $table->string('account_title');
            $table->string('account_number');
            $table->string('iban')->nullable();
            $table->string('swift_code')->nullable();
            $table->string('branch_name')->nullable();
            $table->string('branch_code')->nullable();
            $table->string('currency', 10)->default('PKR');
            $table->decimal('conversion_rate', 10, 2);
            // JazzCash, EasyPaisa, Bank Transfer, etc.
            $table->enum('type', [
                'bank',
                'jazzcash',
                'easypaisa',
                'other',
            ])->default('bank');
            // QR Code image (optional)
            $table->string('picture');
            $table->string('qr_code')->nullable();
            // Additional instructions shown to the user
            $table->text('instructions')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_accounts');
    }
};