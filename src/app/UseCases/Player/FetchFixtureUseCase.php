<?php declare(strict_types=1);

namespace App\UseCases\Player;

use Exception;
use Illuminate\Support\Collection;

use App\Http\Controllers\Util\LeagueImageFile;
use App\Http\Controllers\Util\TeamImageFile;
use App\Models\Fixture;


final readonly class FetchFixtureUseCase
{
    public function __construct(
        private Fixture $fixture,
        private TeamImageFile $teamImage,
        private LeagueImageFile $leagueImage)
    {
        
    }
    
    public function execute(int $fixtureId): Collection
    {
        try {
            $fixture = Fixture::query()
                ->where('external_fixture_id', $fixtureId)
                ->first();

            return $fixture->fixture;

        } catch (Exception $e) {
            throw $e;
        }
    }
}