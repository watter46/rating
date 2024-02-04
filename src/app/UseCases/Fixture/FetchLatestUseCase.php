<?php declare(strict_types=1);

namespace App\UseCases\Fixture;

use Exception;

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
            
            /** @var PlayerInfo $playerInfos */  
            $playerInfos = PlayerInfo::query()
                ->currentSeason()
                ->lineups($fixture)
                ->get();

            $fixture['playerInfos'] = $playerInfos;
            
            return $fixture;

        } catch (Exception $e) {
            throw $e;
        }
    }
}