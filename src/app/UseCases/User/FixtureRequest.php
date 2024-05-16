<?php declare(strict_types=1);

namespace App\UseCases\User;

use App\Models\Fixture;
use Illuminate\Support\Facades\Cache;

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
        // $fixture = Cache::get(
        //         'fixtureInfo_'.$this->fixtureInfoId,
        //         Fixture::query()
        //             ->selectWithout()
        //             ->fixtureInfoId($this->fixtureInfoId)
        //             ->firstOrNew(['fixture_info_id' => $this->fixtureInfoId])
        //     );

        // dd($fixture);

        $fixture = Fixture::query()
            ->selectWithout()
            ->fixtureInfoId($this->fixtureInfoId)
            ->firstOrNew(['fixture_info_id' => $this->fixtureInfoId]);
                    
        return new FixtureBuilder($fixture);
    }
}