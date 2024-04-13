<?php declare(strict_types=1);

namespace App\Livewire\User\Rating;


class RatingPresenter
{
    private array $player;
    private int $momCount;
    private int $momLimit;

    public function create(array $player, int $momLimit, int $momCount): self
    {
        $this->player = $player;
        $this->momLimit = $momLimit;
        $this->momCount = $momCount;

        return $this;
    }

    public function getRemainingRateCountRange(): array
    {
        $rateCount = $this->player['rate_count'];

        $remainingCount = $this->getRateLimit() - $rateCount;

        return $remainingCount ? range(1, $remainingCount) : [];
    }

    public function getRateCountRange(): array
    {
        $rateCount = $this->player['rate_count'];

        return $rateCount ? range(1, $rateCount) : [];
    }

    public function getRateLimit(): int
    {
        return $this->player['rateLimit'];
    }

    public function getRemainingMomCountRange(): array
    {
        $remainingMomCount = $this->getMomLimit() - $this->momCount;

        return $remainingMomCount ? range(1, $remainingMomCount) : [];
    }

    public function getMomCountRange(): array
    {
        return $this->momCount ? range(1, $this->momCount) : [];
    }

    public function getMomLimit(): int
    {
        return $this->momLimit;
    }
}