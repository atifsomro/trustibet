<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lottery_draws', function (Blueprint $table) {

            $table->id();


            /*
            |--------------------------------------------------------------------------
            | Lottery
            |--------------------------------------------------------------------------
            */

            $table->foreignId('lottery_id');


            /*
            |--------------------------------------------------------------------------
            | Draw Status
            |--------------------------------------------------------------------------
            |
            | pending   = draw record created but not executed
            | running   = winner selection is in progress
            | completed = winners successfully selected and paid
            | failed    = draw failed
            |
            */

            $table->string('status', 30)
                ->default('pending');


            /*
            |--------------------------------------------------------------------------
            | Draw Information
            |--------------------------------------------------------------------------
            */

            $table->unsignedInteger('total_tickets')
                ->default(0);

            $table->unsignedInteger('total_winners')
                ->default(0);


            /*
            |--------------------------------------------------------------------------
            | Admin
            |--------------------------------------------------------------------------
            |
            | Admin user ID.
            |
            | We don't add a foreign key here because your admin guard/table
            | structure may be different from the normal users table.
            |
            */

            $table->unsignedBigInteger('drawn_by')
                ->nullable();


            /*
            |--------------------------------------------------------------------------
            | Dates
            |--------------------------------------------------------------------------
            */

            $table->timestamp('started_at')
                ->nullable();

            $table->timestamp('completed_at')
                ->nullable();


            /*
            |--------------------------------------------------------------------------
            | Error / Audit
            |--------------------------------------------------------------------------
            */

            $table->text('error_message')
                ->nullable();


            $table->timestamps();


            /*
            |--------------------------------------------------------------------------
            | Constraints / Indexes
            |--------------------------------------------------------------------------
            |
            | A lottery can only have one official draw.
            |
            */

            $table->unique('lottery_id');

            $table->index('status');

            $table->index('drawn_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lottery_draws');
    }
};