<?php declare(strict_types=1);

namespace App\UseCases\Fixtures;

use App\Http\Controllers\Util\LeagueImageFile;
use App\Http\Controllers\Util\TeamImageFile;
use Illuminate\Support\Collection;


class FixturesDataProcessor
{
    private function __construct(private Collection $fixturesData)
    {
        //
    }
    
    /**
     * 保存されていない画像のチームIDを取得する
     *
     * @return Collection<int, int>
     */
    public function getInvalidTeamIds(): Collection
    {
        $file = new TeamImageFile();

        return $this->fixturesData
            ->map(function ($fixture) {
                return collect($fixture->teams)
                    ->map(fn($team) => $team->id);  
            })
            ->flatten()
            ->unique()
            ->values()
            ->filter(fn (int $teamId) => !$file->exists($teamId));
    }
    
    /**
     * 保存されていない画像のリーグIDを取得する
     *
     * @return Collection<int, int>
     */
    public function getInvalidLeagueIds(): Collection
    {
        $file = new LeagueImageFile();

        return $this->fixturesData
            ->map(fn ($fixture) => $fixture->league->id)
            ->unique()
            ->values()
            ->filter(fn (int $leagueId) => !$file->exists($leagueId));
    } 
    
    /**
     * 保存するべきか判定する
     *
     * @return bool
     */
    public function shouldRegister(): bool
    {
        return $this->getInvalidTeamIds()->isNotEmpty()
            || $this->getInvalidLeagueIds()->isNotEmpty();
    }
    
    /**
     * FixturesData
     *
     * @param  Collection $fixturesData
     * @return self
     */
    public static function validate(Collection $fixturesData): self
    {
        return new self($fixturesData);
    }
}