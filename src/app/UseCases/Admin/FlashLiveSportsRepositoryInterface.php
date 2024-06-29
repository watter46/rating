<?php declare(strict_types=1);

namespace App\UseCases\Admin;

use App\UseCases\Admin\Data\FlashLiveSports\TeamSquad;


interface FlashLiveSportsRepositoryInterface
{
    public function fetchTeamSquad(): TeamSquad;
}