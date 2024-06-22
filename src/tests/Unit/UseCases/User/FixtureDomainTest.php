<?php declare(strict_types=1);

namespace Tests\Unit\UseCases\User;

use Tests\TestCase;
use Illuminate\Support\Carbon;

use App\Models\Fixture;
use App\Models\FixtureInfo;
use App\Models\Player;
use App\UseCases\User\FixtureDomain;


class FixtureDomainTest extends TestCase
{
    public function test_評価数が3以下ならfalse4以上ならtrueを返す(): void
    {
        $validPlayer = new Player(['rate_count' => 3]);
        $invalidPlayer = new Player(['rate_count' => 4]);

        $domain = new FixtureDomain(new Fixture);

        $this->assertFalse($domain->exceedRateLimit($validPlayer));
        $this->assertTrue($domain->exceedRateLimit($invalidPlayer));
    }

    public function test_Mom評価数が5以下ならfalse6以上ならtrueを返す(): void
    {
        $validDomain = new FixtureDomain(new Fixture(['mom_count' => 5]));
        $invalidDomain = new FixtureDomain(new Fixture(['mom_count' => 6]));

        $this->assertFalse($validDomain->exceedMomLimit());
        $this->assertTrue($invalidDomain->exceedMomLimit());
    }

    public function test_評価期間を超えていたらtrue超えていないならfalseを返す(): void
    {
        Carbon::setTestNow(now('UTC'));
        
        $now = now('UTC');
        
        // 120時間後
        $validDomain = new FixtureDomain((new Fixture)
            ->setRelation('fixtureInfo', new FixtureInfo(['date' => $now->copy()->addHours(120)->__toString()])));

        // 121時間後
        $invalidDomain = new FixtureDomain((new Fixture)
            ->setRelation('fixtureInfo', new FixtureInfo(['date' => $now->copy()->addHours(121)->__toString()])));
            
        $this->assertTrue($invalidDomain->exceedPeriodDay());
        $this->assertFalse($validDomain->exceedPeriodDay());
    }

    public function test_評価できるときtrueできないときfalseを返す()
    {
        Carbon::setTestNow(now('UTC'));
        
        $now = now('UTC');
        
        // 120時間後
        $validDomain = new FixtureDomain((new Fixture)
            ->setRelation('fixtureInfo', new FixtureInfo(['date' => $now->copy()->addHours(120)->__toString()])));

        // fixtureInfo:valid Player: valid
        $this->assertTrue($validDomain->canRate(new Player(['rate_count' => 3])));

        // fixtureInfo:valid Player: invalid
        $this->assertFalse($validDomain->canRate(new Player(['rate_count' => 4])));

        // 121時間後
        $invalidDomain = new FixtureDomain((new Fixture)
            ->setRelation('fixtureInfo', new FixtureInfo(['date' => $now->copy()->addHours(121)->__toString()])));

        // fixtureInfo:invalid Player: valid
        $this->assertFalse($invalidDomain->canRate(new Player(['rate_count' => 3])));

        // fixtureInfo:invalid Player: invalid
        $this->assertFalse($invalidDomain->canRate(new Player(['rate_count' => 4])));
    }

    public function test_Momを評価できるときtrueできないときfalseを返す()
    {
        Carbon::setTestNow(now('UTC'));
        
        $now = now('UTC');
        
        // fixture: valid fixtureInfo:valid
        $validDomain = new FixtureDomain((new Fixture(['mom_count' => 5]))
            ->setRelation('fixtureInfo', new FixtureInfo(['date' => $now->copy()->addHours(120)->__toString()])));

        // fixture: invalid fixtureInfo:valid
        $inValidDomain = new FixtureDomain((new Fixture(['mom_count' => 6]))
            ->setRelation('fixtureInfo', new FixtureInfo(['date' => $now->copy()->addHours(120)->__toString()])));

        $this->assertTrue($validDomain->canMom(new Player));
        $this->assertFalse($inValidDomain->canMom(new Player));

        // fixture: invalid fixtureInfo:valid
        $validDomain2 = new FixtureDomain((new Fixture(['mom_count' => 5]))
            ->setRelation('fixtureInfo', new FixtureInfo(['date' => $now->copy()->addHours(121)->__toString()])));

        // fixture: invalid fixtureInfo:invalid
        $inValidDomain2 = new FixtureDomain((new Fixture(['mom_count' => 6]))
            ->setRelation('fixtureInfo', new FixtureInfo(['date' => $now->copy()->addHours(121)->__toString()])));

        $this->assertFalse($validDomain2->canMom(new Player));
        $this->assertFalse($inValidDomain2->canMom(new Player));
    }
}
