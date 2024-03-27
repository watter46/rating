<?php declare(strict_types=1);

namespace Tests\Unit\Api\SofaScore;

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;


class SofaScoreTest extends TestCase
{
    public function test_Playerを取得できるか()
    {
        Http::fake();

        $playerName = 'test';

        Http::withHeaders([
                'X-RapidAPI-Host' => config('sofa-score.api-host'),
                'X-RapidAPI-Key'  => config('sofa-score.api-key')
            ])
            ->retry(1, 500)
            ->get('https://sofascores.p.rapidapi.com/v1/search/multi', [
                'query' => $playerName,
                'group' => 'players'
            ]);

        Http::assertSent(function (Request $request) {
            return
                $request->hasHeader('X-RapidAPI-Host', config('sofa-score.api-host')) &&
                $request->hasHeader('X-RapidAPI-Key', config('sofa-score.api-key')) &&
                $request['query'] === 'test' &&
                $request['group'] === 'players' &&
                $request->method() === 'GET';
        });
    }

    public function test_PlayersOfTeamを取得できるか()
    {
        Http::fake();

        Http::withHeaders([
                'X-RapidAPI-Host' => config('sofa-score.api-host'),
                'X-RapidAPI-Key'  => config('sofa-score.api-key')
            ])
            ->retry(1, 500)
            ->get('https://sofascores.p.rapidapi.com/v1/teams/players', [
                'team_id' => (string) config('sofa-score.chelsea-id')
            ]);

        Http::assertSent(function (Request $request) {
            return
                $request->hasHeader('X-RapidAPI-Host', config('sofa-score.api-host')) &&
                $request->hasHeader('X-RapidAPI-Key', config('sofa-score.api-key')) &&
                $request['team_id'] === '38' &&
                $request->method() === 'GET';
        });
    }

    public function test_選手の画像が取得できるか()
    {
        Http::fake();

        $playerId = 11111;
        
        Http::withHeaders([
            'X-RapidAPI-Host' => config('sofa-score.api-host'),
            'X-RapidAPI-Key'  => config('sofa-score.api-key')
        ])
        ->retry(1, 500)
        ->get('https://sofascores.p.rapidapi.com/v1/players/photo', [
            'player_id' => (string) $playerId
        ]);

        Http::assertSent(function (Request $request) {
            return
                $request->hasHeader('X-RapidAPI-Host', config('sofa-score.api-host')) &&
                $request->hasHeader('X-RapidAPI-Key', config('sofa-score.api-key')) &&
                $request->method() === 'GET' && 
                $request['player_id'] === '11111';
        });
    }
}
