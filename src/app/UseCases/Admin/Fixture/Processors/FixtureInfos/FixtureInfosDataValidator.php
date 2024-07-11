<?php declare(strict_types=1);

namespace App\UseCases\Admin\Fixture\Processors\FixtureInfos;

use Illuminate\Support\Collection;

use App\Http\Controllers\Util\LeagueImageFile;
use App\Http\Controllers\Util\TeamImageFile;
use App\UseCases\Admin\Fixture\Processors\FixtureInfos\FixtureInfosData;
use App\UseCases\Admin\Fixture\ValidatorInterface;


class FixtureInfosDataValidator implements ValidatorInterface
{
    private TeamImageFile $teamImage;
    private LeagueImageFile $leagueImage;

    /**
     * __construct
     *
     * @param  FixtureInfosData $fixtureInfosData
     * @return void
     */
    private function __construct(private FixtureInfosData $fixtureInfosData)
    {
        $this->teamImage   = new TeamImageFile;
        $this->leagueImage = new LeagueImageFile;
    }
    
    /**
     * 保存されていない画像のチームIDを取得する
     *
     * @return Collection<int>
     */
    public function getInvalidTeamIds(): Collection
    {
        return $this->fixtureInfosData
            ->getUniqueTeamIds()
            ->filter(fn (int $teamId) => !$this->teamImage->exists($teamId));
    }
    
    /**
     * 保存されていない画像のリーグIDを取得する
     *
     * @return Collection<int>
     */
    public function getInvalidLeagueIds(): Collection
    {
        return $this->fixtureInfosData
            ->getUniqueLeagueIds()
            ->filter(fn (int $leagueId) => !$this->leagueImage->exists($leagueId));
    } 
    
    /**
     * データがすべて存在しているか判定する
     *
     * @return bool
     */
    public function checkRequiredData(): bool
    {
        return collect([
                $this->getInvalidTeamIds(),
                $this->getInvalidLeagueIds()
            ])
            ->every(fn (Collection $ids) => $ids->isEmpty());
    }
    
    /**
     * FixtureInfosData
     *
     * @param  FixtureInfosData
     * @return self
     */
    public static function validate(FixtureInfosData $fixtureInfosData): self
    {
        return new self($fixtureInfosData);
    }
}