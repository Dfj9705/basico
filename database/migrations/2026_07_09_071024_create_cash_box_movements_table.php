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
        Schema::create('cash_box_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('force_id')->constrained('forces');
            $table->foreignId('contribution_id')->nullable()->constrained('contributions')->cascadeOnDelete();
            $table->decimal('quantity', 10, 2);
            $table->enum('type', ['ingreso', 'egreso', 'transferencia'])->default('ingreso');
            $table->text('observation')->nullable();
            $table->foreignId('user_id')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_box_movements');
    }
};
