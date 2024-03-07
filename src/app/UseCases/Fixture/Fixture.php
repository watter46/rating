<?php declare(strict_types=1);

namespace App\UseCases\Fixture;

use App\Events\FixtureRegistered;
use Illuminate\Support\Collection;
use App\UseCases\Fixture\FixtureDataProcessor;


readonly class Fixture
{
    public function registered(Collection $fixtureData)
    {
        $validate = FixtureDataProcessor::validate($fixtureData);
        
        if (!$validate->shouldRegister()) {
            return;
        }
        
        FixtureRegistered::dispatch($validate);
    }
}