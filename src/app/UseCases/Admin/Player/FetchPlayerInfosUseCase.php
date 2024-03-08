<?php declare(strict_types=1);

namespace App\UseCases\Admin\Player;

use Exception;
use Illuminate\Support\Collection;

use App\Models\PlayerInfo;
use App\Http\Controllers\Util\PlayerImageFile;


final readonly class FetchPlayerInfosUseCase
{
    public function __construct(private PlayerImageFile $playerImage)
    {
        //
    }

    public function execute(): Collection
    {
        try {
            return PlayerInfo::query()
                ->select('id')
                ->currentSeason()
                ->pluck('id');

        } catch (Exception $e) {
            throw $e;
        }
    }
}