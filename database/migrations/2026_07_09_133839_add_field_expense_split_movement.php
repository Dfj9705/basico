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
        Schema::table('cash_box_movements', function (Blueprint $table) {
            $table->foreignId('expense_split_id')->nullable()->constrained('expense_splits');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cash_box_movements', function (Blueprint $table) {
            $table->dropForeign(['expense_split_id']);
            $table->dropColumn('expense_split_id');
        });
    }
};
