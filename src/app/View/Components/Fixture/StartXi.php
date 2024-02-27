<?php declare(strict_types=1);

namespace App\View\Components\Fixture;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class StartXi extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(public string $fixtureId, public array $startXi)
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.fixture.start-xi');
    }
}
