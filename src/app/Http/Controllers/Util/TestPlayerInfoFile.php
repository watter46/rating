<?php declare(strict_types=1);

namespace App\Http\Controllers\Util;

use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\SplFileInfo;
use Illuminate\Support\Str;

use App\Models\FixtureInfo;
use App\Models\PlayerInfo;

class TestPlayerInfoFile
{
    private const DIR_PATH = 'Template/tests/playerInfos/';

    private function dirPath()
    {
        return app_path(self::DIR_PATH);
    }

    private function fileName(int $external_fixture_id)
    {
        return $this->dirPath().$external_fixture_id.'.json';
    }

    public function get(int $external_fixture_id)
    {
        return collect(json_decode(File::get($this->fileName($external_fixture_id))));
    }

    public function write(int $external_fixture_id)
    {
        $fixtureInfo = FixtureInfo::query()
            ->where('external_fixture_id', $external_fixture_id)
            ->first();

        $playerInfos = PlayerInfo::query()
            ->whereIn('foot_player_id', collect($fixtureInfo->lineups)
                ->flatten(1)
                ->pluck('id')
                ->toArray())
            ->get();
        
        File::put($this->fileName($external_fixture_id), $playerInfos->toJson());
    }

    public function idList()
    {
        return collect(File::files($this->dirPath()))
            ->map(function (SplFileInfo $info) {
                $fileName = $info->getFilename();

                $id = Str::before($fileName, '.json');
                
                return (int) $id;
            });
    }

    public function writeAll()
    {
        $file = new FixtureFile;
        $file->getIdList();

        $testExternalFixtureIds = collect(File::files($this->dirPath()))
            ->map(function (SplFileInfo $info) {
                $fileName = $info->getFilename();

                $id = Str::before($fileName, '.json');
                
                return (int) $id;
            });

        $invalidExternalFixtureIds = $file->getIdList()->diff($testExternalFixtureIds);

        $invalidExternalFixtureIds
            ->each(function (int $external_fixture_id) {
                $this->write($external_fixture_id);
            });
    }
}