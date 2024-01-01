<?php declare(strict_types=1);

namespace App\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;

class Message extends Component
{
    public string $message;
    
    public function render()
    {
        return view('livewire.message');
    }
}
