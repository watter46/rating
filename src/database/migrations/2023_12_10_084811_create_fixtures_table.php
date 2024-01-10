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
        Schema::create('fixtures', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->unsignedMediumInteger('external_fixture_id');
            $table->unsignedMediumInteger('external_league_id');
            $table->unsignedSmallInteger('season')->length(4);
            $table->boolean('is_end');
            $table->timestamp('date');
            $table->json('score');
            $table->json('fixture')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fixtures');
    }
};
