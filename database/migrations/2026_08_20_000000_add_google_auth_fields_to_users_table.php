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
        Schema::table('users', function (Blueprint $table) {
            // Google account identifier
            $table->string('google_id')->nullable()->unique()->after('id');

            // Which provider the account originated from (null = local/email+password)
            $table->string('provider')->nullable()->after('google_id');

            // Profile picture returned by Google (or uploaded manually later)
            $table->string('avatar')->nullable()->after('provider');
        });

        // Google-only accounts won't have a real password, phone, or country
        // at the time of creation, so these must be nullable.
        Schema::table('users', function (Blueprint $table) {
            $table->string('password')->nullable()->change();
            $table->string('phone')->nullable()->change();
            $table->unsignedBigInteger('country_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['google_id', 'provider', 'avatar']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('password')->nullable(false)->change();
            $table->string('phone')->nullable(false)->change();
            $table->unsignedBigInteger('country_id')->nullable(false)->change();
        });
    }
};