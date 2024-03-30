<?php declare(strict_types=1);

namespace App\UseCases\Admin\Fixture\FixturesData;

use Illuminate\Support\Collection;

use App\Events\FixturesRegistered;
use App\UseCases\Admin\Fixture\FixturesData\FixturesDataProcessor;


readonly class Fixtures
{
    public function registered(Collection $fixturesData)
    {
        $validate = FixturesDataProcessor::validate($fixturesData);
        
        if (!$validate->shouldRegister()) {
            return;
        }
        
        FixturesRegistered::dispatch($validate);
    }
}