<?php declare(strict_types=1);

namespace App\UseCases\Admin\Fixture\Accessors;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

use App\UseCases\Admin\Data\ApiFootball\FixtureData\FixtureStatusType;
use App\UseCases\Util\Season;

class Fixture
{
    private function __construct(private Collection $fixture)
    {
        
    }

    public static function create(Collection $data)
    {
        $fixture = $data->dataGet('fixture');
        
        $status = FixtureStatusType::tryFrom($fixture->dataGet('status.long', false)) ?? FixtureStatusType::OtherStatus;
        
        $winner = $data
            ->dataGet('teams')
            ->filter(fn($team) => $team['id'] === config('api-football.chelsea-id'))
            ->first()['winner'];
        
        return new self(collect([
            'id'             => $fixture['id'],
            'first_half_at'  => Carbon::parse($fixture['date'], 'UTC'),
            'second_half_at' => Carbon::parse($fixture->dataGet('periods.second', false), 'UTC'),
            'is_end'         => $status->isFinished(),
            'winner'         => $winner
        ]));
    }

    public static function reconstruct(Collection $fixture): self
    {
        return new self($fixture);
    }

    public function toModel(): Collection
    {
        return $this->fixture;
    }

    public function getFixtureId(): int
    {
        return $this->fixture->get('id');
    }

    public function getSeason(): int
    {
        return Season::fromDate($this->getDate());
    }

    public function getDate(): Carbon
    {
        return Carbon::parse($this->fixture->get('first_half_at'));
    }

    public function isEnd(): bool
    {
        return $this->fixture->get('is_end');
    }
}