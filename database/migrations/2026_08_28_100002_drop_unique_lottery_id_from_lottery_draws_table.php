<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Multiple rounds per lottery are already a first-class concept.
     * The original unique(lottery_id) blocks a second draw record.
     */
    public function up(): void
    {
        $indexes = collect(Schema::getIndexes('lottery_draws'));

        $unique = $indexes->first(function (array $index) {
            return ($index['unique'] ?? false)
                && ($index['primary'] ?? false) === false
                && ($index['columns'] ?? []) === ['lottery_id'];
        });

        if (!$unique) {
            return;
        }

        Schema::table('lottery_draws', function (Blueprint $table) use ($unique) {
            $table->dropUnique($unique['name']);
        });
    }

    public function down(): void
    {
        Schema::table('lottery_draws', function (Blueprint $table) {
            $table->unique('lottery_id');
        });
    }
};
