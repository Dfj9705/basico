<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('meal_attendances', function (Blueprint $table) {
            $table->date('week_start')->nullable()->after('user_id');
        });

        DB::table('meal_attendances')
            ->orderBy('id')
            ->get()
            ->each(function ($record) {
                DB::table('meal_attendances')
                    ->where('id', $record->id)
                    ->update([
                        'week_start' => Carbon::parse($record->date)
                            ->startOfWeek(Carbon::MONDAY)
                            ->toDateString(),
                    ]);
            });

        Schema::table('meal_attendances', function (Blueprint $table) {
            $table->date('week_start')->nullable(false)->change();
            $table->unique(['user_id', 'week_start']);
        });
    }

    public function down(): void
    {
        Schema::table('meal_attendances', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'week_start']);
            $table->dropColumn('week_start');
        });
    }
};