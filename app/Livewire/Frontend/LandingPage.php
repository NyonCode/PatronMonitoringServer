<?php

namespace App\Livewire\Frontend;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class LandingPage extends Component
{
    public array $devices = [];

    public function mount(): void
    {
        $this->devices = $this->generateDevices();
    }

    /**
     * Generate random devices
     */
    private function generateDevices(): array
    {
        $names = [
            'edge-ny-01', 'edge-ny-02', 'core-prg-01',
            'db-lon-01', 'backup-01', 'workstation-17',
        ];

        $devices = [];
        foreach ($names as $i => $name) {
            $cpu = rand(5, 92);
            $mem = rand(18, 94);
            $disk = rand(12, 96);
            $uptimeHours = rand(5, 9999);
            $status = $cpu > 85 || $mem > 90 || $disk > 95 ? 'critical' : ($cpu > 75 ? 'warning' : 'ok');

            $devices[] = [
                'id' => $i + 1,
                'name' => $name,
                'cpu' => $cpu,
                'mem' => $mem,
                'disk' => $disk,
                'uptime' => $uptimeHours,
                'status' => $status,
            ];
        }

        return $devices;
    }

    public function render(): View|Factory|\Illuminate\View\View
    {
        $this->devices = $this->generateDevices();
        $this->dispatch('devices-updated', ['devices' => $this->devices]);

        return view('livewire.frontend.landing-page')
            ->layout('components.layouts.frontend');
    }
}
