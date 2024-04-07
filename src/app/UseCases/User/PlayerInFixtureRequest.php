<?php declare(strict_types=1);

namespace App\UseCases\User;


readonly class PlayerInFixtureRequest
{
    public function __construct(
        private ?string $fixtureId,
        private ?string $playerInfoId
    ) {
        //  
    }
    
    public static function make(string $fixtureId, ?string $playerInfoId = null): self
    {
        return new self(
                fixtureId: $fixtureId,
                playerInfoId: $playerInfoId
            );
    }

    public function getFixtureId(): string
    {
        return $this->fixtureId;
    }

    public function getPlayerInfoId(): string
    {
        return $this->playerInfoId;
    }
}