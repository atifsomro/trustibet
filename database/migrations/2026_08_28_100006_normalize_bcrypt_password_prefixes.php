<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * PHP's password_get_info() only recognizes $2y$ as bcrypt.
     * Legacy $2a$ / $2b$ hashes are the same algorithm, but Laravel 11+
     * throws "This password does not use the Bcrypt algorithm" on login
     * when HASH_VERIFY is enabled (the default).
     */
    public function up(): void
    {
        foreach (['users', 'admins'] as $table) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            $rows = DB::table($table)
                ->where(function ($query) {
                    $query->where('password', 'like', '$2a$%')
                        ->orWhere('password', 'like', '$2b$%');
                })
                ->get(['id', 'password']);

            foreach ($rows as $row) {
                $password = (string) $row->password;

                if (strlen($password) < 60) {
                    continue;
                }

                DB::table($table)->where('id', $row->id)->update([
                    'password' => '$2y$' . substr($password, 4),
                ]);
            }
        }
    }

    public function down(): void
    {
        // Irreversible: native PHP hashes also use the $2y$ prefix.
    }
};
