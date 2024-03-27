<?php declare(strict_types=1);

namespace App\UseCases\Admin\Fixture\FixtureData;

use App\Events\FixtureRegistered;
use Illuminate\Support\Collection;
use App\UseCases\Admin\Fixture\FixtureData\FixtureDataProcessor;


readonly class Fixture
{    
    /**
     * 試合の表示で必要になる情報(チーム画像、リーグ画像、プレイヤー画像、プレイヤー情報)が
     * 既に存在しているか確認して、存在していない情報は取得して保存するイベントを発行する
     *
     * @param  Collection $fixtureData
     * @return void
     */
    public function registered(Collection $fixtureData): void
    {
        $validate = FixtureDataProcessor::validate($fixtureData);
        
        if (!$validate->shouldRegister()) {
            return;
        }
        
        FixtureRegistered::dispatch($validate);
    }
}