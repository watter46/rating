<?php declare(strict_types=1);

namespace App\UseCases\Admin\Player\Processors\PlayerInfos;

use Illuminate\Support\Str;

use App\Models\PlayerInfo;


class PlayerDataMatcher
{
    public function __construct(private PlayerInfo $playerInfo)
    {
        
    }
        
    /**
     * match
     *
     * @param  array{id: string, name: string, number: int} $player
     * @return bool
     */
    public function match(array $player): bool
    {
        return collect([
                $this->matchFullNameAndNumber($player),
                $this->matchLastNameAndNumber($player)
            ])
            ->some(fn($matched) => $matched);
    }

    private function matchFullNameAndNumber(array $player): bool
    {
        return $this->transliterate($this->playerInfo->name) === $this->transliterate($player['name'])
            && $this->playerInfo->number === $player['number'];
    }

    private function matchLastNameAndNumber($player): bool
    {
        $lastName = function($name) {
                return Str::afterLast($this->transliterate($name), ' ');
            };
        
        return $lastName($this->playerInfo->name) === $lastName($player['name'])
            && $this->playerInfo->number === $player['number'];
    }

    private function transliterate(string $name): string
    {
        return Str::ascii($name);
    }
}
