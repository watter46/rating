<?php declare(strict_types=1);

namespace App\UseCases\Admin\Fixture;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Models\Fixture;
use App\UseCases\Admin\ApiFootballRepositoryInterface;


final readonly class RegisterFixtureUseCase
{
    public function __construct(private ApiFootballRepositoryInterface $apiFootballRepository)
    {
        //
    }

    public function execute(string $fixtureId): Fixture
    {
        try {
            /** @var Fixture $fixture */
            $fixture = Fixture::findOrFail($fixtureId);

            $data = $this->apiFootballRepository->fetchFixture($fixture->external_fixture_id);
            
            $fixture->updateFixture($data);
            
            DB::transaction(function () use ($fixture) {
                $fixture->save();
            });
            
            $fixture->fixtureRegistered($data);

            return $fixture;

        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException('Fixture Not Exists.');
 
        } catch (Exception $e) {
            throw $e;
        }
    }
}