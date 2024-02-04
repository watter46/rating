<?php declare(strict_types=1);

namespace Database\Seeders;

use App\Http\Controllers\Util\PlayerFile;
use Illuminate\Database\Seeder;

use Database\Mocks\Player\MockRegisterPlayerOfTeamUseCase;
use Database\Mocks\Player\MockRegisterPlayerUseCase;

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

        /** @var MockRegisterPlayerUseCase $registerPlayer */
        $registerPlayer = app(MockRegisterPlayerUseCase::class);

        $registerPlayer->execute();
    }
}
