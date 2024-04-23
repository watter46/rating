<?php declare(strict_types=1);

namespace App\UseCases\Admin\Fixture\FixtureInfoData;

use Illuminate\Support\Collection;

use App\Http\Controllers\Util\LeagueImageFile;
use App\Http\Controllers\Util\PlayerImageFile;
use App\Http\Controllers\Util\TeamImageFile;
use App\UseCases\Admin\Fixture\FixtureData\FilterInvalidPlayerIds;
use App\UseCases\Admin\Fixture\ValidatorInterface;

readonly class FixtureInfoDataValidator2 implements ValidatorInterface
{
    private Collection $invalidTeamIds;
    private Collection $invalidLeagueIds;
    private Collection $invalidPlayers;
    private Collection $invalidPlayerImageIds;
    
    private function __construct(private Collection $fixtureData)
    {
        $this->validateTeamIds();
        $this->validateLeagueIds();
        $this->validatePlayerIds();
        $this->validatePlayerImage();
    }

    public function getInvalidTeamIds(): Collection
    {
        return $this->invalidTeamIds;
    }

    public function getInvalidLeagueIds(): Collection
    {
        return $this->invalidLeagueIds;
    }

    public function getInvalidPlayers(): Collection
    {
        return $this->invalidPlayers;
    }

    public function getInvalidPlayerImageIds(): Collection
    {
        return $this->invalidPlayerImageIds;
    }
    
    /**
     * 保存されていない画像のチームIDを取得する
     *
     * @return void
     */
    private function validateTeamIds(): void
    {
        $file = new TeamImageFile();
        
        $this->invalidTeamIds = collect($this->fixtureData->get('teams'))
            ->map(fn ($team) => $team->id)
            ->values()
            ->filter(fn (int $teamId) => !$file->exists($teamId));
    }
    
    /**
     * 保存されていない画像のリーグIDを取得する
     *
     * @return void
     */
    private function validateLeagueIds(): void
    {
        $file = new LeagueImageFile();

        $leagueId = $this->fixtureData->get('league')->id;

        $this->invalidLeagueIds = $file->exists($leagueId)
            ? collect()
            : collect($leagueId);
    } 
    
    /**
     * 保存されていないPlayerIDを取得する
     *
     * @return void
     */
    private function validatePlayerIds(): void
    {
        $playedPlayerIds = $this->getPlayedPlayers()->map(fn($player) => $player['id']);

        $invalidPlayerIds = (new FilterInvalidPlayerIds)->execute($playedPlayerIds);

        $this->invalidPlayers = $this->getPlayedPlayers()
            ->whereIn('id', $invalidPlayerIds)
            ->values();
    }
    
    /**
     * 保存されていないPlayerImageのIDを取得する
     *
     * @return void
     */
    public function validatePlayerImage(): void
    {
        $file = new PlayerImageFile();

        $this->invalidPlayerImageIds = $this->getPlayedPlayers()
            ->reject(fn($player) => $file->exists($player['id']))
            ->pluck('id')
            ->values();
    }
    
    /**
     * データがすべて存在しているか判定する
     *
     * @return bool
     */
    public function checkRequiredData(): bool
    {
        return $this->getInvalidTeamIds()->empty()
            || $this->getInvalidLeagueIds()->empty()
            || $this->getPlayedPlayers()->empty()
            || $this->getInvalidPlayerImageIds()->empty();
    }
    
    /**
     * FixtureData
     *
     * @param  Collection $fixtureData
     * @return self
     */
    public static function validate(Collection $fixtureData): self
    {
        return new self($fixtureData);
    }
    
    /**
     * 出場した選手のみ取得する
     *
     * @return Collection<int>
     */
    public function getPlayedPlayers(): Collection
    {
        $players = collect($this->fixtureData->get('players'))
            ->first(fn($teams) => $teams->team->id === 49)
            ->players;

        $playedPlayerIds = collect($players)
            ->filter(fn($player) => $player->statistics[0]->games->minutes)
            ->map(fn($player) => $player->player->id);

        $lineups = collect($this->fixtureData->get('lineups'))
            ->first(fn($teams) => $teams->team->id === 49);

        return collect($lineups)
            ->only(['startXI', 'substitutes'])
            ->flatten()
            ->map(function ($startXI) {
                $player = $startXI->player;

                return [
                    'id' => $player->id,
                    'name' => $player->name,
                    'number' => $player->number
                ];
            })
            ->whereIn('id', $playedPlayerIds);
    }
}