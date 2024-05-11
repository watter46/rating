<?php declare(strict_types=1);

namespace App\Livewire\Admin;

use Exception;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\Attributes\Validate;
use Illuminate\Support\Collection;

use App\Livewire\MessageType;
use App\UseCases\Admin\Player\FetchPlayerInfosUseCase;
use App\UseCases\Admin\Player\UpdatePlayerInfos;


class Players extends Component
{
    #[Validate('required')]
    public string $refreshKey;
    
    private readonly FetchPlayerInfosUseCase $fetchPlayerInfos;

    private const SUCCESS_MESSAGE = 'Please Reload!!';
    private const ERROR_MESSAGE   = 'Incorrect key';
    
    public function boot(FetchPlayerInfosUseCase $fetchPlayerInfos)
    {
        $this->fetchPlayerInfos = $fetchPlayerInfos;
    }
    
    public function render()
    {
        return view('livewire.admin.players');
    }

    #[Computed()]
    public function players(): Collection
    {
        try {
            return $this->fetchPlayerInfos->execute();

        } catch (Exception $e) {
            $this->dispatch('notify', message: MessageType::Error->toArray($e->getMessage()));
        }
    }

    public function refreshSquads(UpdatePlayerInfos $updatePlayerInfos)
    {
        try {
            if ($this->refreshKey !== config('refreshKey.key')) {
                throw new Exception(self::ERROR_MESSAGE);
            }

            $updatePlayerInfos->execute();
    
            $this->dispatch('notify', message: MessageType::Success->toArray(self::SUCCESS_MESSAGE));
            $this->dispatch('close-players-modal');

        } catch (Exception $e) {
            $this->dispatch('notify', message: MessageType::Error->toArray($e->getMessage()));
        }
    }
}
