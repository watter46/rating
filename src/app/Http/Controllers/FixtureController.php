<?php declare(strict_types=1);

namespace App\Http\Controllers;

use Exception;

use App\UseCases\User\Fixture\FindFixture;
use App\UseCases\User\Fixture\fetchLatestFixture;
use App\Http\Controllers\Presenters\FixturePresenter;


class FixtureController extends Controller
{
    public function index()
    {
        return view('fixtures');
    }

    public function find(string $fixtureInfoId, FindFixture $findFixture)
    {
        try {
            $fixture = $findFixture->execute($fixtureInfoId);
            
            return view('fixture', (new FixturePresenter($fixture))->format());

        } catch (Exception $e) {
            throw $e;
        }
    }

    public function latest(fetchLatestFixture $fetchLatestFixture, FixturePresenter $presenter)
    {
        try {
            $fixture = $fetchLatestFixture->execute();
                        
            return view('fixture', $presenter->format($fixture));

        } catch (Exception $e) {
            throw $e;
        }
    }
}