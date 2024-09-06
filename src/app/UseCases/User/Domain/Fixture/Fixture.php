<?php declare(strict_types=1);

namespace App\UseCases\User\Domain\Fixture;

use Exception;
use App\UseCases\User\Domain\FixtureId;
use App\UseCases\User\Domain\Fixture\MomCount;


class Fixture
{
    /** 評価可能期間 5日間 */
    private const RATE_PERIOD_HOURS = 24 * 5;
    public const RATE_PERIOD_EXPIRED_MESSAGE = 'Rate period has expired.';

    /**
     * Score
     * Teams
     * League
     * Fixture
     * Lineups
     */
    public function __construct(
        private FixtureId $id,
        private MomCount $count,
        private PlayerIds $playerIds)
    {
        
    }

    public function incrementMomCount()
    {
        $count = $this->count->increment();

        if ($count->exceed()) {
            throw new Exception(MomCount::MOM_LIMIT_EXCEEDED_MESSAGE);
        }

        return new self($this->id, $count);
    }

    public function decideMom(Player $player)
    {
        if ($this->fixtureInfo->exceedPeriodDay(self::RATE_PERIOD_HOURS)) {
            return new Exception(self::MOM_LIMIT_EXCEEDED_MESSAGE);
        }

        $this->incrementMomCount();

        $player->decideMom();

        $this->fixtureInfo->
    }
}