<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->json('notification_preferences')->nullable()->after('account_status');
            $table->timestamp('last_login_at')->nullable()->after('notification_preferences');
            $table->string('last_login_user_agent', 512)->nullable()->after('last_login_at');
            $table->timestamp('password_set_at')->nullable()->after('password');
        });

        DB::table('users')
            ->where(function ($query) {
                $query->whereNull('provider')->orWhere('provider', '');
            })
            ->update(['password_set_at' => DB::raw('created_at')]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'notification_preferences',
                'last_login_at',
                'last_login_user_agent',
                'password_set_at',
            ]);
        });
    }
};
