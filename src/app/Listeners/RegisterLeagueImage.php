<?php declare(strict_types=1);

namespace App\Listeners;

use App\Events\FixtureInfoRegistered;
use App\Events\FixtureInfosRegistered;
use App\Http\Controllers\Util\LeagueImageFile;
use App\UseCases\Admin\ApiFootballRepositoryInterface;


class RegisterLeagueImage
{
    /**
     * Create the event listener.
     */
    public function __construct(private LeagueImageFile $file, private ApiFootballRepositoryInterface $repository)
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(FixtureInfosRegistered|FixtureInfoRegistered $event): void
    {
        $invalidLeagueIds = $event->data->validated()->getInvalidLeagueIds();

        if ($invalidLeagueIds->isEmpty()) return;

        foreach($invalidLeagueIds as $leagueId) {
            if ($this->file->exists($leagueId)) {
                continue;
            }

            $leagueImage = $this->repository->fetchLeagueImage($leagueId);

            $this->file->write($leagueId, $leagueImage);
        }
    }
}
