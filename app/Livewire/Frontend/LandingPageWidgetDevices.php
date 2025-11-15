<?php

namespace App\Livewire\Frontend;

use Illuminate\Contracts\View\Factory;
use Illuminate\View\View;
use Livewire\Component;

class LandingPageWidgetDevices extends Component
{
    public array $devices = [];

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->refreshData();
    }

    /**
     * Render the component.
     */
    public function render(): Factory|\Illuminate\Contracts\View\View|View
    {
        $this->refreshData();

        return view('livewire.frontend.landing-page-widget-devices');
    }

    /**
     * Refresh the data.
     */
    private function refreshData(): void
    {
        $names = ['edge-ny-01', 'edge-ny-02', 'core-prg-01', 'db-lon-01', 'backup-01', 'workstation-17'];
        $this->devices = [];
        foreach ($names as $i => $name) {
            $cpu = rand(5, 92);
            $mem = rand(18, 94);
            $disk = rand(12, 96);
            $status = $cpu > 85 || $mem > 90 || $disk > 95 ? 'critical' : ($cpu > 75 ? 'warning' : 'ok');

            $this->devices[] = [
                'id' => $i + 1,
                'name' => $name,
                'cpu' => $cpu,
                'mem' => $mem,
                'disk' => $disk,
                'status' => $status,
            ];
        }
    }
}
