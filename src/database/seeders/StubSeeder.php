<?php

namespace Database\Seeders;

use App\Models\Stub;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class StubSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Redis::flushdb();
        Cache::flush();

        foreach(range(1,5) as $i) {
            Stub::create([
                'date' => now('UTC')->subMinutes(130)->addMinutes($i)->addMinute()->second(0),
                'fixture' => null
            ]);
        }
    }
}
