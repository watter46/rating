<?php declare(strict_types=1);

namespace App\UseCases\Player;

use Illuminate\Support\Collection;

use App\Models\Player;
use App\UseCases\Api\SofaScore\FindPlayer;
use App\UseCases\Util\Season;


final readonly class RegisterPlayerBuilder
{
    public function __construct(private FindPlayer $findPlayer)
    {
        
    }
    
    /**
     * build
     *
     * @property Collection<int, Player> $players
     * @return array
     */
    public function build(Collection $players)
    {
        $data = $players
            ->map(function (Collection $player) {
                $playerData = $this->findPlayer->fetch($player['player']['name']);
                
                $filtered = $playerData
                    ->filter(function ($player) {
                        return $player->team->shortName === 'Chelsea'
                            || $player->team->nameCode === 'CFC';
                    });

                if ($filtered->isEmpty()) {
                    return collect([
                        'name' => $player['player']['name'],
                        'season' => Season::current(),
                        'number' => $player['player']['number'],
                        'foot_player_id' => $player['player']['id'],
                        'sofa_player_id' => null
                    ]);
                }

                $newPlayer = json_decode($filtered->toJson())[0];
                                    
                $data = collect([
                        'name' => $newPlayer->shortName,
                        'season' => Season::current(),
                        'number' => $newPlayer->jerseyNumber,
                        'foot_player_id' => $player['player']['id'],
                        'sofa_player_id' => $newPlayer->id
                    ]);
                
                return $player->get('model')->isEmpty()
                    ? $data
                    : $data->merge(['id' => $player['model']->first()->id]);
            })
            ->toArray();

        return $data;
    }
}