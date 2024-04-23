<?php declare(strict_types=1);

namespace App\UseCases\Admin\Fixture\FixtureInfoData;

use Illuminate\Support\Collection;

use App\Http\Controllers\Util\LeagueImageFile;
use App\Http\Controllers\Util\PlayerImageFile;
use App\Http\Controllers\Util\TeamImageFile;
use App\UseCases\Admin\Fixture\Data\FixtureData;
use App\UseCases\Admin\Fixture\FixtureData\FilterInvalidPlayerIds;
use App\UseCases\Admin\Fixture\ValidatorInterface;


readonly class FixtureInfoDataValidator implements ValidatorInterface
{
    private Collection $invalidTeamIds;
    private Collection $invalidLeagueIds;
    private Collection $invalidPlayerIds;
    private Collection $invalidPlayerImageIds;
    
    private function __construct(private FixtureData $fixtureData)
    {
        $this->validateTeamIds();
        $this->validateLeagueIds();
        $this->validatePlayerIds();
        $this->validatePlayerImage();
    }

    /**
     * データがすべて存在しているか判定する
     *
     * @return bool
     */
    public function checkRequiredData(): bool
    {
        return $this->getInvalidTeamIds()->isEmpty()
            && $this->getInvalidLeagueIds()->isEmpty()
            && $this->getInvalidPlayerIds()->isEmpty()
            && $this->getInvalidPlayerImageIds()->isEmpty();
    }
    
    /**
     * FixtureData
     *
     * @param  FixtureData $fixtureData
     * @return self
     */
    public static function validate(FixtureData $fixtureData): self
    {
        return new self($fixtureData);
    }
    
    /**
     * 保存されていないチームIDを取得する
     *
     * @return Collection
     */
    public function getInvalidTeamIds(): Collection
    {
        return $this->invalidTeamIds;
    }
    
    /**
     * 保存されていないリーグIDを取得する
     *
     * @return Collection
     */
    public function getInvalidLeagueIds(): Collection
    {
        return $this->invalidLeagueIds;
    }
    
    /**
     * 保存されていないPlayerIDを取得する
     *
     * @return Collection
     */
    public function getInvalidPlayerIds(): Collection
    {
        return $this->invalidPlayerIds;
    }
    
    /**
     * 保存されていない選手の画像のPlayerIDを取得する
     *
     * @return Collection
     */
    public function getInvalidPlayerImageIds(): Collection
    {
        return $this->invalidPlayerImageIds;
    }
    
    public function validateTeamIds(): void
    {
        $file = new TeamImageFile();

        $this->invalidTeamIds = $this->fixtureData->getTeams()
            ->map(fn ($team) => $team->get('id'))
            ->values()
            ->filter(fn (int $teamId) => !$file->exists($teamId));
    }
    
    public function validateLeagueIds(): void
    {
        $file = new LeagueImageFile();

        $leagueId = $this->fixtureData->getLeague()->get('id');

        $this->invalidLeagueIds = $file->exists($leagueId)
            ? collect()
            : collect($leagueId);
    }
    
    public function validatePlayerIds(): void
    {
        $players = $this->fixtureData->getPlayedPlayers();

        $playerIds = $players->pluck('id');

        $this->invalidPlayerIds = (new FilterInvalidPlayerIds)->execute($playerIds);
    }
    
    public function validatePlayerImage(): void
    {
        $file = new PlayerImageFile();

        $this->invalidPlayerImageIds = $this->fixtureData->getPlayedPlayers()
            ->reject(fn($player) => $file->exists($player['id']))
            ->pluck('id')
            ->values();
    }
}