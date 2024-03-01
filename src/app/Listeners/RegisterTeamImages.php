<?php declare(strict_types=1);

namespace App\Listeners;

use App\Events\FixturesRegistered;
use App\Http\Controllers\Util\TeamImageFile;
use App\UseCases\Api\ApiFootball\TeamImageFetcher;


class RegisterTeamImages
{
    /**
     * Create the event listener.
     */
    public function __construct(private TeamImageFile $file, private TeamImageFetcher $teamImage)
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(FixturesRegistered $event): void
    {
        $invalidTeamIds = $event->processor->getInvalidTeamIds();

        foreach($invalidTeamIds as $teamId) {
            if ($this->file->exists($teamId)) {
                continue;
            }

            $teamImage = $this->teamImage->fetch($teamId);

            $this->file->write($teamId, $teamImage);
        }
    }
}
