<?php declare(strict_types=1);

namespace App\UseCases\Player\Util;

use GuzzleHttp\Client;


final readonly class FootApiFetcher
{
    private function __construct(private readonly string $url)
    {
        //
    }

    public function fetch(): string
    {
        $client = new Client();

        $response = $client->request('GET', $this->url, [
            'headers' => [
                'X-RapidAPI-Host' => config('foot-api.api-host'),
                'X-RapidAPI-Key'  => config('foot-api.api-key')
            ],
        ]);

        return $response->getBody()->getContents();
    }

    // team -> imageで取得できるか調べる
    
    public static function playerImage(string $playerId): self
    {
        return new self(
            url: "https://footapi7.p.rapidapi.com/api/player/$playerId/image"
        );
    }
}