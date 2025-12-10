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
        Toast::toast('Agent deleted successfully', 'success');
        $this->dispatch('closeDelete');
    }

    public function testToast(): void
    {
        Toast::toast('Agent deleted successfully', 'success');
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
