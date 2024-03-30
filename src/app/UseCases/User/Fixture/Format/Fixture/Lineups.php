<?php declare(strict_types=1);

namespace App\UseCases\User\Fixture\Format\Fixture;

use Illuminate\Support\Collection;

use App\Http\Controllers\PositionType;
use App\Http\Controllers\Util\PlayerImageFile;


readonly class Lineups
{
    public function __construct(private Chelsea $chelsea, private PlayerImageFile $playerImage)
    {
        //
    }
    
    public function build($data): Collection
    {
        $chelsea = $this->chelsea->filter($data);
                
        return $chelsea
            ->only(['startXI', 'substitutes'])
            ->map(function ($lineups) {
                return collect($lineups)
                    ->map(function ($lineup) {
                        return collect([
                            'id'       => $lineup->player->id,
                            'name'     => $lineup->player->name,
                            'number'   => $lineup->player->number,
                            'position' => PositionType::from($lineup->player->pos)->name,
                            'grid'     => $lineup->player->grid,
                            'img'      => $this->playerImage->generatePath($lineup->player->id)
                        ]);
                    });
            });
    }
}