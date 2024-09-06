<?php

namespace App\UseCases\User\Accessors;

class Player
{
    /** 評価可能期間 5日間 */
    private const RATE_PERIOD_HOURS = 24 * 5;
    public const RATE_PERIOD_EXPIRED_MESSAGE = 'Rate period has expired.';

    private const RATE_COUNT_LIMIT = 3;
    public const RATE_LIMIT_EXCEEDED_MESSAGE = 'Rate limit exceeded.';

    private const MOM_COUNT_LIMIT = 5;
    public const MOM_LIMIT_EXCEEDED_MESSAGE = 'MOM limit exceeded.';

    public function __construct(Collection $fixture)
    {
        
    }

    public function getRateCountLimit(): int
    {
        return self::RATE_COUNT_LIMIT;
    }

    public function getMomCountLimit(): int
    {
        return self::MOM_COUNT_LIMIT;
    }
    
    /**
     * 評価可能な回数を超えているか判定する
     *
     * @return bool
     */
    public function exceedRateLimit(Player $player): bool
    {        
        return $player->rate_count > self::RATE_COUNT_LIMIT;
    }
        
    /**
     * 評価可能か判定する
     *
     * @return bool
     */
    public function canRate(Player $player): bool
    {
        return !$this->exceedPeriodDay() && !$this->exceedRateLimit($player);
    }
    
    /**
     * MOMを選択可能か判定する
     *
     * @return bool
     */
    public function canMom(Player $player): bool
    {
        return !$this->exceedPeriodDay()
            && !$this->exceedMomLimit()
            && !$player->mom;
    }
}