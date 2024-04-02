<?php declare(strict_types=1);

namespace Database\Stubs\Fixture;

use Exception;

use App\Models\Fixture;
use App\UseCases\Admin\ApiFootballRepositoryInterface;
use Database\Stubs\Infrastructure\ApiFootball\MockApiFootballRepository;


class StubRegisterFixturesUseCase
{
    public function __construct(
        private ApiFootballRepositoryInterface $apiFootballRepository)
    {
        //
    }

    public function execute(): void
    {
        try {
            /** @var MockApiFootballRepository $repository */
            $repository = app(MockApiFootballRepository::class);

            $data = $repository->fetchFixtures();
            
            $unique = ['id'];
            $updateColumns = ['date', 'status', 'score'];

            Fixture::upsert($data->build()->toArray(), $unique, $updateColumns);

        } catch (Exception $e) {
            throw $e;
        }
    }
}