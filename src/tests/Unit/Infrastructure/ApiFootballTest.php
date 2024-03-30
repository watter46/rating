<?php declare(strict_types=1);

namespace Tests\Unit\Infrastructure;

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;


class ApiFootballTest extends TestCase
{
    public function test_Fixtureが取得できるか()
    {
        Http::fake();

        Http::withHeaders([
                'X-RapidAPI-Host' => config('api-football.api-host'),
                'X-RapidAPI-Key'  => config('api-football.api-key')
            ])
            ->retry(1, 500)
            ->get('https://api-football-v1.p.rapidapi.com/v3/fixtures', [
                'id' => 11111
            ]);

        Http::assertSent(function (Request $request) {
            return
                $request->hasHeader('X-RapidAPI-Host', config('api-football.api-host')) &&
                $request->hasHeader('X-RapidAPI-Key', config('api-football.api-key')) &&
                $request['id'] === 11111 &&
                $request->method() === 'GET';
        });
    }

    public function test_Fixturesが取得できるか()
    {
        Http::fake();

        Http::withHeaders([
                'X-RapidAPI-Host' => config('api-football.api-host'),
                'X-RapidAPI-Key'  => config('api-football.api-key')
            ])
            ->retry(1, 500)
            ->get('https://api-football-v1.p.rapidapi.com/v3/fixtures', [
                'season' => 2024,
                'team'   => 49
            ]);

        Http::assertSent(function (Request $request) {
            return
                $request->hasHeader('X-RapidAPI-Host', config('api-football.api-host')) &&
                $request->hasHeader('X-RapidAPI-Key', config('api-football.api-key')) &&
                $request['season'] === 2024 &&
                $request['team'] === 49 &&
                $request->method() === 'GET';
        });
    }

    public function test_Squadsが取得できるか()
    {
        Http::fake();
        
        Http::withHeaders([
            'X-RapidAPI-Host' => config('api-football.api-host'),
            'X-RapidAPI-Key'  => config('api-football.api-key')
        ])
        ->retry(1, 500)
        ->get('https://api-football-v1.p.rapidapi.com/v3/players/squads', [
            'team' => config('api-football.chelsea-id')
        ]);

        Http::assertSent(function (Request $request) {
            return
                $request->hasHeader('X-RapidAPI-Host', config('api-football.api-host')) &&
                $request->hasHeader('X-RapidAPI-Key', config('api-football.api-key')) &&
                $request['team'] === 49 &&
                $request->method() === 'GET';
        });
    }

    public function test_リーグ画像が取得できるか()
    {
        Http::fake();

        $leagueId = 11111;
        
        Http::withHeaders([
            'X-RapidAPI-Host' => config('api-football.api-host'),
            'X-RapidAPI-Key'  => config('api-football.api-key')
        ])
        ->retry(1, 500)
        ->get("https://media-4.api-sports.io/football/leagues/$leagueId.png");

        Http::assertSent(function (Request $request) {
            return
                $request->hasHeader('X-RapidAPI-Host', config('api-football.api-host')) &&
                $request->hasHeader('X-RapidAPI-Key', config('api-football.api-key')) &&
                $request->method() === 'GET' &&
                $request->url("https://media-4.api-sports.io/football/leagues/11111.png");
        });
    }

    public function test_チーム画像が取得できるか()
    {
        Http::fake();

        $teamId = 11111;
        
        Http::withHeaders([
            'X-RapidAPI-Host' => config('api-football.api-host'),
            'X-RapidAPI-Key'  => config('api-football.api-key')
        ])
        ->retry(1, 500)
        ->get("https://media-4.api-sports.io/football/teams/$teamId.png");

        Http::assertSent(function (Request $request) {
            return
                $request->hasHeader('X-RapidAPI-Host', config('api-football.api-host')) &&
                $request->hasHeader('X-RapidAPI-Key', config('api-football.api-key')) &&
                $request->method() === 'GET' &&
                $request->url("https://media-4.api-sports.io/football/teams/11111.png");
        });
    }
}