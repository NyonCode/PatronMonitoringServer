<?php

namespace App\Livewire\Customer;

use App\Models\Agent;
use App\Models\AgentLog as ModelsAgentLog;
use Livewire\Component;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

class AgentLog extends Component
{
    public Agent $agent;
    public ?ModelsAgentLog $agentLog = null;

    public function mount(Agent $agent): void
    {
        $agent->dump();

        $this->agent = $agent;
        $this->agentLog = $this->agent->log;
    }

    public function render(): View|Factory|\Illuminate\View\View
    {
        $this->agent->dump();

        return view('livewire.customer.agent-log', ['agentLog' => $this->agentLog] );
    }
}
