<?php declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Livewire\MessageType;
use App\UseCases\Admin\Player\FetchPlayerInfoUseCase;
use App\UseCases\Admin\Player\UpdatePlayerImageUseCase;
use Exception;
use Livewire\Component;

class Player extends Component
{
    public string $playerInfoId;
    public string $refreshKey;

    private const SUCCESS_MESSAGE = 'Please Reload!!';
    private const ERROR_MESSAGE   = 'Incorrect key';

    private FetchPlayerInfoUseCase $fetchPlayerInfo;
    
    public function boot(FetchPlayerInfoUseCase $fetchPlayerInfo)
    {
        $this->fetchPlayerInfo = $fetchPlayerInfo;
    }

    public function render()
    {
        return view('livewire.admin.player', [
            'player' => $this->fetchPlayerInfo->execute($this->playerInfoId)
        ]);
    }

    public function updateImage(UpdatePlayerImageUseCase $updatePlayerImage): void
    {
        try {
            if ($this->refreshKey !== config('refreshKey.key')) {
                throw new Exception(self::ERROR_MESSAGE);
            }

            $updatePlayerImage->execute($this->playerInfoId);
    
            $this->dispatch('notify', message: MessageType::Success->toArray(self::SUCCESS_MESSAGE));
            $this->dispatch('close');

        } catch (Exception $e) {
            $this->dispatch('notify', message: MessageType::Error->toArray($e->getMessage()));
        }
    }
}
