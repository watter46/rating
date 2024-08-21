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
        Schema::create('fixture_infos', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->unsignedMediumInteger('api_fixture_id');
            $table->unsignedMediumInteger('api_league_id');
            $table->unsignedSmallInteger('season')->length(4);
            $table->timestamp('date');
            $table->boolean('is_end')->default(false);
            $table->json('score');
            $table->json('teams');
            $table->json('league');
            $table->json('fixture');
            $table->json('lineups')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fixture_infos');
    }
};
