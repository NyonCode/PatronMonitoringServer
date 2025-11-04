<?php

namespace App\Livewire\Frontend;

use App\Models\AgentLog;
use Livewire\Component;

class LandingPage extends Component
{
    public function render()
    {
        return view('frontend.landing-page')
            ->layout('layouts.frontend');
    }
}
