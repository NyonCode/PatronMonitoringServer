<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

class AgentLog extends Component
{
    public AgentLog $agentLog;

    public function mount(AgentLog $agentLog): void
    {
        dump($agentLog);
        $this->agentLog = $agentLog;
    }

    public function render(): View|Factory|\Illuminate\View\View
    {
        return view('livewire.customer.agent-log');
    }
}
