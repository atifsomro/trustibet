<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            $table = DB::getTablePrefix() . 'bonuses';
            DB::statement("ALTER TABLE `{$table}` MODIFY COLUMN `type` ENUM('welcome', 'referral') NOT NULL");
        }
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            $table = DB::getTablePrefix() . 'bonuses';
            DB::statement("ALTER TABLE `{$table}` MODIFY COLUMN `type` ENUM('welcome') NOT NULL");
        }
    }
};
