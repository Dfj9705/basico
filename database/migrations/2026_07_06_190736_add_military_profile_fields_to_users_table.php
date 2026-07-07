<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('grade_id')
                ->nullable()
                ->constrained('grades')
                ->nullOnDelete();

            $table->foreignId('weapon_branch_id')
                ->nullable()
                ->constrained('weapon_branches')
                ->nullOnDelete();

            $table->string('catalog_number')->nullable()->unique();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['grade_id']);
            $table->dropForeign(['weapon_branch_id']);
            $table->dropColumn(['grade_id', 'weapon_branch_id', 'catalog_number']);
        });
    }
};
