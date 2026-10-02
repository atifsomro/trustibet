<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('google2fa_enabled')->default(false)->after('remember_token');
            $table->text('google2fa_secret')->nullable()->after('google2fa_enabled');
            $table->timestamp('google2fa_confirmed_at')->nullable()->after('google2fa_secret');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['google2fa_enabled', 'google2fa_secret', 'google2fa_confirmed_at']);
        });
    }
};
