<?php

namespace App\Livewire\Customer;

use App\Livewire\Support\WithTable;
use App\Models\Agent;
use Livewire\Component;

class Agents extends Component
{
    use WithTable;
    /**
     * @var int[]
     */

    public $model = Agent::class;
    public array $relationships = ['log', 'disk', 'network', 'metrics'];


    public function render()
    {
        $agents = \App\Models\Agent::all();
        $agents->partition($this->perPage);

        return view('livewire.customer.agents', compact('agents'));
    }

    public function columns(): array
    {
        return [
            'id' => [
                'label' => 'ID',
                'sortable' => true,
                'searchable' => false,
                'filterable' => false,
                'visible' => false
            ],
            'hostname' => [
                'label' => 'Hostname',
                'sortable' => true,
                'searchable' => true,
                'filterable' => true,
                'visible' => true
            ],
            'ip_address' => [
                'label' => 'IP',
                'sortable' => true,
                'searchable' => true,
                'filterable' => true,
                'visible' => true
            ],
            'pretty_name' => [
                'label' => 'Name',
                'sortable' => true,
                'searchable' => true,
                'filterable' => true,
                'visible' => true
            ],
            'update_interval' => [
                'label' => 'Update Interval',
                'sortable' => true,
                'searchable' => true,
                'filterable' => true,
                'visible' => false
            ],
            'last_seen_at' => [
                'label' => 'Last Seen',
                'sortable' => true,
                'searchable' => true,
                'filterable' => true,
                'visible' => false
            ],
            'network.mac_address' => [
                'label' => 'MAC Address',
                'sortable' => true,
                'searchable' => true,
                'filterable' => false,
                'visible' => false
            ]
        ];
    }
}
