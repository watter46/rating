<?php declare(strict_types=1);

namespace App\UseCases\Fixture;

use Exception;
use Illuminate\Support\Str;

use App\Models\Fixture;
use App\Models\PlayerInfo;


final readonly class FetchFixtureUseCase
{
    public function __construct()
    {
        //
    }
    
    public function execute(string $fixtureId): Fixture
    {
        try {
            $fixture = Fixture::find($fixtureId);
            
            $idList = collect($fixture->fixture['lineups'])
                ->dot()
                ->filter(function ($player, $key) {
                    return Str::afterLast($key, '.') === 'id';
                })
                ->values()
                ->toArray();
            
            $playerInfos = PlayerInfo::query()
                ->currentSeason()
                ->whereIn('foot_player_id', $idList)
                ->get();

            $fixture['playerInfos'] = $playerInfos;
            
            return $fixture;
            
        } catch (Exception $e) {
            throw $e;
        }
    }
}