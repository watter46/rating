<?php declare(strict_types=1);

namespace App\Livewire\User\Lineups;

use App\Livewire\User\Rating\Rating;
use App\UseCases\Admin\Fixture\FetchMomCount;
use App\UseCases\Admin\Fixture\FetchMomCountUseCase;
use App\UseCases\User\PlayerInFixtureRequest;
use Livewire\Attributes\On;


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
        ['momLimit' => $this->momLimit, 'mom_count' => $this->momCount] = $this->fetchMomCount();
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
