<?php declare(strict_types=1);

namespace App\Listeners;

use App\Events\FixtureRegistered;
use App\Events\FixturesRegistered;
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
    public function handle(FixturesRegistered|FixtureRegistered $event): void
    {
        $invalidTeamIds = $event->data->validated()->getInvalidTeamIds();

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
