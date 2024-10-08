<?php declare(strict_types=1);

namespace App\Providers;

use App\Events\FixtureInfoRegistered;
use App\Events\FixtureInfosRegistered;
use App\Events\PlayerInfosRegistered;
use App\Listeners\RegisterLeagueImage;
use App\Listeners\RegisterLineups;
use App\Listeners\RegisterPlayerImage;
use App\Listeners\RegisterPlayerInfos;
use App\Listeners\RegisterTeamImages;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;


class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        FixtureInfosRegistered::class => [
            RegisterTeamImages::class,
            RegisterLeagueImage::class
        ],

        FixtureInfoRegistered::class => [
            RegisterPlayerInfos::class,
            RegisterLineups::class,
            RegisterTeamImages::class,
            RegisterLeagueImage::class,
            RegisterPlayerImage::class
        ],

        PlayerInfosRegistered::class => [
            RegisterPlayerImage::class
        ]
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
