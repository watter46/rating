<?php declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use Database\Mocks\Player\MockRegisterPlayerOfTeamUseCase;


class PlayerInfoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /** @var MockRegisterPlayerOfTeamUseCase $registerPlayerOfTeam */
        $registerPlayerOfTeam = app(MockRegisterPlayerOfTeamUseCase::class);

        $registerPlayerOfTeam->execute();
    }
}
