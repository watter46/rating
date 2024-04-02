<?php declare(strict_types=1);

namespace App\UseCases\Admin\Fixture;

use Exception;
use Illuminate\Support\Facades\DB;

use App\Models\Fixture;
use App\UseCases\Admin\ApiFootballRepositoryInterface;


final readonly class RegisterFixturesUseCase
{    
    public function __construct(
        private Fixture $fixture,
        private ApiFootballRepositoryInterface $apiFootballRepository)
    {
        //
    }

    public function execute(): void
    {
        try {
            $fixturesData = $this->apiFootballRepository->fetchFixtures();

            DB::transaction(function () use ($fixturesData) {
                $unique = ['id'];
                $updateColumns = ['date', 'status', 'score'];

                Fixture::upsert($fixturesData->build()->toArray(), $unique, $updateColumns);
            });

            $this->fixture->fixturesRegistered($fixturesData);

        } catch (Exception $e) {
            throw $e;
        }
    }
}