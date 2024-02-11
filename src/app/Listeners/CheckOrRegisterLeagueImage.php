<?php declare(strict_types=1);

namespace App\Listeners;

use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use App\Events\FixtureRegistered;
use App\Http\Controllers\Util\LeagueImageFile;
use App\UseCases\Api\ApiFootball\LeagueImage;


class CheckOrRegisterLeagueImage
{
    /**
     * Create the event listener.
     */
    public function __construct(
        private LeagueImageFile $file,
        private LeagueImage $leagueImage)
    {
        //
    }

    /**
     * Leagueの画像が無ければ登録する
     */
    public function handle(FixtureRegistered $event): void
    {
        try {
            $fixture = $event->model;

            if ($this->file->exists($fixture->external_league_id)) return;
            
            $this->leagueImage->register($fixture->external_league_id);

        } catch (Exception $e) {
            throw $e;
        }
    }
}