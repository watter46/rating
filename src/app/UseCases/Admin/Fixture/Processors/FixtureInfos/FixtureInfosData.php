<?php declare(strict_types=1);

namespace App\UseCases\Admin\Fixture\Processors\FixtureInfos;

use Illuminate\Support\Collection;

use App\Models\FixtureInfo;


class FixtureInfosData
{    
    /**
     * __construct
     *
     * @param  Collection<array{leagueId: int, teamIds: Collection<int>}> $fixtureInfos
     * @return void
     */
    private function __construct(private Collection $fixturesData)
    {
        
    }

    public static function create(Collection $fixtureInfos): self
    {
        return new self(
            $fixtureInfos
                ->map(function (FixtureInfo $fixtureInfo) {
                    return [
                        'leagueId' => $fixtureInfo->league['id'],
                        'teamIds'  => $fixtureInfo->teams->pluck('id')
                    ];
                })
        );
    }

    public function getUniqueTeamIds(): Collection
    {
        return $this->fixturesData
            ->pluck('teamIds')
            ->flatten()
            ->unique()
            ->values();
    }
        
    /**
     * リーグIDのリストを取得する
     *
     * @return Collection<int, int>
     */
    public function getUniqueLeagueIds(): Collection
    {
        return $this->fixturesData
            ->pluck('leagueId')
            ->unique()
            ->values();
    }
}