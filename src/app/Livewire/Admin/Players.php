<?php declare(strict_types=1);

namespace App\Livewire\Admin;

use Exception;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\Attributes\Validate;

use App\UseCases\Player\FetchPlayerInfoListUseCase;
use App\UseCases\Player\RegisterPlayerOfTeamUseCase;


class Players extends Component
{
    #[Validate('required')]
    public string $refreshKey;
    
    private readonly FetchPlayerInfoListUseCase $fetchPlayerInfoList;

    private const SUCCESS_MESSAGE = 'Please Reload!!';
    private const ERROR_MESSAGE   = 'Incorrect key';
    
    public function boot(FetchPlayerInfoListUseCase $fetchPlayerInfoList)
    {
        $this->fetchPlayerInfoList = $fetchPlayerInfoList;
    }
    
    public function render()
    {
        return view('livewire.admin.players');
    }

    #[Computed()]
    public function players()
    {
        try {
            return $this->fetchPlayerInfoList->execute();

        } catch (Exception $e) {

        }
    }

    public function refreshSquads(RegisterPlayerOfTeamUseCase $registerPlayerOfTeam)
    {
        try {
            if ($this->refreshKey !== config('refreshKey.key')) {
                throw new Exception;
            }

            $registerPlayerOfTeam->execute();
    
            $this->dispatch('notify', message: self::SUCCESS_MESSAGE);
            $this->dispatch('close-fixture-modal');

        } catch (Exception $e) {
            $this->dispatch('notify', message: self::ERROR_MESSAGE);
        }
    }
}
