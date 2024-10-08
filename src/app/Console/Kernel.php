<?php declare(strict_types=1);

namespace App\Console;

use Carbon\Carbon;
use DateTimeZone;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Cache;

use App\Models\FixtureInfo;
use App\Jobs\FetchFixtureInfo;
use App\Jobs\UpdateFixtureInfos;
use App\Jobs\UpdatePlayerInfos;
use App\Jobs\UpdateUsersRating;


class Kernel extends ConsoleKernel
{
    // Tournamentごとに処理を実行する時間を決める
    private const FIXTURE_START_DELAY_MINUTES = 130;
    
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->job(new UpdateFixtureInfos)->dailyAt('00:00');
        $schedule->job(new UpdatePlayerInfos)->dailyAt('00:00');
        $schedule->job(new UpdateUsersRating)->dailyAt('00:00');
            
        $schedule
            ->job(new FetchFixtureInfo)
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
        $cache = $this->cacheNextFixtureInfo();
        
        if (!$cache) {
            return false;
        }
        
        $nextDate = Carbon::parse($cache->date)->addMinutes(self::FIXTURE_START_DELAY_MINUTES);
        $handleTime = now('UTC');

        $parsed = fn ($date) => Carbon::parse($date->__toString());

        return $parsed($nextDate)->equalTo($parsed($handleTime));
    }
    
    /**
     * Fixtureをキャッシュから取得する
     *
     * @return ?FixtureInfo
     */
    private function cacheNextFixtureInfo(): ?FixtureInfo
    {
        return Cache::store('redis')
            ->rememberForever('nextFixtureInfo', function () {
                return FixtureInfo::query()
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
