<?php declare(strict_types=1);

namespace App\UseCases\Admin\Fixture;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Models\Fixture;
use App\UseCases\Api\ApiFootball\FixtureFetcher;
use App\UseCases\Util\FixtureData;


final readonly class RegisterFixtureUseCase
{
    public function __construct(
        private FixtureFetcher $fetcher,
        private FixtureData $fixtureData)
    {
        //
    }

    public function execute(string $fixtureId)
    {
        try {
            /** @var Fixture $model */
            $model = Fixture::findOrFail($fixtureId);

            $fixtureData = $this->fetcher->fetchOrGetFile($model->external_fixture_id);
            
            $data = $this->fixtureData->build($fixtureData);
                        
            $fixture = $model->updateFixture($data);
            
            DB::transaction(function () use ($fixture) {
                $fixture->save();
            });
            
            $fixture->registered();

        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException('Fixture Not Exists.');
 
        } catch (Exception $e) {
            throw $e;
        }
    }
}