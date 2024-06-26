<?php declare(strict_types=1);

namespace App\Livewire\Admin;

use Exception;
use Livewire\Component;

use App\Livewire\MessageType;
use App\Models\PlayerInfo;
use App\UseCases\Admin\Player\UpdatePlayerImageUseCase;


class Player extends Component
{
    public PlayerInfo $playerInfo;
    public string $refreshKey;

    private const SUCCESS_MESSAGE = 'Please Reload!!';
    private const ERROR_MESSAGE   = 'Incorrect key';

    public function render()
    {
        return view('livewire.admin.player');
    }

    public function updateImage(UpdatePlayerImageUseCase $updatePlayerImage): void
    {
        try {
            if ($this->refreshKey !== config('refreshKey.key')) {
                throw new Exception(self::ERROR_MESSAGE);
            }

            $updatePlayerImage->execute($this->playerInfo->id);
    
            $this->dispatch('notify', message: MessageType::Success->toArray(self::SUCCESS_MESSAGE));
            $this->dispatch('close');

        } catch (Exception $e) {
            $this->dispatch('notify', message: MessageType::Error->toArray($e->getMessage()));
        }
    }
}
