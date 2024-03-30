<?php declare(strict_types=1);

namespace Database\Stubs\Fixture;

use App\Models\Fixture;
use App\UseCases\Admin\ApiFootballRepositoryInterface;
use App\UseCases\Admin\Fixture\FixturesData\Fixtures;
use App\UseCases\Admin\Fixture\FixturesDataBuilder;
use App\UseCases\Api\ApiFootball\FixturesFetcher;
use Exception;
use Illuminate\Database\Eloquent\Collection;


class StubRegisterFixturesUseCase
{
    public function __construct(
        private Fixtures $fixtures,
        private ApiFootballRepositoryInterface $apiFootballRepository)
    {
        //
    }

    public function execute(): void
    {
        try {
            $data = $this->apiFootballRepository->fetchFixtures();
            
            $unique = ['id'];
            $updateColumns = ['date', 'status', 'score'];

            Fixture::upsert($data->get('formatted'), $unique, $updateColumns);

        } catch (Exception $e) {
            throw $e;
        }
    }
}