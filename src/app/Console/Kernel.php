<?php declare(strict_types=1);

namespace App\Console;

use App\Models\Fixture;
use Carbon\Carbon;
use DateTimeZone;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Cache;


class Kernel extends ConsoleKernel
{    
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('fixtures:update')->withoutOverlapping()->runInBackground()->dailyAt('00:00');
        $schedule->command('players:update')->withoutOverlapping()->runInBackground()->dailyAt('00:00');

        $schedule
            ->command('fixture:fetch')
            ->when(fn () => $this->shouldHandle())
            ->everyMinute()
            ->after(function () {
                Cache::forget('nextFixture');
            }); 
    }
    
    /**
     * コマンドを実行するか判定する
     *
     * @return bool
     */
    private function shouldHandle(): bool
    {
        if (!$this->cacheNextFixture()) {
            return false;
        }

        $nextDate = Carbon::parse(Carbon::parse($this->cacheNextFixture()->date)->__toString(), 'UTC');
        $now = Carbon::parse(now('UTC')->__toString());
                 
        return $nextDate->equalTo($now);
    }
    
    /**
     * Fixtureをキャッシュから取得する
     *
     * @return ?Fixture
     */
    private function cacheNextFixture(): ?Fixture
    {
        return Cache::rememberForever('nextFixture', function () {
            return Fixture::select(['id', 'fixture', 'date'])->next()->first();
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
