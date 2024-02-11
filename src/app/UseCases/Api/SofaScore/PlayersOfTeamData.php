<?php declare(strict_types=1);

namespace App\UseCases\Api\SofaScore;

use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Collection;

use App\Http\Controllers\Util\PlayerOfTeamFile;


class PlayersOfTeamData
{
    private const CHELSEA_ID = 38;

    public function __construct(private PlayerOfTeamFile $file)
    {
        //
    }

    public function fetch(): Collection
    {
        try {            
            $client = new Client();

            $response = $client->request('GET', 'https://sofascores.p.rapidapi.com/v1/teams/players', [
                'query' => [
                    'team_id' => (string) self::CHELSEA_ID
                ],
                'delay' => 500,
                'headers' => [
                    'X-RapidAPI-Host' => config('sofa-score.api-host'),
                    'X-RapidAPI-Key'  => config('sofa-score.api-key')
                ]
            ]);

            $json = $response->getBody()->getContents();

            return $this->parse($json);

          } catch (Exception $e) {
            throw $e;
        }
    }

    public function fetchOrGetFile(): Collection
    {
        try {            
            if ($this->file->exists()) {
                return $this->file->get();
            }

            $playersOfTeamData = $this->fetch();

            $this->file->write($playersOfTeamData);
            
            return $playersOfTeamData;

        } catch (Exception $e) {
            throw $e;
        }
    }

    private function parse(string $json): Collection
    {
        $decoded = json_decode($json)->data;

        return collect($decoded);
    }
}