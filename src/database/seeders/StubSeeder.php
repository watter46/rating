<?php

namespace Database\Seeders;

use App\Models\Stub;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;

class StubSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Cache::forget('nextFixture');

        foreach(range(1,5) as $i) {
            Stub::create([
                'date' => now('UTC')->subMinutes(130)->addMinutes($i)->second(0),
                'fixture' => null
            ]);
        }
    }
}
