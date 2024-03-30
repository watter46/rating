<?php declare(strict_types=1);

namespace App\UseCases\Admin\Fixture;

use Exception;
use Illuminate\Support\Facades\DB;

use App\Models\Fixture;
use App\UseCases\Admin\ApiFootballRepositoryInterface;
use App\UseCases\Admin\Fixture\FixturesData\Fixtures;


final readonly class RegisterFixturesUseCase
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
            
            DB::transaction(function () use ($data) {
                $unique = ['id'];
                $updateColumns = ['date', 'status', 'score'];

                Fixture::upsert($data->get('formatted'), $unique, $updateColumns);
            });

            $this->fixtures->registered($data->get('original'));

        } catch (Exception $e) {
            throw $e;
        }
    }
}