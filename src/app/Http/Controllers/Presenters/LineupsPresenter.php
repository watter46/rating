<?php declare(strict_types=1);

namespace App\Http\Controllers\Presenters;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

use App\Livewire\User\Data\MobileSubstitutesSplitter;


class LineupsPresenter
{
    public function __construct(private Collection $lineups, private Collection $playerInfos)
    {
        dd($playerInfos);
        $lineups = $lineups
            ->map(function (Collection $lineup) {
                return $lineup
                    ->map(function (Collection $player) {
                        return (new PlayerPresenter($player))->format();
                    });
            });
    }

    public function format()
    {
        return collect([
            'startXI' => $this->formatStartXI(),
            'substitutes' => $this->lineups->dataGet('substitutes'),
            'mobile_substitutes' => $this->formatMobileSubstitutes()
        ]);
    }
    
    /**
     * 先発出場した選手を表示用に成形する
     * 
     * @return Collection
     */
    private function formatStartXI(): Collection
    {
        return $this->lineups
            ->dataGet('startXI')
            ->reverse()
            ->groupBy(function ($player) {
                return Str::before($player['grid'], ':');
            })
            ->values();
    }

    private function formatMobileSubstitutes(): Collection
    {
        return MobileSubstitutesSplitter::split($this->lineups->dataGet('substitutes'))->get();
    }
}