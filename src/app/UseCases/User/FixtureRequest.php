<?php declare(strict_types=1);

namespace App\UseCases\User;

use App\Models\Fixture;


class FixtureRequest
{
    public function __construct(
        private ?string $fixtureInfoId,
        private ?string $playerInfoId
    ) {
        //  
    }
    
    public static function make(
        string $fixtureInfoId,
        ?string $playerInfoId = null): self
    {
        return new self(
                fixtureInfoId: $fixtureInfoId,
                playerInfoId: $playerInfoId
            );
    }

    public function getPlayerInfoId(): string
    {
        return $this->playerInfoId;
    }

    public function buildFixture(): FixtureBuilder
    {
        $fixture = Fixture::query()
            ->fixtureInfoId($this->fixtureInfoId)
            ->firstOrNew(['fixture_info_id' => $this->fixtureInfoId]);
                    
        return new FixtureBuilder($fixture);
    }
}