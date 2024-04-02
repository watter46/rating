<?php declare(strict_types=1);

namespace App\UseCases\Admin\Fixture\FixturesData;

use Illuminate\Support\Collection;

use App\Http\Controllers\Util\LeagueImageFile;
use App\Http\Controllers\Util\TeamImageFile;
use App\UseCases\Admin\Fixture\FixturesData\FixturesData;
use App\UseCases\Admin\Fixture\ValidatorInterface;

class FixturesDataValidator implements ValidatorInterface
{
    private TeamImageFile $teamImage;
    private LeagueImageFile $leagueImage;

    /**
     * __construct
     *
     * @param  FixturesData $fixturesData
     * @return void
     */
    private function __construct(private FixturesData $fixturesData)
    {
        $this->teamImage   = new TeamImageFile();
        $this->leagueImage = new LeagueImageFile();
    }
    
    /**
     * 保存されていない画像のチームIDを取得する
     *
     * @return Collection<int>
     */
    public function getInvalidTeamIds(): Collection
    {
        return $this->fixturesData
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
        return $this->fixturesData
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
        return $this->getInvalidTeamIds()->isEmpty()
            || $this->getInvalidLeagueIds()->isEmpty();
    }
    
    /**
     * FixturesData
     *
     * @param  FixturesData
     * @return self
     */
    public static function validate(FixturesData $fixturesData): self
    {
        return new self($fixturesData);
    }
}