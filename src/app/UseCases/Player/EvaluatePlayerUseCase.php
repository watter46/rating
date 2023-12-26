<?php declare(strict_types=1);

namespace App\UseCases\Player;

use App\Models\Fixture;
use App\Models\Rating;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;


final readonly class EvaluatePlayerUseCase
{
    public function __construct(private Fixture $fixture, private Rating $rating)
    {
        //
    }

    public function execute(string $fixtureId, int $playerId, float $rating)
    {
        try {
            /** @var Fixture $fixture */
            $fixture = $this->fixture
                ->with([
                    'ratings' => fn($query) => $query->player($playerId)
                ])
                ->findOrFail($fixtureId);

            $rating = $this->evaluate($fixture, $playerId, $rating);

            DB::transaction(function () use ($rating) {
                $rating->save();
            });

        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException('Fixtureが見つかりませんでした。');

        } catch (Exception $e) {
            throw $e;
        }
    }

    private function evaluate(Fixture $fixture, int $playerId, float $rating)
    {
        if ($fixture->ratings->isEmpty()) {
            $rating = $this->rating->evaluate($playerId, $rating);

            $rating->fixture()->associate($fixture);

            return $rating;
        }

        return $fixture->ratings
            ->first()
            ->evaluate($playerId, $rating);
    }
}