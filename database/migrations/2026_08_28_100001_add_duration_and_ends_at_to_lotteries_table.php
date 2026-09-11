<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lotteries', function (Blueprint $table) {
            $table->unsignedInteger('duration_seconds')->nullable()->after('draw_at');
            $table->dateTime('starts_at')->nullable()->after('duration_seconds');
            $table->dateTime('ends_at')->nullable()->after('starts_at');

            $table->index('ends_at');
            $table->index('starts_at');
        });

        $lotteries = DB::table('lotteries')->select([
            'id',
            'sales_start_at',
            'sales_end_at',
            'draw_at',
            'created_at',
            'is_active',
            'status',
        ])->get();

        foreach ($lotteries as $lottery) {
            $endsAt = $lottery->sales_end_at ?: $lottery->draw_at;
            $startsAt = $lottery->sales_start_at ?: $lottery->created_at;

            if (!$endsAt) {
                Log::warning('Lottery #'.$lottery->id.' could not be backfilled with ends_at; needs manual admin review.');

                continue;
            }

            $start = \Illuminate\Support\Carbon::parse($startsAt);
            $end = \Illuminate\Support\Carbon::parse($endsAt);

            $durationSeconds = $end->greaterThan($start)
                ? $start->diffInSeconds($end)
                : null;

            if ($durationSeconds === null || $durationSeconds < 1) {
                Log::warning('Lottery #'.$lottery->id.' has an invalid sales window; ends_at left null for manual admin review.');

                continue;
            }

            DB::table('lotteries')->where('id', $lottery->id)->update([
                'duration_seconds' => $durationSeconds,
                'starts_at' => $start,
                'ends_at' => $end,
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('lotteries', function (Blueprint $table) {
            $table->dropIndex(['ends_at']);
            $table->dropIndex(['starts_at']);
            $table->dropColumn([
                'duration_seconds',
                'starts_at',
                'ends_at',
            ]);
        });
    }
};
