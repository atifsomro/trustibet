<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void {

    Schema::create('kycs', function (Blueprint $table) {
        $table->id();

        $table->bigInteger('user_id');
        $table->string('full_name');
        $table->date('date_of_birth');
        $table->string('country');
        $table->string('id_type');
        $table->string('id_number');

        $table->string('address');
        $table->string('city');

        $table->text('front_image');
        $table->text('back_image');
        $table->text('selfie_image');

        $table->enum('status', [
            'pending',
            'approved',
            'rejected'
        ])->default('pending');

        $table->text('rejection_reason')->nullable();
        $table->timestamp('verified_at')->nullable();

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kycs');
    }
};