<?php declare(strict_types=1);

namespace App\View\Components\Result;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Score extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public $fixtureData,
        public $teamsData,
        public $scoreData)
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.result.score');
    }
}
