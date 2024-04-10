<?php declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Pagination\Paginator;

use App\Models\Fixture;
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
            ->transform(function (Fixture $fixture) {
                $fixture->fixture = FixturesDataPresenter::create($fixture)
                    ->formatPathToLeagueImage()
                    ->formatPathToTeamImages()
                    ->formatFixtureData()
                    ->get();
                    
                return $fixture;
            });
            
        return $fixtures;
    }
}