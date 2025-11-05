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
    public ModelsAgentLog $agentLog;
    public $log;

    public function mount(Agent $agent): void
    {
        $this->agent = $agent;
        $this->agentLog = $agent->logs;

    }

    public function render(): View|Factory|\Illuminate\View\View
    {
        return view('livewire.customer.agent-log');
    }
}
