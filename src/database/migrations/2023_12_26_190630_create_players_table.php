<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('players', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->unsignedFloat('rating', 3, 1)->nullable();
            $table->boolean('mom')->default(false);
            $table->unsignedTinyInteger('rate_count');

            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignUlid('fixture_id')->constrained()->onDelete('cascade');
            $table->foreignUlid('player_info_id')->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('players');
    }
};
