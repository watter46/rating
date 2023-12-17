<?php declare(strict_types=1);

namespace App\UseCases\Player;

use Exception;
use Illuminate\Support\Facades\DB;

use App\Models\Lineup;
use App\UseCases\Player\Util\ApiFootballFetcher;


final readonly class RegisterLineupUseCase
{
    public function __construct(private Lineup $lineup)
    {
        //
    }

    public function execute(int $fixtureId)
    {
        try {
            if ($this->lineup->where('fixture_id', $fixtureId)->exists()) {
                return;
            }
            
            $json = ApiFootballFetcher::lineup($fixtureId)->fetch();

            $decoded = json_decode($json);

            $lineup = $this
                ->lineup
                ->setLineup(
                    fixture_id: $fixtureId,
                    lineup: json_encode($decoded[0]->startXI)
                );

            DB::transaction(function () use ($lineup) {
                $lineup->save();
            });

        } catch (Exception $e) {
            throw $e;
        }
    }
}