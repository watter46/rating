<?php declare(strict_types=1);

namespace App\UseCases\Fixture;

use Exception;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Util\FixtureFile;
use App\Models\Fixture;
use App\UseCases\Player\Builder\FixtureDataBuilder;
use App\UseCases\Player\Util\ApiFootballFetcher;
use Illuminate\Database\Eloquent\ModelNotFoundException;

final readonly class RegisterFixtureUseCase
{
    public function __construct(
        private FixtureFile $file,
        private FixtureDataBuilder $builder,
        private Fixture $fixture)
    {
        //
    }

    public function execute(string $fixtureId)
    {
        try {
            /** @var Fixture $fixture */
            $model = $this->fixture->findOrFail($fixtureId);

            $fetched = ApiFootballFetcher::fixture($model->external_fixture_id)->fetch();

            // $fetched = $this->file->get($model->external_fixture_id);
            
            // $this->file->write($model->external_fixture_id, json_encode($fetched));
                        
            $data = $this->builder->build($fetched[0]);
            
            $fixture = $model->updateFixture($data);

            DB::transaction(function () use ($fixture) {
                $fixture->save();
            });

        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException('Fixture Not Exists.');
 
        } catch (Exception $e) {
            throw $e;
        }
    }
}