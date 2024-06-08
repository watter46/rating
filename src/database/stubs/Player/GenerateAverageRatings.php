<?php declare(strict_types=1);

namespace Database\Stubs\Player;

use Illuminate\Support\Collection;

class GenerateAverageRatings
{
    public function momPercent(Collection $player)
    {
        $momPercent = $player->filter(fn($data) => $data['mom'])->count() / $player->count() * 100;
        
        return strval($momPercent);
    }

    public function rand($min = 3, $max = 10)
    {
        return round($min + mt_rand() / mt_getrandmax() * ($max - $min), 1);
    }

    public function createPlayersData()
    {
        $generateCount = 16;
        
        $result = collect();

        foreach(range(1, $generateCount) as $i) {
            $result->push(
                collect([
                    "rating" => $this->rand(),
                    "mom" => false,
                    "player_info_id" => "$i"
                ])
            );
        }

        $momIndex = rand(0, $generateCount - 1);

        $result->map(function (Collection $player, $index) use ($momIndex) {
            if ($index !== $momIndex) {
                return $player;
            }

            return $player->put('mom', true);
        });
        
        return $result;
    }

    public function createData()
    {
        $result = collect();
        
        foreach(range(1, 100) as $i) {
            $result->push($this->createPlayersData());
        }
        
        return $result;
    }
}