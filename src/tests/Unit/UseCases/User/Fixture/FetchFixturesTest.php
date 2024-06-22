<?php declare(strict_types=1);

namespace Tests\Unit\UseCases\User\Fixture;

use Tests\TestCase;

use App\Models\TournamentType;
use App\UseCases\User\Fixture\FetchFixtures;
use Database\Seeders\Tests\User\TestingFixtureInfosSeeder;


class FetchFixturesTest extends TestCase
{
    protected $seeder = TestingFixtureInfosSeeder::class;
    
    public function test_試合を15件取得できているか()
    {
        /** @var FetchFixtures $fetchFixtures */
        $fetchFixtures = app(FetchFixtures::class);

        $fixtures = $fetchFixtures->execute(TournamentType::ALL);

        $this->assertSame(15, $fixtures->count());
    }

    public function test_FA杯の試合のみ取得する()
    {
        /** @var FetchFixtures $fetchFixtures */
        $fetchFixtures = app(FetchFixtures::class);

        $fixtures = $fetchFixtures->execute(TournamentType::FA_CUP);

        $this->assertSame(3, $fixtures->count());
    }

    public function test_カラバオ杯の試合のみ取得する()
    {
        /** @var FetchFixtures $fetchFixtures */
        $fetchFixtures = app(FetchFixtures::class);

        $fixtures = $fetchFixtures->execute(TournamentType::LEAGUE_CUP);

        $this->assertSame(2, $fixtures->count());
    }

    public function test_評価されている試合をがあることを確認できる()
    {
        /** @var FetchFixtures $fetchFixtures */
        $fetchFixtures = app(FetchFixtures::class);

        $fixtures = $fetchFixtures->execute(TournamentType::ALL);

        $this->assertSame(1, $fixtures->filter(fn($fixture) => $fixture->isRate)->count());
    }
}