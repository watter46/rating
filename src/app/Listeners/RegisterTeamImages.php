<?php declare(strict_types=1);

namespace App\Listeners;

use App\Events\FixtureInfoRegistered;
use App\Events\FixtureInfosRegistered;
use App\Http\Controllers\Util\TeamImageFile;
use App\UseCases\Admin\ApiFootballRepositoryInterface;


class RegisterTeamImages
{
    public function __construct(
        private TeamImageFile $file,
        private ApiFootballRepositoryInterface $repository)
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(FixtureInfosRegistered|FixtureInfoRegistered $event): void
    {
        $fixture = match (true) {
            $event instanceof FixtureInfosRegistered => $event->fixtureInfos,
            $event instanceof FixtureInfoRegistered  => $event->fixtureInfo,
        };
        
        $invalidTeamImageIds = $fixture->getInvalidTeamImageIds();
        
        if ($invalidTeamImageIds->isEmpty()) {
            return;
        }
        
        $invalidTeamImageIds
            ->each(function ($teamId) {
                if ($this->file->exists($teamId)) {
                    return true;
                }
    
                $teamImage = $this->repository->fetchTeamImage($teamId);
                
                $this->file->write($teamId, $teamImage);
            });
    }
}
