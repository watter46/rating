<?php declare(strict_types=1);

namespace App\UseCases\Fixture;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Models\Fixture;
use App\Http\Controllers\Util\FixtureFile;
use App\UseCases\Api\ApiFootball;
use App\UseCases\Api\ApiFootball\FixtureData;
use App\UseCases\Fixture\RegisterFixtureBuilder;


final readonly class RegisterFixtureUseCase
{
    public function __construct(
        private FixtureFile $file,
        private RegisterFixtureBuilder $builder,
        private FixtureData $fixtureData)
    {
        //
    }

    public function execute(string $fixtureId)
    {
        try {
            /** @var Fixture $model */
            $model = Fixture::findOrFail($fixtureId);

            $fixtureData = $this->fixtureData->fetchOrGetFile($model->external_fixture_id);
            
            $data = $this->builder->build($fixtureData);
                        
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