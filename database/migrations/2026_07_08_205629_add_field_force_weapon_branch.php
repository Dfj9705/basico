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
        Schema::table('weapon_branches', function (Blueprint $table) {
            $table->foreignId('force_id')->reference('id')->on('forces')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('weapon_branches', function (Blueprint $table) {
            $table->dropForeign(['force_id']);
            $table->dropColumn('force_id');
        });
    }
};
