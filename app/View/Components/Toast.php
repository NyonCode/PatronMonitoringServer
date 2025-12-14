<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Toast extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(public string $position = 'bottom end')
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @render View|Closure|string
     */
    public function render(): View|Closure|string
    {
        return view('components.toast.toast');
    }
}
