<?php

namespace App\Livewire\Frontend;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class LandingPage extends Component
{
    public function mount(): void
    {
        //
    }
    public function render(): View|Factory|\Illuminate\View\View
    {
        return view('livewire.frontend.landing-page')
            ->layout('components.layouts.frontend');
    }
}
