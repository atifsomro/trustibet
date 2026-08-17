<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lottery_tickets', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Lottery
            |--------------------------------------------------------------------------
            */

            $table->foreignId('lottery_id');


            /*
            |--------------------------------------------------------------------------
            | User
            |--------------------------------------------------------------------------
            */

            $table->foreignId('user_id');


            /*
            |--------------------------------------------------------------------------
            | Ticket
            |--------------------------------------------------------------------------
            */

            $table->string('ticket_number', 50)
                ->unique();

            /*
             * Store the price at the time of purchase.
             *
             * This is important because the lottery itself should never
             * be changed after tickets have been sold.
             */
            $table->decimal('price', 12, 2);


            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            |
            | active    = available for draw
            | winner    = selected as a winning ticket
            | refunded  = ticket amount returned to user
            | cancelled = lottery/ticket cancelled
            |
            */

            $table->string('status', 30)
                ->default('active');


            /*
            |--------------------------------------------------------------------------
            | Wallet Transactions
            |--------------------------------------------------------------------------
            |
            | We keep references to wallet transactions for auditing.
            | We will connect these to your existing Wallet system in the
            | ticket purchase/refund implementation.
            |
            */

            $table->unsignedBigInteger('purchase_transaction_id')
                ->nullable();

            $table->unsignedBigInteger('refund_transaction_id')
                ->nullable();


            /*
            |--------------------------------------------------------------------------
            | Dates
            |--------------------------------------------------------------------------
            */

            $table->timestamp('purchased_at')
                ->nullable();

            $table->timestamp('refunded_at')
                ->nullable();

            $table->timestamps();


            /*
            |--------------------------------------------------------------------------
            | Indexes
            |--------------------------------------------------------------------------
            */

            $table->index([
                'lottery_id',
                'status',
            ]);

            $table->index([
                'user_id',
                'lottery_id',
            ]);

            $table->index([
                'lottery_id',
                'user_id',
                'status',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lottery_tickets');
    }
};