<div class="fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4" wire:poll.5s="refreshMetrics">

    <div class="bg-white dark:bg-zinc-900 rounded-lg shadow-xl w-full max-w-6xl max-h-[90vh] overflow-y-auto">
        <!-- Header -->
        <div class="flex items-center justify-between p-6 border-b border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    @if($editingName)
                        <input
                            wire:model="editName"
                            class="text-2xl font-bold px-3 py-1 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                            autofocus
                        />
                        <div class="flex gap-2">
                            <button
                                wire:click="saveName"
                                class="px-3 py-1.5 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M5 13l4 4L19 7"></path>
                                </svg>
                            </button>
                            <button
                                wire:click="cancelEditName"
                                class="px-3 py-1.5 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    @else
                        <div class="flex flex-col">
                            <div class="flex items-center gap-2">
                                <h2 class="text-2xl font-bold text-zinc-900 dark:text-white">
                                    {{ $this->getEditName() }}
                                </h2>
                                <button
                                    wire:click="startEditingName"
                                    class="p-2 text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg transition-colors"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                    </svg>
                                </button>
                            </div>
                            @if($agent->pretty_name)
                                <div>
                                    <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ $agent->hostname }}</p>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
            <button
                wire:click="$parent.closeDetail"
                class="text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300"
            >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <div class="space-y-6 p-4">

             @if($agent->last_seen_at?->lt(now()->subMinutes(5)))
                <div class="inset-0 bg-zinc-900/40 backdrop-blur-sm flex items-center justify-center text-white text-lg font-semibold">
                    Agent je offline — zobrazují se poslední známá data
                </div>
            @endif

            <!-- Základní informace -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-700 p-4">
                    <div class="space-y-1">
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">IP adresa</p>
                        <p class="text-lg font-semibold text-zinc-900 dark:text-white font-mono">
                            {{ $agent->ip_address ?? 'N/A' }}
                        </p>
                    </div>
                </div>

                <div class="bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-700 p-4">
                    <div class="space-y-1">
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">UUID</p>
                        <p class="text-xs font-semibold text-zinc-900 dark:text-white font-mono overflow-hidden text-ellipsis">
                            {{ $agent->uuid }}
                        </p>
                    </div>
                </div>

                <div class="bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-700 p-4">
                    <div class="space-y-1">
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Poslední komunikace</p>
                        <p class="text-lg font-semibold text-zinc-900 dark:text-white">
                            {{ $agent->last_seen_at?->diffForHumans() ?? 'Nikdy' }}
                        </p>
                    </div>
                </div>

                <div class="bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-700 p-4">
                    <div class="space-y-1">
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Update interval</p>
                        <p class="text-lg font-semibold text-zinc-900 dark:text-white">
                            {{ $agent->update_interval }}s
                        </p>
                    </div>
                </div>
            </div>

            <!-- Síťové informace -->
            @if($networkInfo)
                <div class="bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                    <h3 class="text-lg font-bold text-zinc-900 dark:text-white mb-4">Síťové informace</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">IP adresa</p>
                            <p class="font-mono text-zinc-900 dark:text-white">{{ $networkInfo['ip_address'] ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">Maska podsítě</p>
                            <p class="font-mono text-zinc-900 dark:text-white">{{ $networkInfo['subnet_mask'] ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">Výchozí brána</p>
                            <p class="font-mono text-zinc-900 dark:text-white">{{ $networkInfo['gateway'] ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">DNS servery</p>
                            <p class="font-mono text-zinc-900 dark:text-white">{{ $networkInfo['dns'] ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">MAC adresa</p>
                            <p class="font-mono text-zinc-900 dark:text-white">{{ $networkInfo['mac_address'] ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Aktuální hodnoty metrik s grafickými ukazateli -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach(['cpu' => 'CPU', 'ram' => 'RAM', 'gpu' => 'GPU'] as $metric => $label)
                    <div class="bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-700 p-4" 
                         x-data="{ value: {{ $currentMetrics[$metric] }} }" 
                         x-init="$watch('$wire.currentMetrics.{{ $metric }}', val => value = val)">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <h4 class="text-sm font-semibold text-zinc-900 dark:text-white">{{ $label }}</h4>
                                <span class="inline-flex px-2 py-1 rounded text-sm font-semibold transition-colors duration-300"
                                      :class="{
                                          'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300': value > 80,
                                          'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300': value > 60 && value <= 80,
                                          'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300': value <= 60
                                      }">
                                    <span x-text="value"></span>%
                                </span>
                            </div>
                            
                            <!-- Progress bar -->
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 overflow-hidden">
                                <div class="h-2 rounded-full transition-all duration-300 ease-out"
                                     :class="{
                                         'bg-red-500': value > 80,
                                         'bg-yellow-500': value > 60 && value <= 80,
                                         'bg-green-500': value <= 60
                                     }"
                                     :style="`width: ${value}%`"></div>
                            </div>
                            
                            <!-- Circular gauge -->
                            <div class="flex justify-center">
                                <svg class="transform -rotate-90" width="80" height="80">
                                    <circle cx="40" cy="40" r="36" stroke="currentColor" 
                                            class="text-gray-200 dark:text-gray-700" 
                                            stroke-width="8" fill="none" />
                                    <circle cx="40" cy="40" r="36" stroke="currentColor" 
                                            :class="{
                                                'text-red-500': value > 80,
                                                'text-yellow-500': value > 60 && value <= 80,
                                                'text-green-500': value <= 60
                                            }"
                                            stroke-width="8" fill="none"
                                            stroke-linecap="round"
                                            :stroke-dasharray="`${(value * 226.19) / 100} 226.19`"
                                            class="transition-all duration-300 ease-out" />
                                </svg>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Period selector -->
            <div class="bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-700 p-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-zinc-900 dark:text-white">Historická data</h3>
                    <select
                        wire:model.live="period"
                        class="px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        <option value="hour">Poslední hodina</option>
                        <option value="day">Poslední den</option>
                        <option value="week">Poslední týden</option>
                        <option value="month">Poslední měsíc</option>
                        <option value="year">Poslední rok</option>
                    </select>
                </div>
            </div>

            <!-- Graf -->
            <div class="bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                <div class="h-96">
                    <canvas id="metricsChart" wire:ignore></canvas>
                </div>
            </div>

            <!-- Disk status -->
            <div class="bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                <h3 class="text-lg font-bold text-zinc-900 dark:text-white mb-4">Stav disků</h3>
                <div class="space-y-4">
                    @forelse($diskStatus as $disk)
                        <div x-data="{ usage: {{ $disk['usage_percent'] }} }" 
                             x-init="$watch('$wire.diskStatus', () => usage = {{ $disk['usage_percent'] }})">
                            <div class="flex items-center justify-between mb-2">
                                <div>
                                    <p class="font-medium text-zinc-900 dark:text-white">{{ $disk['name'] }}</p>
                                    <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ $disk['free'] }} volných
                                        z {{ $disk['size'] }}</p>
                                </div>
                                <span class="inline-flex px-2 py-1 rounded text-sm font-semibold transition-colors duration-300"
                                      :class="{
                                          'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300': usage > 90,
                                          'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300': usage > 75 && usage <= 90,
                                          'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300': usage <= 75
                                      }">
                                    <span x-text="usage"></span>%
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3 overflow-hidden">
                                <div class="h-3 rounded-full transition-all duration-300 ease-out"
                                     :class="{
                                         'bg-red-500': usage > 90,
                                         'bg-yellow-500': usage > 75 && usage <= 90,
                                         'bg-green-500': usage <= 75
                                     }"
                                     :style="`width: ${usage}%`"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-zinc-600 dark:text-zinc-400">Žádné disky nenalezeny</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="flex justify-end gap-3 p-6 border-t border-zinc-200 dark:border-zinc-700">
            <button
                wire:click="$parent.closeDetail"
                class="px-4 py-2 text-sm font-medium text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg transition-colors"
            >
                Zavřít
            </button>
        </div>
    </div>

    @script
    <script>
        let chart = null;
        let lastPeriod = @json($period);
        let updateInProgress = false;

        function updateChartData() {
            if (!chart || updateInProgress) return;
            
            updateInProgress = true;
            const data = $wire.get('chartData');
            
            if (!data || !data.labels || data.labels.length === 0) {
                updateInProgress = false;
                return;
            }

            // Plynulá aktualizace dat
            chart.data.labels = data.labels;
            chart.data.datasets.forEach((dataset, index) => {
                if (data.datasets[index]) {
                    dataset.data = data.datasets[index].data;
                }
            });
            
            chart.update('none');
            updateInProgress = false;
        }

        function initChart() {
            const ctx = document.getElementById('metricsChart');
            if (!ctx) return;

            const data = $wire.get('chartData');
            const currentPeriod = $wire.get('period');

            if (!data || !data.labels || data.labels.length === 0) {
                return;
            }

            // Pokud se změnilo období, znovu vytvoř graf
            if (chart && lastPeriod !== currentPeriod) {
                chart.destroy();
                chart = null;
                lastPeriod = currentPeriod;
            }

            // Pokud graf existuje, pouze aktualizuj data
            if (chart) {
                updateChartData();
                return;
            }

            // Vytvoř nový graf
            chart = new Chart(ctx, {
                type: 'line',
                data: data,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: false,
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
                                callback: value => value + '%'
                            }
                        }
                    }
                }
            });
        }

        // Inicializace
        document.addEventListener('DOMContentLoaded', initChart);

        // Optimalizovaná aktualizace
        let debounceTimer;
        Livewire.hook('morph.updated', ({component}) => {
            if (component.name === 'customer.agent-detail') {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    if (chart) {
                        updateChartData();
                    } else {
                        initChart();
                    }
                }, 100);
            }
        });

        // Při změně období
        Livewire.on('periodChanged', () => {
            lastPeriod = null;
            if (chart) {
                chart.destroy();
                chart = null;
            }
            setTimeout(initChart, 100);
        });
    </script>
    @endscript

    @assets
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
    @endassets
</div>
