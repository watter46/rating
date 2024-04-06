<?php declare(strict_types=1);

namespace App\UseCases\User\Fixture;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Models\Fixture;
use App\UseCases\User\PlayerInFixture;


final readonly class FetchFixtureUseCase
{
    public function __construct()
    {
        //
    }
    
    public function execute(string $fixtureId): Fixture
    {
        try {            
            $fixture = PlayerInFixture::playedPlayersInFixture(
                Fixture::query()
                    ->currentSeason()
                    ->inSeasonTournament()
                    ->finished()
                    ->findOrFail($fixtureId)
            )->fetch();
            
            return $fixture;

        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException('Fixture Not Found.');
                                    
        } catch (Exception $e) {
            throw $e;
        }
    }
}