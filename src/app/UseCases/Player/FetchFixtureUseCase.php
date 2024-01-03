<?php declare(strict_types=1);

namespace App\UseCases\Player;

use App\Http\Controllers\Util\FixtureFile;
use App\Http\Controllers\Util\FixturesFile;
use Exception;
use Illuminate\Support\Str;

use App\Models\Fixture;
use App\Models\PlayerInfo;
use App\Http\Controllers\Util\LeagueImageFile;
use App\Http\Controllers\Util\TeamImageFile;


final readonly class FetchFixtureUseCase
{
    public function __construct(
        private Fixture $fixture,
        private TeamImageFile $teamImage,
        private LeagueImageFile $leagueImage)
    {
        
    }
    
    public function execute(int $fixtureId): Fixture
    {
        try {
            $fixture = Fixture::query()
                // ->with('players.apiPlayer')
                ->where('external_fixture_id', $fixtureId)
                ->first();

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