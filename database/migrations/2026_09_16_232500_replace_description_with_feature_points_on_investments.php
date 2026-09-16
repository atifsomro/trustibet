<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('investment_packages', function (Blueprint $table) {
            $table->json('feature_points')->nullable()->after('total_days');
            $table->boolean('is_recommended')->default(false)->after('is_active');
        });

        Schema::table('user_investments', function (Blueprint $table) {
            $table->json('feature_points')->nullable()->after('total_days');
        });

        $this->migrateDescriptionToFeaturePoints('investment_packages');
        $this->migrateDescriptionToFeaturePoints('user_investments');

        Schema::table('investment_packages', function (Blueprint $table) {
            $table->dropColumn('description');
        });

        Schema::table('user_investments', function (Blueprint $table) {
            $table->dropColumn('description');
        });
    }

    public function down(): void
    {
        Schema::table('investment_packages', function (Blueprint $table) {
            $table->text('description')->nullable()->after('total_days');
        });

        Schema::table('user_investments', function (Blueprint $table) {
            $table->text('description')->nullable()->after('total_days');
        });

        $this->migrateFeaturePointsToDescription('investment_packages');
        $this->migrateFeaturePointsToDescription('user_investments');

        Schema::table('investment_packages', function (Blueprint $table) {
            $table->dropColumn(['feature_points', 'is_recommended']);
        });

        Schema::table('user_investments', function (Blueprint $table) {
            $table->dropColumn('feature_points');
        });
    }

    protected function migrateDescriptionToFeaturePoints(string $table): void
    {
        DB::table($table)->orderBy('id')->get()->each(function ($row) use ($table) {
            $points = [];

            if (! empty($row->description)) {
                $points = array_values(array_filter(array_map(
                    'trim',
                    preg_split('/\r\n|\r|\n|,/', (string) $row->description) ?: []
                ), fn (string $line) => $line !== ''));
            }

            DB::table($table)->where('id', $row->id)->update([
                'feature_points' => json_encode($points),
            ]);
        });
    }

    protected function migrateFeaturePointsToDescription(string $table): void
    {
        DB::table($table)->orderBy('id')->get()->each(function ($row) use ($table) {
            $points = json_decode((string) ($row->feature_points ?? '[]'), true);
            $description = is_array($points) ? implode("\n", $points) : null;

            DB::table($table)->where('id', $row->id)->update([
                'description' => $description ?: null,
            ]);
        });
    }
};
