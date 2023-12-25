<?php

namespace App\View\Components\Score;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Score extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public $fixture,
        public $teams,
        public $league,
        public $score)
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.score.score');
    }
}
