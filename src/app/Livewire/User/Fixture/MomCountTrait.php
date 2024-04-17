<?php declare(strict_types=1);

namespace App\Livewire\User\Fixture;

use Livewire\Attributes\On;

use App\UseCases\User\Player\FetchMomCountUseCase;
use App\UseCases\User\PlayerInFixtureRequest;


trait MomCountTrait
{
    public int $momLimit;
    public int $momCount;

    private readonly FetchMomCountUseCase $fetchMomCount;

    public function bootMomCountTrait(FetchMomCountUseCase $fetchMomCount)
    {
        $this->fetchMomCount = $fetchMomCount;
    }

    public function mountMomCountTrait()
    {
        $this->updateMomCount();
    }
    
    #[On('mom-count-updated')]
    public function updateMomCount()
    {
        ['momLimit' => $this->momLimit, 'mom_count' => $this->momCount, 'exceedMomLimit' => $exceedMomLimit]
            = $this->fetchMomCount();

        if ($exceedMomLimit) {
            $this->dispatch('mom-button-disabled');
        }
    }

    public function dispatchMomCount(): void
    {
        $this->dispatch('mom-count-updated');
    }

    public function fetchMomCount()
    {
        $request = PlayerInFixtureRequest::make(fixtureId: $this->fixtureId);

        return $this->fetchMomCount->execute($request);
    }
}
