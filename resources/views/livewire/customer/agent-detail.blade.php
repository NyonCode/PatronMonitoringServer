<div wire:poll.5s="refreshMetrics">
    <div class="space-y-6">
        <!-- Header s inline editací názvu -->
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                @if($editingName)
                    <flux:input
                        wire:model="editName"
                        class="text-2xl font-bold"
                        autofocus
                    />
                    <div class="flex gap-2">
                        <flux:button
                            wire:click="saveName"
                            size="sm"
                            variant="primary"
                            icon="check"
                        />
                        <flux:button
                            wire:click="cancelEditName"
                            size="sm"
                            variant="ghost"
                            icon="x-mark"
                        />
                    </div>
                @else
                    <flux:heading size="xl">
                        {{ $agent->pretty_name ?? $agent->hostname }}
                    </flux:heading>
                    <flux:button
                        wire:click="startEditingName"
                        size="sm"
                        variant="ghost"
                        icon="pencil"
                    />
                @endif
            </div>

            @if($agent->pretty_name)
                <flux:subheading>{{ $agent->hostname }}</flux:subheading>
            @endif
        </div>

        <!-- Základní informace -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- IP adresa -->
            <flux:card>
                <div class="space-y-1">
                    <flux:subheading size="sm">IP adresa</flux:subheading>
                    <flux:heading size="sm" class="font-mono">
                        {{ $agent->ip_address ?? 'N/A' }}
                    </flux:heading>
                </div>
            </flux:card>

            <!-- UUID -->
            <flux:card>
                <div class="space-y-1">
                    <flux:subheading size="sm">UUID</flux:subheading>
                    <flux:heading size="sm" class="font-mono text-xs">
                        {{ $agent->uuid }}
                    </flux:heading>
                </div>
            </flux:card>

            <!-- Poslední komunikace -->
            <flux:card>
                <div class="space-y-1">
                    <flux:subheading size="sm">Poslední komunikace</flux:subheading>
                    <flux:heading size="sm">
                        {{ $agent->last_seen_at?->diffForHumans() ?? 'Nikdy' }}
                    </flux:heading>
                </div>
            </flux:card>

            <!-- Update interval -->
            <flux:card>
                <div class="space-y-1">
                    <flux:subheading size="sm">Update interval</flux:subheading>
                    <flux:heading size="sm">
                        {{ $agent->update_interval }}s
                    </flux:heading>
                </div>
            </flux:card>
        </div>

        <!-- Síťové informace -->
        @if($networkInfo)
            <flux:card>
                <flux:heading size="lg" class="mb-4">Síťové informace</flux:heading>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div>
                        <flux:subheading size="sm">IP adresa</flux:subheading>
                        <flux:text class="font-mono">{{ $networkInfo['ip_address'] ?? 'N/A' }}</flux:text>
                    </div>
                    <div>
                        <flux:subheading size="sm">Maska podsítě</flux:subheading>
                        <flux:text class="font-mono">{{ $networkInfo['subnet_mask'] ?? 'N/A' }}</flux:text>
                    </div>
                    <div>
                        <flux:subheading size="sm">Výchozí brána</flux:subheading>
                        <flux:text class="font-mono">{{ $networkInfo['gateway'] ?? 'N/A' }}</flux:text>
                    </div>
                    <div>
                        <flux:subheading size="sm">DNS servery</flux:subheading>
                        <flux:text class="font-mono">{{ $networkInfo['dns'] ?? 'N/A' }}</flux:text>
                    </div>
                    <div>
                        <flux:subheading size="sm">MAC adresa</flux:subheading>
                        <flux:text class="font-mono">{{ $networkInfo['mac_address'] ?? 'N/A' }}</flux:text>
                    </div>
                </div>
            </flux:card>
        @endif

        <!-- Aktuální hodnoty metrik -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- CPU Card -->
            <flux:card>
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <flux:heading size="sm">CPU</flux:heading>
                        <flux:badge :color="$currentMetrics['cpu'] > 80 ? 'red' : ($currentMetrics['cpu'] > 60 ? 'yellow' : 'green')">
                            {{ $currentMetrics['cpu'] }}%
                        </flux:badge>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                        <div
                            class="h-2 rounded-full transition-all duration-300 {{ $currentMetrics['cpu'] > 80 ? 'bg-red-500' : ($currentMetrics['cpu'] > 60 ? 'bg-yellow-500' : 'bg-green-500') }}"
                            style="width: {{ $currentMetrics['cpu'] }}%"
                        ></div>
                    </div>
                </div>
            </flux:card>

            <!-- RAM Card -->
            <flux:card>
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <flux:heading size="sm">RAM</flux:heading>
                        <flux:badge :color="$currentMetrics['ram'] > 80 ? 'red' : ($currentMetrics['ram'] > 60 ? 'yellow' : 'green')">
                            {{ $currentMetrics['ram'] }}%
                        </flux:badge>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                        <div
                            class="h-2 rounded-full transition-all duration-300 {{ $currentMetrics['ram'] > 80 ? 'bg-red-500' : ($currentMetrics['ram'] > 60 ? 'bg-yellow-500' : 'bg-green-500') }}"
                            style="width: {{ $currentMetrics['ram'] }}%"
                        ></div>
                    </div>
                </div>
            </flux:card>

            <!-- GPU Card -->
            <flux:card>
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <flux:heading size="sm">GPU</flux:heading>
                        <flux:badge :color="$currentMetrics['gpu'] > 80 ? 'red' : ($currentMetrics['gpu'] > 60 ? 'yellow' : 'green')">
                            {{ $currentMetrics['gpu'] }}%
                        </flux:badge>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                        <div
                            class="h-2 rounded-full transition-all duration-300 {{ $currentMetrics['gpu'] > 80 ? 'bg-red-500' : ($currentMetrics['gpu'] > 60 ? 'bg-yellow-500' : 'bg-green-500') }}"
                            style="width: {{ $currentMetrics['gpu'] }}%"
                        ></div>
                    </div>
                </div>
            </flux:card>
        </div>

        <!-- Period selector -->
        <flux:card>
            <div class="flex items-center justify-between">
                <flux:heading size="lg">Historická data</flux:heading>
                <flux:select wire:model.live="period">
                    <option value="hour">Poslední hodina</option>
                    <option value="day">Poslední den</option>
                    <option value="week">Poslední týden</option>
                    <option value="month">Poslední měsíc</option>
                    <option value="year">Poslední rok</option>
                </flux:select>
            </div>
        </flux:card>

        <!-- Graf -->
        <flux:card>
            <div class="h-96">
                <canvas id="metricsChart" wire:ignore></canvas>
            </div>
        </flux:card>

        <!-- Disk status -->
        <flux:card>
            <flux:heading size="lg" class="mb-4">Stav disků</flux:heading>
            <div class="space-y-4">
                @forelse($diskStatus as $disk)
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <div>
                                <flux:subheading>{{ $disk['name'] }}</flux:subheading>
                                <flux:text size="sm">{{ $disk['free'] }} volných z {{ $disk['size'] }}</flux:text>
                            </div>
                            <flux:badge :color="$disk['usage_percent'] > 90 ? 'red' : ($disk['usage_percent'] > 75 ? 'yellow' : 'green')">
                                {{ $disk['usage_percent'] }}%
                            </flux:badge>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                            <div
                                class="h-3 rounded-full transition-all duration-300 {{ $disk['usage_percent'] > 90 ? 'bg-red-500' : ($disk['usage_percent'] > 75 ? 'bg-yellow-500' : 'bg-green-500') }}"
                                style="width: {{ $disk['usage_percent'] }}%"
                            ></div>
                        </div>
                    </div>
                @empty
                    <flux:text>Žádné disky nenalezeny</flux:text>
                @endforelse
            </div>
        </flux:card>
    </div>

    @script
    <script>
        let chart = null;

        function initChart() {
            const ctx = document.getElementById('metricsChart');
            if (!ctx) {
                console.error('Canvas element not found');
                return;
            }

            if (chart) {
                chart.destroy();
            }

            const data = @json($chartData);

            if (!data || !data.labels || data.labels.length === 0) {
                console.warn('No chart data available');
                return;
            }

            chart = new Chart(ctx, {
                type: 'line',
                data: data,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                        },
                        filler: {
                            propagate: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            stacked: false,
                            ticks: {
                                callback: function(value) {
                                    return value + '%';
                                }
                            }
                        },
                        x: {
                            display: true,
                            title: {
                                display: true
                            }
                        }
                    }
                }
            });

            console.log('Chart initialized successfully');
        }

        // Inicializace při načtení
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initChart);
        } else {
            initChart();
        }

        // Reinicializace po Livewire aktualizaci
        Livewire.hook('morph.updated', ({el, component}) => {
            if (component.name === 'customer.agent-detail') {
                setTimeout(initChart, 100);
            }
        });
    </script>
    @endscript

    @assets
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
    @endassets
</div>
