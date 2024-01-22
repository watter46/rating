<?php declare(strict_types=1);

namespace App\UseCases\Fixture;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Models\Fixture;
use App\Events\FixtureRegistered;
use App\Http\Controllers\Util\FixtureFile;
use App\UseCases\Fixture\Builder\FixtureDataBuilder;
use App\UseCases\Player\Util\ApiFootballFetcher;


final readonly class RegisterFixtureUseCase
{
    public function __construct(
        private FixtureFile $file,
        private FixtureDataBuilder $builder,
        private Fixture $fixture,
        private FixtureRegistered $fixtureRegistered)
    {
        //
    }

    public function execute(string $fixtureId)
    {
        try {
            /** @var Fixture $model */
            $model = $this->fixture->findOrFail($fixtureId);

            // $fetched = ApiFootballFetcher::fixture($model->external_fixture_id)->fetch();

            $fetched = $this->file->get($model->external_fixture_id);
            
            // $this->file->write($model->external_fixture_id, json_encode(json_decode($fetched)->response[0]));
                        
            $data = $this->builder->build($fetched[0]);
            
            $fixture = $model->updateFixture($data);

            DB::transaction(function () use ($fixture) {
                $fixture->save();
            });

            // $fixture->registered();

        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException('Fixture Not Exists.');
 
        } catch (Exception $e) {
            dd($e);
            throw $e;
        }
    }
}