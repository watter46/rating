<?php declare(strict_types=1);

namespace App\Livewire\User\Fixture;

use Livewire\Component;

class ToggleUserMachine extends Component
{
    public bool $isUser = true;
    
    private const DEFAULT_STATE = 'my';
    
    public $toggleStates = self::DEFAULT_STATE;
    
    public function render()
    {
        return view('livewire.user.fixture.toggle-user-machine');
    }

    public function updatedIsUser()
    {
        $this->dispatch('user-machine-toggled', $this->isUser);
    }

    public function updatedToggleStates(string $state)
    {
        $this->dispatch('toggle-states-updated', state: $state);
    }
}
