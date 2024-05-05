<?php declare(strict_types=1);

namespace App\UseCases\User;

use Illuminate\Support\Carbon;

use App\Models\Fixture;
use App\Models\Player;


class FixtureValidator
{
    private const RATE_PERIOD_DAY = 50;
    public const RATE_PERIOD_EXPIRED_MESSAGE = 'Rate period has expired.';

    private const RATE_COUNT_LIMIT = 3;
    public const RATE_LIMIT_EXCEEDED_MESSAGE = 'Rate limit exceeded.';

    private const MOM_COUNT_LIMIT = 5;
    public const MOM_LIMIT_EXCEEDED_MESSAGE = 'MOM limit exceeded.';
    
    public function __construct(private Fixture $fixture)
    {
        
    }

    public function validate(Fixture $fixture): self
    {
        return new self($fixture);
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
        return $player->rate_count >= self::RATE_COUNT_LIMIT;
    }
    
    /**
     * MOMを選択できる回数を超えているか判定する
     *
     * @return bool
     */
    public function exceedMomLimit(): bool
    {
        return $this->fixture->mom_count >= self::MOM_COUNT_LIMIT;
    }
    
    /**
     * 評価可能期間を超えている判定する
     *
     * @return bool
     */
    public function exceedPeriodDay(): bool
    {
        $specifiedDate = Carbon::parse($this->fixture->fixtureInfo->date);
        
        return $specifiedDate->diffInDays(now('UTC')) > self::RATE_PERIOD_DAY;
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