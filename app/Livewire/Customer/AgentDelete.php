<?php

namespace App\Livewire\Customer;

use App\Models\Agent;
use App\UI\Toast;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class AgentDelete extends Component
{
    public Agent $agent;

    /**
     * Mount the component.
     *
     * @param  Agent  $agent  The agent to delete.
     */
    public function mount(Agent $agent): void
    {
        $this->agent = $agent;
    }

    /**
     * Delete the agent and dispatch a toast notification.
     */
    public function delete(): void
    {
        $this->agent->delete();
        Toast::success('Agent deleted successfully');
        $this->dispatch('closeDelete');
    }

    /**
     * Render the component.
     */
    public function render(): View
    {
        return view('livewire.customer.agent-delete');
    }
}
