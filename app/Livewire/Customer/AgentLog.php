<?php

namespace App\Livewire\Customer;

use Livewire\Component;

class AgentLog extends Component
{
    public AgentLog $agentLog;

    public function mount(AgentLog $agentLog): void
    {
        $this->agentLog = $agentLog;
    }

    public function render()
    {
        return view('livewire.customer.agent-log');
    }
}
