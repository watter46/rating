<?php declare(strict_types=1);

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

use App\Models\FixtureInfo;
use App\UseCases\Admin\Player\UpdateUsersRating as UpdateUsersRatingUC;


class UpdateUsersRating implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(UpdateUsersRatingUC $updateUsersRating)
    {
        if (!$this->shouldHandle()) return;

        $updateUsersRating->execute($this->getLastFixtureInfo()->id);
    }
    
    /**
     * 試合が終わってから24時間～5日までの範囲か判定する
     *
     * @return bool
     */
    private function shouldHandle(): bool
    {
        $fixtureInfo = $this->getLastFixtureInfo();

        if (!$fixtureInfo) {
            return false;
        }

        $now = Carbon::now();

        return $now->between(...$this->getDateInRange($fixtureInfo->date));
    }
    
    /**
     * getDateInRange
     * 
     * @param string $date
     * @return array<Carbon>
     */
    private function getDateInRange(string $date): array
    {
        $date = Carbon::parse($date);

        $start = $date->copy()->addHours(24);
        $end = $date->copy()->addDays(5);
        
        return [$start, $end];
    }

    private function getLastFixtureInfo(): ?FixtureInfo
    {
        return Cache::store('redis')
            ->rememberForever('lastFixtureInfo', function () {
                return FixtureInfo::query()
                    ->select(['id', 'date'])
                    ->last()
                    ->first();
            });
    }
}
