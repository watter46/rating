<?php declare(strict_types=1);

namespace App\Listeners;

use App\Events\FixtureRegistered;
use App\Http\Controllers\Util\TeamImageFile;
use App\UseCases\Api\ApiFootball\TeamImageFetcher;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CheckOrRegisterTeamImages
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
    public function handle(FixtureRegistered $event): void
    {
        $fixture = $event->model;

        $teamIds = $fixture->fixture['teams']->keyBy('id')->keys();

        foreach($teamIds as $teamId) {
            if ($this->file->exists($teamId)) {
                continue;
            }

            $teamImage = $this->teamImage->register($teamId);

            $this->file->write($teamId, $teamImage);
        }
    }
}
