<?php

use App\Enums\BonusType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bonuses', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('user_id');

            // welcome, referral, cashback etc.
            $table->enum('type', BonusType::values());

            // Stored in cents
            $table->decimal('initial_amount', 15, 2);

            $table->decimal('remaining_amount', 15, 2);

            // active, expired, voided, completed
            $table->string('status', 20)->default('active');

            $table->timestamp('activated_at')->nullable();

            $table->timestamp('expires_at')->nullable();

            $table->timestamp('completed_at')->nullable();

            $table->timestamps();

            $table->index('user_id');
            $table->index('status');
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bonuses');
    }
};
