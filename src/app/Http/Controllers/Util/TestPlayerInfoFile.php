<?php declare(strict_types=1);

namespace App\Http\Controllers\Util;

use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\SplFileInfo;
use Illuminate\Support\Str;

use App\Models\FixtureInfo;
use App\Models\PlayerInfo;
use Exception;

class TestPlayerInfoFile
{
    private const DIR_PATH = 'Template/tests/playerInfo';

    private function dirPath()
    {
        return app_path(self::DIR_PATH);
    }

    public function gets()
    {
        collect(File::files($this->dirPath()))->each(function (SplFileInfo $file) {
            try {
                $f = collect(json_decode($file->getContents()))
                    ->fromStd()
                    ->toCollection();

                $fixture = $f
                    ->map(function ($player) {
                        $player['name'] = Str::ascii($player['name']);
                        
                        return $player;
                    });
            
                File::put($file->getPath().'/'.$file->getFilename(), $fixture->toJson());
            } catch (Exception $e) {
                // dd($file->getFilename());
            }
            
        });
    }

    private function fileName(int $api_fixture_id)
    {
        return $this->dirPath().'/'.$api_fixture_id.'.json';
    }

    public function get(int $api_fixture_id)
    {
        return collect(json_decode(File::get($this->fileName($api_fixture_id))));
    }

    public function write(int $api_fixture_id)
    {
        $fixtureInfo = FixtureInfo::query()
            ->where('api_fixture_id', $api_fixture_id)
            ->first();

        if (!$fixtureInfo?->lineups) return;

        $playerInfos = PlayerInfo::query()
            ->whereIn('api_player_id', collect($fixtureInfo->lineups)
                ->flatten(1)
                ->pluck('id')
                ->toArray())
            ->get();
        
        File::put($this->fileName($api_fixture_id), $playerInfos->toJson());
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

        $testApiFixtureIds = collect(File::files($this->dirPath()))
            ->map(function (SplFileInfo $info) {
                $fileName = $info->getFilename();

                $id = Str::before($fileName, '.json');
                
                return (int) $id;
            });

        $invalidApiFixtureIds = $file->getIdList()->diff($testApiFixtureIds);

        $invalidApiFixtureIds
            ->each(function (int $api_fixture_id) {
                $this->write($api_fixture_id);
            });
    }
}