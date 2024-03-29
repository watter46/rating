<?php declare(strict_types=1);

namespace App\UseCases\Admin\Fixture;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Models\Fixture as EqFixture;
use App\UseCases\Api\ApiFootball\FixtureFetcher;
use App\UseCases\Admin\Fixture\FixtureData\Fixture;
use App\UseCases\Util\FixtureData;


final readonly class RegisterFixtureUseCase
{
    public function __construct(
        private FixtureFetcher $fetcher,
        private FixtureData $fixtureData,
        private Fixture $fixture)
    {
        //
    }

    public function execute(string $fixtureId): EqFixture
    {
        try {
            /** @var EqFixture $model */
            $model = EqFixture::findOrFail($fixtureId);

            $fixtureData = $this->fetcher->fetch($model->external_fixture_id);

            $data = $this->fixtureData->build($fixtureData);
                        
            $fixture = $model->updateFixture($data);
            
            DB::transaction(function () use ($fixture) {
                $fixture->save();
            });
            
            $this->fixture->registered($fixtureData);

            return $fixture;

        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException('Fixture Not Exists.');
 
        } catch (Exception $e) {
            throw $e;
        }
    }
}