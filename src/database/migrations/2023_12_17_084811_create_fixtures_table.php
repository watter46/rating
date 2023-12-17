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
            $table->ulid('id');
            $table->unsignedMediumInteger('external_fixture_id');
            $table->unsignedMediumInteger('external_team_id');
            $table->tinyText('team_name');
            $table->unsignedMediumInteger('external_league_id');
            $table->tinyText('league_name');
            $table->unsignedSmallInteger('season')->length(4);
            $table->boolean('is_end');
            $table->boolean('is_home');
            $table->tinyInteger('home')->nullable();
            $table->tinyInteger('away')->nullable();
            $table->timestamp('first_half_at')->nullable();
            $table->timestamp('second_half_at')->nullable();
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
