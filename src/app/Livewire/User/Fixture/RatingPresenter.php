<?php declare(strict_types=1);

namespace App\Livewire\User\Fixture;


class RatingPresenter
{
    private int $rateCount;
    private int $rateLimit;
    private int $momCount;
    private int $momLimit;

    public function create(int $rateCount, int $rateLimit, int $momLimit, int $momCount): self
    {
        $this->rateCount = $rateCount;
        $this->rateLimit = $rateLimit;
        $this->momCount = $momCount;
        $this->momLimit = $momLimit;

        return $this;
    }

    public function getRemainingRateCountRange(): array
    {
        $remainingCount = $this->getRateLimit() - $this->rateCount;

        return $remainingCount ? range(1, $remainingCount) : [];
    }

    public function getRateCountRange(): array
    {
        return $this->rateCount ? range(1, $this->rateCount) : [];
    }

    public function getRateLimit(): int
    {
        return $this->rateLimit;
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