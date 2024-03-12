<?php declare(strict_types=1);

namespace App\UseCases\Admin\Player\UpdatePlayerInfos;

use App\UseCases\Util\Season;
use Illuminate\Support\Collection;


readonly class SquadsData
{
    private Collection $squadsData;

    private function __construct(Collection $squadsData)
    {
        $this->squadsData = $this->parse($squadsData);
    }

    public static function build(Collection $squadsData)
    {
        return new self($squadsData);
    }

    public function getData(): Collection
    {
        return $this->squadsData;
    }

    private function parse(Collection $squadsData): Collection
    {
        return collect($squadsData['players'])
            ->map(function ($player) {
                return [
                    'foot_player_id' => $player->id,
                    'name' => $player->name,
                    'number' => $player->number,
                    'season' => Season::current()
                ];
            });
    }
}