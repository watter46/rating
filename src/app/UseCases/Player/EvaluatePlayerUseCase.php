<?php declare(strict_types=1);

namespace App\UseCases\Player;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Models\Player;


final readonly class EvaluatePlayerUseCase
{
    public function __construct(private Player $player)
    {
        //
    }

    public function execute(string $playerId, float $rating): Player
    {
        try {
            /** @var Player $player */
            $player = $this->player
                ->findOrFail($playerId)
                ->evaluate($rating);

            DB::transaction(function () use ($player) {
                $player->save();
            });

            return $player;

        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException('Playerが見つかりませんでした。');

        } catch (Exception $e) {
            throw $e;
        }
    }
}