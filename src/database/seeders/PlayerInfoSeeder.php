<?php declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use Database\Stubs\Player\StubRegisterPlayerOfTeamUseCase;
use Database\Stubs\Player\StubRegisterPlayerUseCase;


class PlayerInfoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /** @var StubRegisterPlayerOfTeamUseCase $registerPlayerOfTeam */
        $registerPlayerOfTeam = app(StubRegisterPlayerOfTeamUseCase::class);

        $registerPlayerOfTeam->execute();

        /** @var StubRegisterPlayerUseCase $registerPlayer */
        $registerPlayer = app(StubRegisterPlayerUseCase::class);

        $registerPlayer->execute();
    }
}
