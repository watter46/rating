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
        Schema::create('api_players', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->tinyText('name');
            $table->unsignedSmallInteger('season')->length(4);
            $table->unsignedTinyInteger('number')->nullable();
            $table->unsignedMediumInteger('foot_player_id');
            $table->unsignedMediumInteger('sofa_player_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_players');
    }
};
