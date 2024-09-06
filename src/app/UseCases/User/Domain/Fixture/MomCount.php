<?php declare(strict_types=1);

namespace App\UseCases\User\Domain\Fixture;

use App\UseCases\Util\Count;

class MomCount extends Count
{
    private const MOM_LIMIT = 5;
    public const MOM_LIMIT_EXCEEDED_MESSAGE = 'MOM limit exceeded.';

    public static function create(): static
    {
        return new static();
    }
    
    public function exceed()
    {
        return $this->count > self::MOM_LIMIT;
    }
}