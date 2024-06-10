<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

use App\Models\FixtureInfo;
use App\UseCases\Admin\Player\UpdateRatingAverage\UpdateRatingAverage;


class UpdateUsersRating extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users-rating:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(UpdateRatingAverage $updateRatingAverage)
    {
        if (!$this->shouldHandle()) return;

        $updateRatingAverage->execute($this->getLastFixtureInfo()->id);
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
