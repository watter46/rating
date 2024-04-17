<?php declare(strict_types=1);

namespace App\Livewire\User\Fixture;

use Livewire\Component;

class ToggleUserMachine extends Component
{
    public bool $isUser = true;
    
    public function render()
    {
        return view('livewire.user.fixture.toggle-user-machine');
    }

    public function updatedIsUser()
    {
        $this->dispatch('user-machine-toggled', $this->isUser);
    }
}
