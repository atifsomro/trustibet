<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_investments', function (Blueprint $table) {
            $table->timestamp('daily_roi_updated_at')->nullable()->after('daily_roi');
        });
    }

    public function down(): void
    {
        Schema::table('user_investments', function (Blueprint $table) {
            $table->dropColumn('daily_roi_updated_at');
        });
    }
};
