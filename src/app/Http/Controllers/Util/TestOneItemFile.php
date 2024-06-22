<?php

namespace App\Http\Controllers\Util;

use App\Models\FixtureInfo;
use Illuminate\Support\Facades\File;

class TestOneItemFile
{
    private const DIR_PATH = 'Template/tests/oneItem/';
    private const TEST_EXTERNAL_FIXTURE_ID = 1035480;

    private function dirPath()
    {
        return app_path(self::DIR_PATH);
    }

    private function fileName(string $dir)
    {
        $fileName = self::TEST_EXTERNAL_FIXTURE_ID.'.json';

        return $this->dirPath().$dir.'/'.$fileName;
    }

    public function getFixtureInfo()
    {
        return json_decode(File::get($this->fileName('fixtureInfo')));
    }

    public function getPlayerInfo(int $foot_player_id)
    {
        return collect(json_decode(File::get($this->fileName('playerInfo'))))
            ->first(fn ($playerInfo) => $playerInfo->foot_player_id === $foot_player_id);
    }
    
    public function getFixture()
    {
        return json_decode(File::get($this->fileName('fixture')));
    }
    
    public function getPlayer()
    {
        return json_decode(File::get($this->fileName('player')))[0];
    }

    public function write()
    {
        $fixtureInfo = FixtureInfo::query()
            ->with('fixture.players', 'playerInfos')
            ->where('external_fixture_id', 1035480)
            ->first();
        
        File::put($this->fileName('fixtureInfo'), $fixtureInfo->toJson());
        File::put($this->fileName('playerInfo'), $fixtureInfo->playerInfos->toJson());
        File::put($this->fileName('fixture'), $fixtureInfo->getRelation('fixture')->toJson());
        File::put($this->fileName('player'), $fixtureInfo->getRelation('fixture')->players->toJson());
    }
}