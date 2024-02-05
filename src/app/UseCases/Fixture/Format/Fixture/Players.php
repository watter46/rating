<?php declare(strict_types=1);

namespace App\UseCases\Fixture\Format\Fixture;


readonly class Players
{
    public function __construct(private Chelsea $chelsea)
    {
        //
    }
        
    /**
     * 試合に出場した選手のみ取得する
     *
     * @param  mixed $data
     * @return void
     */
    public function build($data)
    {
        $chelsea = $this->chelsea->filter($data);

        return collect($chelsea->get('players'))
            ->reject(function ($players) {
                return !$players->statistics[0]->games->minutes;
            })
            ->map(function ($players) {
                return [
                    'id' => $players->player->id,
                    'name' => $players->player->name,
                    'goal' => $players->statistics[0]->goals->total, 
                    'assists' => $players->statistics[0]->goals->assists, 
                    'defaultRating' => (float) $players->statistics[0]->games->rating,
                ];
            });
    }
}