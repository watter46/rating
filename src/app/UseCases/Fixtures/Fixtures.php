<?php declare(strict_types=1);

namespace App\UseCases\Fixtures;

use Illuminate\Support\Collection;

use App\Events\FixturesRegistered;
use App\Http\Controllers\Util\TeamImageFile;
use App\UseCases\Fixtures\FixturesDataProcessor;


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