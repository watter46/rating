<?php declare(strict_types=1);

namespace App\UseCases\User;

use App\Models\Fixture;
use App\Models\PlayerInfo;
use Exception;

readonly class PlayerInFixture
{
    private function __construct(
        private Fixture $fixture)
    {
        
    }
    
    // 選手が出場しているか
    public function isPlayed()
    {

    }
    
    // Rateできるか
    public function canRate()
    {

    }

    public function fetch(): Fixture
    {
        return $this->fixture;
    }

    public static function playedPlayersInFixture(Fixture $fixture): self
    {
        try {
            $playedIds = $fixture->toFixtureData()->getPlayerIds();
        
            $playerInfos = PlayerInfo::query()
                ->currentSeason()
                ->whereIn('foot_player_id', $playedIds->toArray())
                ->get();

            if ($playerInfos->count() !== $playedIds->count()) {
                throw new Exception('PlayerInfo Not Found.');
            }

            $fixture->playerInfos = $playerInfos;

            return new self($fixture);
            
        } catch (Exception $e) {
            throw $e;
        }
    }
}