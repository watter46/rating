<?php declare(strict_types=1);

namespace App\UseCases\Admin\Data\ApiFootball\FixtureData;


enum PositionType: string
{
    case FW  = 'F';
    case MID = 'M';
    case DEF = 'D';
    CASE GK  = 'G';
}