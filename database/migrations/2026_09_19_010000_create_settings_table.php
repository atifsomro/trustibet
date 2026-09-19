<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            // Logical grouping for the admin UI, e.g. "general", "social", "seo".
            $table->string('group')->default('general');
            // Unique machine key, e.g. "site_facebook". Used as the array key
            // under config('settings') and as the lookup key in Setting::get().
            $table->string('key')->unique();
            // Human friendly label shown on the admin form.
            $table->string('label')->nullable();
            // Free-form value. Kept as text so any field type below can share
            // a single column without extra migrations per field.
            $table->text('value')->nullable();
            // Rendering hint for the admin form: text, textarea, url, email,
            // image, boolean. Purely presentational — storage stays generic.
            $table->string('type')->default('text');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['group', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
