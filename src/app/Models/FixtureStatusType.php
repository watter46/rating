<?php

namespace App\Models;

enum FixtureStatusType: string
{
    case NotStarted = 'Not Started';
    case MatchFinished = 'Match Finished';
    case MatchPostponed = 'Match Postponed';
}
