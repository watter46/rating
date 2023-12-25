<?php declare(strict_types=1);

namespace App\UseCases\Player;

use App\Http\Controllers\Util\FixtureFile;
use App\Models\Fixture;
use App\Models\Lineup;
use App\UseCases\Player\Builder\FixtureDataBuilder;
use App\UseCases\Player\Util\ApiFootballFetcher;
use Exception;
use Illuminate\Support\Facades\DB;

final readonly class RegisterFixtureUseCase
{
    public function __construct(
        private FixtureFile $file,
        private FixtureDataBuilder $builder,
        private Fixture $fixture)
    {
        //
    }

    public function execute(int $fixtureId)
    {
        try {
            $model = Fixture::query()
                ->where('external_fixture_id', $fixtureId)
                ->first();
            
            $fetched = ApiFootballFetcher::fixture($fixtureId)->fetch();

            // $this->file->write($fixtureId, json_encode($fetched));
            
            // $fetched = $this->file->get($fixtureId);
            
            $data = $this->builder->build($fetched[0]);

            $fixture = $model->updateFixture($data);

            DB::transaction(function () use ($fixture) {
                $fixture->save();
            });
 
        } catch (Exception $e) {
            throw $e;
        }
    }
}