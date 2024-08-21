<?php declare(strict_types=1);

namespace App\Http\Controllers\Util;

use Illuminate\Support\Facades\File;
use App\UseCases\Util\Season;


class TestFixtureInfosFile
{
    private const DIR_PATH = 'Template/tests/fixtureInfos/';

    private function dirPath()
    {
        return app_path(self::DIR_PATH);
    }

    private function fileName(int $season)
    {
        return $this->dirPath().$season.'.json';
    }

    public function get(?int $season = null)
    {
        $data = json_decode(File::get($this->fileName($season ?? Season::current())));

        return collect($data)
            ->reverse()
            ->values()
            ->map(function ($fixtureData, $index) {
                $fixtureData->fixture->date = now()
                    ->subDays(1)
                    ->subDays($index * 5)
                    ->format('Y-m-d\TH:i:sP');;

                return $fixtureData;
            });
    }
}