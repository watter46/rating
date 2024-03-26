<?php declare(strict_types=1);

namespace App\UseCases\Admin\Fixture\FixtureData;

use App\Events\FixtureRegistered;
use Illuminate\Support\Collection;
use App\UseCases\Admin\Fixture\FixtureData\FixtureDataProcessor;


readonly class Fixture
{
    public function registered(Collection $fixtureData): void
    {
        $validate = FixtureDataProcessor::validate($fixtureData);
        
        if (!$validate->shouldRegister()) {
            return;
        }
        
        FixtureRegistered::dispatch($validate);
    }
}