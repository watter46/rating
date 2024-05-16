<?php declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Pagination\Paginator;

use App\Models\FixtureInfo;
use App\Livewire\User\Data\FixturesDataPresenter;


final readonly class FixturesPresenter
{   
    /**
     * チーム、リーグ、プレイヤーのファイルパスの画像を取得する
     *
     * @param  Paginator $fixtures
     * @return Paginator
     */
    public function format(Paginator $fixtures): Paginator
    {
        $fixtures
            ->getCollection()
            ->transform(function (FixtureInfo $fixtureInfo) {
                return FixturesDataPresenter::create($fixtureInfo)
                    ->formatFixtureData()
                    ->get();
            });
        
        return $fixtures;
    }
}