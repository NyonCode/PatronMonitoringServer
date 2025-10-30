<div wire:poll.5s="refreshMetrics">
    <div class="space-y-6">
        <!-- Header s aktuálními hodnotami -->
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
                    <div class="w-full bg-gray-200 rounded-full h-2">
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
                    <div class="w-full bg-gray-200 rounded-full h-2">
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
                    <div class="w-full bg-gray-200 rounded-full h-2">
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
                @foreach($diskStatus as $disk)
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
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div
                                class="h-3 rounded-full transition-all duration-300 {{ $disk['usage_percent'] > 90 ? 'bg-red-500' : ($disk['usage_percent'] > 75 ? 'bg-yellow-500' : 'bg-green-500') }}"
                                style="width: {{ $disk['usage_percent'] }}%"
                            ></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </flux:card>
    </div>

    @script
    <script>
        let chart = null;

        function initChart() {
            const ctx = document.getElementById('metricsChart');
            if (!ctx) return;

            if (chart) {
                chart.destroy();
            }

            const data = @json($chartData);

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
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            ticks: {
                                callback: function(value) {
                                    return value + '%';
                                }
                            }
                        }
                    }
                }
            });
        }

        // Inicializace při načtení
        document.addEventListener('DOMContentLoaded', initChart);

        // Aktualizace při změně dat
        Livewire.on('chartDataUpdated', () => {
            initChart();
        });

        // Reinicializace při Livewire aktualizaci
        $wire.on('$refresh', () => {
            setTimeout(initChart, 100);
        });
    </script>
    @endscript

    @assets
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
    @endassets
</div>
