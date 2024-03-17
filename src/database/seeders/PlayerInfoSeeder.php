<?php declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use Database\Stubs\Player\StubUpdatePlayerInfosUseCase;
use Database\Stubs\Player\StubRegisterPlayerUseCase;


class PlayerInfoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /** @var StubUpdatePlayerInfosUseCase $updatePlayerInfos */
        $updatePlayerInfos = app(StubUpdatePlayerInfosUseCase::class);

        $updatePlayerInfos->execute();

        /** @var StubRegisterPlayerUseCase $registerPlayer */
        $registerPlayer = app(StubRegisterPlayerUseCase::class);

        $registerPlayer->execute();
    }
}
