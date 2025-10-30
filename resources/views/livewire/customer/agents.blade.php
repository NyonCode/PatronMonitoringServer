<div class="space-y-6">
    <!-- Header se statistikami -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <flux:card>
            <div class="flex items-center justify-between">
                <div>
                    <flux:subheading>Celkem agentů</flux:subheading>
                    <flux:heading size="xl">{{ $this->agents->total() }}</flux:heading>
                </div>
                <flux:icon.computer-desktop class="w-12 h-12 text-zinc-400" />
            </div>
        </flux:card>

        <flux:card>
            <div class="flex items-center justify-between">
                <div>
                    <flux:subheading>Online</flux:subheading>
                    <flux:heading size="xl" class="text-green-600">
                        {{ $this->agents->filter(fn($agent) => $this->getAgentStatus($agent) === 'online')->count() }}
                    </flux:heading>
                </div>
                <flux:icon.check-circle class="w-12 h-12 text-green-600" />
            </div>
        </flux:card>

        <flux:card>
            <div class="flex items-center justify-between">
                <div>
                    <flux:subheading>Offline</flux:subheading>
                    <flux:heading size="xl" class="text-red-600">
                        {{ $this->agents->filter(fn($agent) => $this->getAgentStatus($agent) === 'offline')->count() }}
                    </flux:heading>
                </div>
                <flux:icon.x-circle class="w-12 h-12 text-red-600" />
            </div>
        </flux:card>

        <flux:card>
            <div class="flex items-center justify-between">
                <div>
                    <flux:subheading>Varování</flux:subheading>
                    <flux:heading size="xl" class="text-yellow-600">
                        {{ $this->agents->filter(function($agent) {
                            $metrics = $this->getCurrentMetrics($agent);
                            return $metrics['cpu'] > 80 || $metrics['ram'] > 80;
                        })->count() }}
                    </flux:heading>
                </div>
                <flux:icon.exclamation-triangle class="w-12 h-12 text-yellow-600" />
            </div>
        </flux:card>
    </div>

    <!-- Vyhledávání a filtry -->
    <flux:card>
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex-1">
                <flux:input
                    wire:model.live.debounce.300ms="search"
                    placeholder="Hledat podle názvu, pretty name nebo IP adresy..."
                    icon="magnifying-glass"
                />
            </div>
            <div class="flex items-center gap-2">
                <flux:select wire:model.live="perPage" class="w-32">
                    <option value="5">5 / stránka</option>
                    <option value="10">10 / stránka</option>
                    <option value="25">25 / stránka</option>
                    <option value="50">50 / stránka</option>
                </flux:select>
            </div>
        </div>
    </flux:card>

    <!-- Tabulka agentů -->
    <flux:card class="overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-zinc-50 dark:bg-zinc-900">
                <tr>
                    <th class="px-6 py-3 text-left">
                        <button
                            wire:click="sortBy('hostname')"
                            class="flex items-center gap-2 font-medium text-sm text-zinc-700 dark:text-zinc-300 hover:text-zinc-900 dark:hover:text-zinc-100"
                        >
                            Název
                            @if($sortBy === 'hostname')
                                <flux:icon.chevron-up-down class="w-4 h-4" />
                            @endif
                        </button>
                    </th>
                    <th class="px-6 py-3 text-left">
                        <span class="font-medium text-sm text-zinc-700 dark:text-zinc-300">Status</span>
                    </th>
                    <th class="px-6 py-3 text-left">
                        <button
                            wire:click="sortBy('ip_address')"
                            class="flex items-center gap-2 font-medium text-sm text-zinc-700 dark:text-zinc-300 hover:text-zinc-900 dark:hover:text-zinc-100"
                        >
                            IP adresa
                            @if($sortBy === 'ip_address')
                                <flux:icon.chevron-up-down class="w-4 h-4" />
                            @endif
                        </button>
                    </th>
                    <th class="px-6 py-3 text-center">
                        <span class="font-medium text-sm text-zinc-700 dark:text-zinc-300">CPU</span>
                    </th>
                    <th class="px-6 py-3 text-center">
                        <span class="font-medium text-sm text-zinc-700 dark:text-zinc-300">RAM</span>
                    </th>
                    <th class="px-6 py-3 text-center">
                        <span class="font-medium text-sm text-zinc-700 dark:text-zinc-300">GPU</span>
                    </th>
                    <th class="px-6 py-3 text-left">
                        <span class="font-medium text-sm text-zinc-700 dark:text-zinc-300">Nejvíce zaplněný disk</span>
                    </th>
                    <th class="px-6 py-3 text-right">
                        <span class="font-medium text-sm text-zinc-700 dark:text-zinc-300">Akce</span>
                    </th>
                </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                @forelse($this->agents as $agent)
                    @php
                        $status = $this->getAgentStatus($agent);
                        $metrics = $this->getCurrentMetrics($agent);
                        $disk = $this->getMostUsedDisk($agent);
                        $sparkline = $this->getSparklineData($agent);
                    @endphp
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                        <!-- Název -->
                        <td class="px-6 py-4">
                            <div>
                                <div class="font-medium text-zinc-900 dark:text-zinc-100">
                                    {{ $agent->hostname }}
                                </div>
                                @if($agent->pretty_name)
                                    <div class="text-sm text-zinc-500 dark:text-zinc-400">
                                        {{ $agent->pretty_name }}
                                    </div>
                                @endif
                            </div>
                        </td>

                        <!-- Status -->
                        <td class="px-6 py-4">
                            @if($status === 'online')
                                <flux:badge color="green" size="sm" icon="check-circle">
                                    Online
                                </flux:badge>
                            @else
                                <flux:badge color="red" size="sm" icon="x-circle">
                                    Offline
                                </flux:badge>
                            @endif
                        </td>

                        <!-- IP adresa -->
                        <td class="px-6 py-4">
                                <span class="text-sm text-zinc-700 dark:text-zinc-300 font-mono">
                                    {{ $agent->ip_address ?? 'N/A' }}
                                </span>
                        </td>

                        <!-- CPU mini graf -->
                        <td class="px-6 py-4">
                            <div class="flex flex-col items-center gap-1">
                                <flux:badge
                                    size="sm"
                                    :color="$metrics['cpu'] > 80 ? 'red' : ($metrics['cpu'] > 60 ? 'yellow' : 'green')"
                                >
                                    {{ $metrics['cpu'] }}%
                                </flux:badge>
                                <canvas
                                    data-sparkline="{{ json_encode($sparkline['cpu']) }}"
                                    data-color="rgb(239, 68, 68)"
                                    class="sparkline-chart"
                                    width="80"
                                    height="20"
                                ></canvas>
                            </div>
                        </td>

                        <!-- RAM mini graf -->
                        <td class="px-6 py-4">
                            <div class="flex flex-col items-center gap-1">
                                <flux:badge
                                    size="sm"
                                    :color="$metrics['ram'] > 80 ? 'red' : ($metrics['ram'] > 60 ? 'yellow' : 'green')"
                                >
                                    {{ $metrics['ram'] }}%
                                </flux:badge>
                                <canvas
                                    data-sparkline="{{ json_encode($sparkline['ram']) }}"
                                    data-color="rgb(59, 130, 246)"
                                    class="sparkline-chart"
                                    width="80"
                                    height="20"
                                ></canvas>
                            </div>
                        </td>

                        <!-- GPU mini graf -->
                        <td class="px-6 py-4">
                            <div class="flex flex-col items-center gap-1">
                                <flux:badge
                                    size="sm"
                                    :color="$metrics['gpu'] > 80 ? 'red' : ($metrics['gpu'] > 60 ? 'yellow' : 'green')"
                                >
                                    {{ $metrics['gpu'] }}%
                                </flux:badge>
                                <canvas
                                    data-sparkline="{{ json_encode($sparkline['gpu']) }}"
                                    data-color="rgb(34, 197, 94)"
                                    class="sparkline-chart"
                                    width="80"
                                    height="20"
                                ></canvas>
                            </div>
                        </td>

                        <!-- Nejvíce zaplněný disk -->
                        <td class="px-6 py-4">
                            @if($disk)
                                <div class="space-y-1">
                                    <div class="flex items-center justify-between gap-2">
                                            <span class="text-sm text-zinc-700 dark:text-zinc-300">
                                                {{ $disk['name'] }}
                                            </span>
                                        <flux:badge
                                            size="sm"
                                            :color="$disk['usage_percent'] > 90 ? 'red' : ($disk['usage_percent'] > 75 ? 'yellow' : 'green')"
                                        >
                                            {{ $disk['usage_percent'] }}%
                                        </flux:badge>
                                    </div>
                                    <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-1.5">
                                        <div
                                            class="h-1.5 rounded-full transition-all {{ $disk['usage_percent'] > 90 ? 'bg-red-500' : ($disk['usage_percent'] > 75 ? 'bg-yellow-500' : 'bg-green-500') }}"
                                            style="width: {{ $disk['usage_percent'] }}%"
                                        ></div>
                                    </div>
                                    <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                        {{ $disk['free'] }} / {{ $disk['size'] }}
                                    </div>
                                </div>
                            @else
                                <span class="text-sm text-zinc-400">N/A</span>
                            @endif
                        </td>

                        <!-- Tlačítko Detail -->
                        <td class="px-6 py-4 text-right">
                            <flux:button
                                wire:click="showDetail({{ $agent->id }})"
                                size="sm"
                                variant="ghost"
                                icon="eye"
                            >
                                Detail
                            </flux:button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <flux:icon.inbox class="w-12 h-12 text-zinc-400" />
                                <flux:subheading>Žádní agenti nenalezeni</flux:subheading>
                                <flux:text>Zkuste upravit vyhledávací kritéria</flux:text>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($this->agents->hasPages())
            <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700">
                {{ $this->agents->links() }}
            </div>
        @endif
    </flux:card>

    <!-- Modal s detaily agenta -->
    @if($showDetailModal && $selectedAgentId)
        <flux:modal wire:model="showDetailModal" class="max-w-7xl">
            <div>
                <flux:heading size="lg">Detail agenta</flux:heading>
                <flux:subheading class="mt-2">
                    {{ $this->agents->find($selectedAgentId)?->hostname }}
                </flux:subheading>
            </div>

            <div class="mt-6">
                @livewire('customer.agent-detail', ['agent' => $this->agents->find($selectedAgentId)], key('agent-detail-'.$selectedAgentId))
            </div>

            <flux:modal.footer>
                <flux:spacer />
                <flux:button wire:click="closeDetail" variant="ghost">
                    Zavřít
                </flux:button>
            </flux:modal.footer>
        </flux:modal>
    @endif

    @script
    <script>
        function initSparklines() {
            document.querySelectorAll('.sparkline-chart').forEach((canvas) => {
                const data = JSON.parse(canvas.getAttribute('data-sparkline'));
                const color = canvas.getAttribute('data-color');

                if (data.length === 0) return;

                const ctx = canvas.getContext('2d');
                const width = canvas.width;
                const height = canvas.height;
                const max = Math.max(...data, 1);
                const min = Math.min(...data, 0);
                const range = max - min || 1;

                // Clear canvas
                ctx.clearRect(0, 0, width, height);

                // Draw line
                ctx.beginPath();
                ctx.strokeStyle = color;
                ctx.lineWidth = 1.5;
                ctx.lineJoin = 'round';
                ctx.lineCap = 'round';

                data.forEach((value, index) => {
                    const x = (index / (data.length - 1)) * width;
                    const y = height - ((value - min) / range) * height;

                    if (index === 0) {
                        ctx.moveTo(x, y);
                    } else {
                        ctx.lineTo(x, y);
                    }
                });

                ctx.stroke();

                // Draw fill
                ctx.lineTo(width, height);
                ctx.lineTo(0, height);
                ctx.closePath();
                ctx.fillStyle = color.replace('rgb', 'rgba').replace(')', ', 0.1)');
                ctx.fill();
            });
        }

        // Inicializace při načtení
        document.addEventListener('DOMContentLoaded', initSparklines);

        // Reinicializace po Livewire aktualizaci
        Livewire.hook('morph.updated', () => {
            setTimeout(initSparklines, 50);
        });
    </script>
    @endscript
</div>
