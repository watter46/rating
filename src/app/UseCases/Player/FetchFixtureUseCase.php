<?php declare(strict_types=1);

namespace App\UseCases\Player;

use Exception;

use App\Models\Fixture;
use App\Http\Controllers\Util\LeagueImageFile;
use App\Http\Controllers\Util\TeamImageFile;
use App\Models\ApiPlayer;
use Illuminate\Support\Str;


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
            return Fixture::query()
                ->with('players.apiPlayer')
                ->where('external_fixture_id', $fixtureId)
                ->first();
            
        } catch (Exception $e) {
            throw $e;
        }
    }
}