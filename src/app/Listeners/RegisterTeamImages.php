<?php declare(strict_types=1);

namespace App\Listeners;

use App\Events\FixtureInfoRegistered;
use App\Events\FixtureInfosRegistered;
use App\Http\Controllers\Util\TeamImageFile;
use App\UseCases\Admin\ApiFootballRepositoryInterface;


class RegisterTeamImages
{
    /**
     * Create the event listener.
     */
    public function __construct(private TeamImageFile $file, private ApiFootballRepositoryInterface $repository)
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(FixtureInfosRegistered|FixtureInfoRegistered $event): void
    {
        $invalidTeamIds = $event->builder->getInvalidTeamImageIds();
        
        if ($invalidTeamIds->isEmpty()) return;
        
        foreach($invalidTeamIds as $teamId) {
            if ($this->file->exists($teamId)) {
                continue;
            }

            $teamImage = $this->repository->fetchTeamImage($teamId);

            $this->file->write($teamId, $teamImage);
        }
    }
}
