<?php declare(strict_types=1);

namespace App\UseCases\Player;

use Exception;

use App\Models\Player;


final readonly class FetchPlayerUseCase
{
    public function execute(string $fixtureId, string $playerId): ?Player
    {
        try {
            return Player::query()
                ->where('fixture_id', $fixtureId)
                ->where('player_info_id', $playerId)
                ->first();

        } catch (Exception $e) {
            throw $e;
        }
    }
}