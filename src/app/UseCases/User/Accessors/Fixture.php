<?php declare(strict_types=1);

namespace App\UseCases\User\Accessors;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use App\Models\Fixture as FixtureModel;


class Fixture
{
    /** 評価可能期間 5日間 */
    private const RATE_PERIOD_HOURS = 24 * 5;
    public const RATE_PERIOD_EXPIRED_MESSAGE = 'Rate period has expired.';

    private const RATE_LIMIT = 3;
    public const RATE_LIMIT_EXCEEDED_MESSAGE = 'Rate limit exceeded.';

    private const MOM_LIMIT = 5;
    public const MOM_LIMIT_EXCEEDED_MESSAGE = 'MOM limit exceeded.';

    public function __construct(private Collection $fixture)
    {
        
    }

    public static function reconstruct(FixtureModel $fixture)
    {
        return new self(collect($fixture)->toCollection());
    }
    
    public function getMomCount()
    {
        return $this->fixture['mom_count'];
    }

    public function getFixtureId()
    {
        return $this->fixture['id'];
    }

    public function getFixtureInfoId()
    {
        return $this->fixture['fixture_info_id'];
    }

    public function getFixtureInfo()
    {
        return $this->fixture->dataGet('fixture_info');
    }

    public function getPlayerInfos()
    {
        return $this->fixture->dataGet('fixture_info.player_infos');
    }

    public function getPlayers()
    {
        return $this->fixture->dataGet('players');
    }

    public function getLineups()
    {
        return $this->fixture->dataGet('fixture_info.lineups');
    }

    public function getRateLimit(): int
    {
        return self::RATE_LIMIT;
    }

    public function getMomLimit(): int
    {
        return self::MOM_LIMIT;
    }
    
    /**
     * MOMを選択できる回数を超えているか判定する
     *
     * @return bool
     */
    public function exceedMomLimit(): bool
    {
        return $this->fixture->mom_count > self::MOM_LIMIT;
    }
    
    /**
     * 評価可能期間を超えている判定する
     *
     * @return bool
     */
    public function exceedPeriodDay(): bool
    {
        $specifiedDate = Carbon::parse($this->fixture->fixtureInfo->date);
        
        return $specifiedDate->diffInHours(now('UTC')) >= self::RATE_PERIOD_HOURS;
    }
}