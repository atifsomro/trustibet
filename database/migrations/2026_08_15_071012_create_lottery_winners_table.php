<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lottery_winners', function (Blueprint $table) {

            $table->id();


            /*
            |--------------------------------------------------------------------------
            | Lottery / Draw
            |--------------------------------------------------------------------------
            */

            $table->foreignId('lottery_id');

            $table->foreignId('draw_id');


            /*
            |--------------------------------------------------------------------------
            | Winning Ticket
            |--------------------------------------------------------------------------
            */

            $table->foreignId('ticket_id');


            /*
            |--------------------------------------------------------------------------
            | Winner
            |--------------------------------------------------------------------------
            */

            $table->foreignId('user_id');


            /*
            |--------------------------------------------------------------------------
            | Prize
            |--------------------------------------------------------------------------
            |
            | first
            | second
            | third
            |
            */

            $table->string('prize_category', 30);


            /*
             * Position inside the prize category.
             *
             * Example:
             *
             * second prize:
             *     position 1
             *     position 2
             *
             * third prize:
             *     position 1
             *     position 2
             *     position 3
             */

            $table->integer('prize_position');


            /*
            |--------------------------------------------------------------------------
            | Prize Amount
            |--------------------------------------------------------------------------
            |
            | Store the actual amount awarded.
            |
            | Do not calculate it from the lottery later because the lottery
            | configuration should be considered historical once the draw
            | has happened.
            |
            */

            $table->decimal('prize_amount', 12, 2);


            /*
            |--------------------------------------------------------------------------
            | Payout
            |--------------------------------------------------------------------------
            */

            $table->string('payout_status', 30)
                ->default('pending');

            $table->unsignedBigInteger('payout_transaction_id')
                ->nullable();

            $table->timestamp('paid_at')
                ->nullable();


            $table->timestamps();


            /*
            |--------------------------------------------------------------------------
            | Important Constraints
            |--------------------------------------------------------------------------
            |
            | A ticket can win only ONE slot in a lottery.
            |
            */

            $table->unique([
                'lottery_id',
                'ticket_id',
            ], 'lottery_winners_ticket_unique');


            /*
            | One specific prize position can only have one winner.
            |
            | Example:
            |
            | second / position 1 → one winner
            | second / position 2 → one winner
            |
            */

            $table->unique([
                'lottery_id',
                'prize_category',
                'prize_position',
            ], 'lottery_winners_prize_slot_unique');


            /*
            |--------------------------------------------------------------------------
            | Indexes
            |--------------------------------------------------------------------------
            */

            $table->index([
                'lottery_id',
                'prize_category',
            ]);

            $table->index([
                'user_id',
                'lottery_id',
            ]);

            $table->index('payout_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lottery_winners');
    }
};