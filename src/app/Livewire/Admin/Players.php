<?php declare(strict_types=1);

namespace App\Livewire\Admin;

use Exception;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\Attributes\Validate;
use Illuminate\Support\Collection;

use App\Livewire\MessageType;
use App\UseCases\Admin\Player\FetchPlayerInfosUseCase;
use App\UseCases\Admin\Fixture\UpdateApiPlayerIds;
use App\UseCases\Admin\Fixture\UpdateFlashIds;


class Players extends Component
{
    #[Validate('required')]
    public string $refreshKey;
    
    private readonly FetchPlayerInfosUseCase $fetchPlayerInfos;
    private readonly PlayersPresenter $presenter;

    private const SUCCESS_MESSAGE = 'Please Reload!!';
    private const ERROR_MESSAGE   = 'Incorrect key';
    
    public function boot(FetchPlayerInfosUseCase $fetchPlayerInfos, PlayersPresenter $presenter)
    {
        $this->fetchPlayerInfos = $fetchPlayerInfos;
        $this->presenter = $presenter;
    }
    
    public function render()
    {
        return view('livewire.admin.players');
    }

    #[Computed()]
    public function players(): Collection
    {
        try {
            return $this->presenter->execute($this->fetchPlayerInfos->execute());

        } catch (Exception $e) {
            $this->dispatch('notify', message: MessageType::Error->toArray($e->getMessage()));
        }
    }

    public function refreshSquads(
        UpdateApiPlayerIds $updateApiPlayerIds,
        UpdateFlashIds $updateFlashIds)
    {
        try {
            if ($this->refreshKey !== config('refreshKey.key')) {
                throw new Exception(self::ERROR_MESSAGE);
            }

            $updateApiPlayerIds->execute();
            $updateFlashIds->execute();
    
            $this->dispatch('notify', message: MessageType::Success->toArray(self::SUCCESS_MESSAGE));
            $this->dispatch('close-players-modal');

        } catch (Exception $e) {
            $this->dispatch('notify', message: MessageType::Error->toArray($e->getMessage()));
        }
    }
}
