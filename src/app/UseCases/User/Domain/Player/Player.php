<?php declare(strict_types=1);

namespace App\UseCases\User\Domain\Player;


readonly class Player
{
    private const RATE_LIMIT = 3;
    public const RATE_LIMIT_EXCEEDED_MESSAGE = 'Rate limit exceeded.';

    public function __construct()
    {
        
    }

    public function rate(Fixture $fixture)
    {
        if ($this->rateCount > self::RATE_LIMIT) {
            return new Exception(self::RATE_LIMIT_EXCEEDED_MESSAGE);
        }
    }

    public function decideMom(Fixture $fixture)
    {
        if ($fixture->exceeded()) return;

        $player->decideMom();

        
    }
}