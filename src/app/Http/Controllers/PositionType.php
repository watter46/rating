<?php declare(strict_types=1);

namespace App\Http\Controllers;

enum PositionType: string
{
    case FW  = 'F';
    case MID = 'M';
    case DEF = 'D';
    CASE GK  = 'G';
}
