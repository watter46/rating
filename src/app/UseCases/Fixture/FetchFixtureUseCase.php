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
            /** @var Fixture $fixture */
            $fixture = Fixture::find($fixtureId);
            
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