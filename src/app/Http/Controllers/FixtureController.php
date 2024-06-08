<?php declare(strict_types=1);

namespace App\Http\Controllers;

use Exception;

use App\UseCases\User\Fixture\FindFixture;
use App\UseCases\User\Fixture\fetchLatestFixture;


class FixtureController extends Controller
{
    public function index()
    {
        return view('fixtures');
    }

    public function find(string $fixtureInfoId, FindFixture $findFixture, FixturePresenter $presenter)
    {
        try {
            $fixture = $findFixture->execute($fixtureInfoId);
            
            return view('fixture', $presenter->format($fixture));

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