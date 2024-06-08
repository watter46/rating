<?php declare(strict_types=1);

namespace App\Livewire\User\Result;

use App\Livewire\User\Fixture\PlayerTrait;
use App\Models\Player;
use Livewire\Component;


class RatedPlayer extends Component
{
    public string $name;
    public string $size;
    
    public array $player;

    use PlayerTrait;

    public function render()
    {
        return view('livewire.user.result.rated-player');
    }
}