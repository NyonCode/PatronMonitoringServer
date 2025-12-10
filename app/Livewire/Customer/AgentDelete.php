<?php

namespace App\Livewire\Customer;

use App\Models\Agent;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Flux\Flux;
use phpDocumentor\Reflection\Types\Void_;

class AgentDelete extends Component
{
    public Agent $agent;

    /**
     * Mount the component.
     *
     * @param  Agent  $agent    The agent to delete.
     *
     * @return void
     */
    public function mount(Agent $agent): void
    {
        $this->agent = $agent;
    }

    /**
     * Delete the agent and dispatch a toast notification.
     *
     * @return void
     */
    public function delete(): void
    {
        $this->agent->delete();
        Flux::toast('Agent deleted successfully', 'success');
        $this->dispatch('closeDelete');
    }

    /**
     * Render the component.
     *
     * @return View
     */
    public function render(): View
    {
        return view('livewire.customer.agent-delete');
    }
}
