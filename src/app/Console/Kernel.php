<?php declare(strict_types=1);

namespace App\Console;

use App\Jobs\FetchFixture;
use Carbon\Carbon;
use DateTimeZone;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Cache;

use App\Models\Fixture;
use App\Models\Stub;
use Illuminate\Support\Facades\Redis;

class Kernel extends ConsoleKernel
{
    // Tournamentごとに処理を実行する時間を決める
    private const FIXTURE_START_DELAY_MINUTES = 130;
    
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('fixtures:update')
            ->withoutOverlapping()
            ->runInBackground()
            ->dailyAt('00:00');

        $schedule->command('players:update')
            ->withoutOverlapping()
            ->runInBackground()
            ->dailyAt('00:00');

        $schedule
            ->job(new FetchFixture)
            ->when(fn () => $this->shouldHandle())
            ->everyMinute();
    }
    
    /**
     * コマンドを実行するか判定する
     *
     * @return bool
     */
    private function shouldHandle(): bool
    {
        $cache = $this->cacheNextFixture();
        
        if (!$cache) {
            return false;
        }
        
        $nextDate = $cache->date->addMinutes(self::FIXTURE_START_DELAY_MINUTES);
        $handleTime = now('UTC');

        $parsed = fn ($date) => Carbon::parse($date->__toString());

        return $parsed($nextDate)->equalTo($parsed($handleTime));
    }
    
    /**
     * Fixtureをキャッシュから取得する
     *
     * @return ?Fixture
     */
    private function cacheNextFixture(): ?Fixture
    {
        return Cache::store('redis')
            ->rememberForever('nextFixture', function () {
                return Fixture::query()
                    ->select(['id', 'date'])
                    ->next()
                    ->first();
            });
    }

    /**
     * スケジュールされたイベントで使用するデフォルトのタイムゾーン取得
     */
    protected function scheduleTimezone(): DateTimeZone|string|null
    {
        return 'UTC';
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
