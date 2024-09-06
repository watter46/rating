<?php declare(strict_types=1);

namespace App\UseCases\User\Domain\Fixture;

use App\UseCases\Util\IdList;


class PlayerIds extends IdList
{
    public static function create(): static
    {
        return new static;
    }
}