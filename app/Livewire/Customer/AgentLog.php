<?php

namespace App\Livewire\Customer;

use App\Models\AgentLog as ModelsAgentLog;
use Livewire\Component;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

class AgentLog extends Component
{
    public ModelsAgentLog $agentLog;
    public $log;

    public function mount(ModelsAgentLog $agentLog): void
    {
        $this->agentLog = $agentLog;
        $this->log = json_decode($agentLog->agent_log, true);

        dump($this->log);

    }

    public function render(): View|Factory|\Illuminate\View\View
    {
        return view('livewire.customer.agent-log');
    }
}
