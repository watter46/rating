<?php declare(strict_types=1);

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
        Schema::create('player_infos', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->tinyText('name');
            $table->unsignedSmallInteger('season')->length(4);
            $table->unsignedTinyInteger('number')->nullable();
            $table->unsignedMediumInteger('api_football_id');
            $table->tinyText('flash_live_sports_id')->nullable();
            $table->tinyText('flash_live_sports_image_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('player_infos');
    }
};
