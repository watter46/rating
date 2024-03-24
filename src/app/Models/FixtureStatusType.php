<?php declare(strict_types=1);

namespace App\Models;

enum FixtureStatusType: string
{
    case NotStarted = 'Not Started';
    case MatchFinished = 'Match Finished';
    case MatchPostponed = 'Match Postponed';

    public function isFinished(): bool
    {
        return $this === self::MatchFinished;
    }
}
