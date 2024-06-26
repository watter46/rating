<?php declare(strict_types=1);

namespace App\Http\Controllers\Util;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Collection;

use App\Models\PlayerInfo;
use App\UseCases\Util\Season;


class TestPlayerInfosFile
{
    private const DIR_PATH = 'Template/tests/playerInfos/';

    private function dirPath(): string
    {
        return app_path(self::DIR_PATH);
    }

    private function fileName(int $season): string
    {
        return $this->dirPath().$season.'.json';
    }

    public function get(?int $season = null): Collection
    {
        return collect(json_decode(File::get($this->fileName($season ?? Season::current())), true));
    }

    public function write(?int $season = null): void
    {
        $playerInfos = PlayerInfo::query()
            ->currentSeason()
            ->get()
            ->map(fn(PlayerInfo $playerInfo) => collect($playerInfo)->except('id')->toArray());
        
        File::put($this->fileName($season ?? Season::current()), $playerInfos->toJson());
    }
}