<?php declare(strict_types=1);

namespace App\UseCases\Admin\Data\ApiFootball\FixtureData;


enum FixtureStatusType: string
{
    case NotStarted = 'Not Started';
    case MatchFinished = 'Match Finished';
    case MatchPostponed = 'Match Postponed';

    case OtherStatus = 'Other Status';

    public function isFinished(): bool
    {   if ($this === self::OtherStatus) {
            return false;
        }
        
        return $this === self::MatchFinished;
    }
}
