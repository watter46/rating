<?php declare(strict_types=1);

namespace App\Livewire;

use Illuminate\Support\Collection;
use Livewire\Component;


class Fixture extends Component
{
    public Collection $fixture;
    
    public function render()
    {
        return view('livewire.fixture');
    }

    public function toFixture()
    {
        $fixtureId = $this->fixture['fixture']['id'];
        
        return $this->redirect("/fixtures/$fixtureId");
    }
}
