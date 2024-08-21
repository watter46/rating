<?php declare(strict_types=1);

namespace App\Http\Controllers\Util;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

use App\Models\PlayerInfo;


class PlayerInfoFile
{
    private const DIR_PATH = 'Template/playerInfo';

    private function dirPath()
    {
        return app_path(self::DIR_PATH);
    }

    private function fileName(int $season)
    {
        return $this->dirPath().'/'.$season.'.json';
    }

    public function get(int $season): Collection
    {
        return collect(json_decode(File::get($this->fileName($season))))
            ->fromStd()
            ->toCollection();
    }

    public function write(int $season): void
    {
        $playerInfos = PlayerInfo::where('season', $season)
            ->get()
            ->toCollection()
            ->map(fn(Collection $p) => $p->except('id'));
        
        if (!File::exists($this->dirPath())) {
            File::makeDirectory($this->dirPath());
        }
            
        File::put($this->fileName($season), $playerInfos->toJson());
    }
}