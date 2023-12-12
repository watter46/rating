<?php declare(strict_types=1);

namespace App\Http\Controllers;

enum PositionType: string
{
    case FW  = 'Attacker';
    case MID = 'Midfielder';
    case DEF = 'Defender';
    CASE GK  = 'Goalkeeper';
}
