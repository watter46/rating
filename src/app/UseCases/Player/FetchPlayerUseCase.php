<?php declare(strict_types=1);

namespace App\UseCases\Player;

use Exception;

use App\Models\Player;
use App\Models\Fixture;


final readonly class FetchPlayerUseCase
{
    public function __construct(private Fixture $fixture, private Player $player)
    {
        //
    }
    
    public function execute(string $fixtureId, string $playerId): Player
    {
        try {
            /** @var Player $player */
            $player = Player::query()
                ->where('fixture_id', $fixtureId)
                ->where('player_info_id', $playerId)
                ->first();
            
            return $player
                ? $player->evaluated()
                : $this->player->unevaluated($fixtureId);
                        
        } catch (Exception $e) {
            throw $e;
        }
    }
}