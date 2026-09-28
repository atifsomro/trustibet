<?php

declare(strict_types=1);

use App\Enums\ReferralStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('referrer_id');
            $table->unsignedBigInteger('referred_user_id');
            $table->unsignedBigInteger('deposit_id')->nullable();

            $table->decimal('deposit_amount', 15, 2)->default(0);
            $table->decimal('bonus_percent', 8, 2)->default(0);
            $table->decimal('bonus_amount', 15, 2)->default(0);

            $table->string('status', 20)->default(ReferralStatus::PENDING->value);

            $table->unsignedBigInteger('bonus_id')->nullable();
            $table->timestamp('unlocked_at')->nullable();

            $table->timestamps();

            $table->unique('referred_user_id');
            $table->index('referrer_id');
            $table->index('status');

            $table->foreign('referrer_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            $table->foreign('referred_user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            $table->foreign('deposit_id')
                ->references('id')
                ->on('deposits')
                ->nullOnDelete();

            $table->foreign('bonus_id')
                ->references('id')
                ->on('bonuses')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referrals');
    }
};
