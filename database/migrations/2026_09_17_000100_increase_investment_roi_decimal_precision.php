<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('investment_packages', function (Blueprint $table) {
            $table->decimal('daily_roi', 15, 4)->change();
        });

        Schema::table('user_investments', function (Blueprint $table) {
            $table->decimal('daily_roi', 15, 4)->change();
        });

        Schema::table('investment_roi_logs', function (Blueprint $table) {
            $table->decimal('amount', 15, 4)->change();
        });
    }

    public function down(): void
    {
        Schema::table('investment_packages', function (Blueprint $table) {
            $table->decimal('daily_roi', 15, 2)->change();
        });

        Schema::table('user_investments', function (Blueprint $table) {
            $table->decimal('daily_roi', 15, 2)->change();
        });

        Schema::table('investment_roi_logs', function (Blueprint $table) {
            $table->decimal('amount', 15, 2)->change();
        });
    }
};
