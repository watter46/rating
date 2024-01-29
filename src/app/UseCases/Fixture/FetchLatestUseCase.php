<?php declare(strict_types=1);

namespace App\UseCases\Fixture;

use Exception;
use Illuminate\Support\Str;

use App\Models\Fixture;
use App\Models\PlayerInfo;
use App\UseCases\Player\DecideManOfTheMatchUseCase;


final readonly class FetchLatestUseCase
{
    public function __construct(private DecideManOfTheMatchUseCase $decideManOfTheMatch)
    {
        //
    }

    public function execute()
    {
        try {
            /** @var Fixture $fixture */  
            $fixture = Fixture::query()
                ->past()
                ->latest()
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