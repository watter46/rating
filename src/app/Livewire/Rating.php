<?php declare(strict_types=1);

namespace App\Livewire;

use Livewire\Component;

class Rating extends Component
{
    public string $modelId;
    public int $playerId;
    public float $rating;

    public function render()
    {
        return view('livewire.rating');
    }
}
