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
        $this->agent = $agent;
        $this->agentLog = $this->agent->log;
    }


    public function getFormattedMessage(string $message): string
    {
        // zachytí text mezi apostrofy
        preg_match_all("/'([^']+)'/", $message, $matches);

        $labels = [
            'Výchozí pro počítač',
            'Oprávnění',
            'Operace',
            'CLSID',
            'APPID',
            'Počítač',
            'Uživatel',
            'SID',
            'Místo volání',
            'Aplikace',
            'Aplikační SID'
        ];

        $details = collect($matches[1] ?? [])
            ->map(function ($value, $index) use ($labels) {
                $label = $labels[$index] ?? "Parametr $index";
                return "<div><strong>{$label}:</strong> {$value}</div>";
            })
            ->implode('');

        // nahradí původní text prvním řádkem + oddělí detaily
        $header = strtok($message, "\n");
        return "<p>{$header}</p><hr>{$details}";
    }

    public function render(): View|Factory|\Illuminate\View\View
    {
        return view('livewire.customer.agent-log', ['agentLog' => $this->agentLog] );
    }
}
