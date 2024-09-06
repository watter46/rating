<?php declare(strict_types=1);

namespace App\UseCases\User\Domain\FixtureInfo;

use App\UseCases\User\Domain\FixtureInfoId;

class FixtureInfo
{
    public function __construct(
        private FixtureInfoId $fixtureInfoId
    ) {
        
    }
}