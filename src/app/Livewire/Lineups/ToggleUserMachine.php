<?php declare(strict_types=1);

namespace App\Livewire\Lineups;

use Livewire\Component;

class ToggleUserMachine extends Component
{
    public bool $isUser = true;
    
    public function render()
    {
        return view('livewire.lineups.toggle-user-machine');
    }

    public function updatedIsUser()
    {
        $this->dispatch('user-machine-toggled', $this->isUser);
    }
}
