<div class="fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center sm:p-4" wire:poll.5s="refreshMetrics">

    <div class="bg-white dark:bg-zinc-900 sm:rounded-lg shadow-xl w-full max-w-screen sm:max-w-6xl max-h-screen sm:max-h-[90vh] overflow-y-auto">
        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-6 md:py-3 border-b border-zinc-200 dark:border-zinc-700 max-w-screen overflow-x-hidden">
            <div class="flex items-center justify-between w-full">
                <div class="flex items-center gap-3">
                    @if($editingName)
                        <input
                            wire:model="editName"
                            class="text-base md:text-2xl font-bold px-3 py-1 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                            autofocus
                        />
                        <div class="flex gap-2">
                            <button wire:click="saveName" class="px-3 py-1.5 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </button>
                            <button wire:click="cancelEditName" class="px-3 py-1.5 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    @else
                        <div class="flex flex-col">
                            <div class="flex items-center gap-2">
                                <h2 class="text-2xl font-bold text-zinc-900 dark:text-white">
                                    {{ $this->getEditName() }}
                                </h2>
                                <button wire:click="startEditingName" class="p-2 text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                    </svg>
                                </button>
                            </div>
                            @if($agent->pretty_name)
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ $agent->hostname }}</p>
                            @endif
                        </div>
                    @endif
                </div>

                @if(!$editingName)
                    <button wire:click="$parent.closeDetail" class="text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                @endif

            </div>
        </div>

        <div class="space-y-6 p-4">

            <!-- Offline info -->
            @if($agent->last_seen_at?->lt(now()->subMinutes(5)))
                <div class="inset-0 backdrop-blur-sm flex items-center justify-center text-lg font-semibold">
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

            <!-- Informace o sezení -->
            @if($sessionInfo)
                @php
                    $diff = now()->diff($sessionInfo->session_start);
                @endphp

                <div class="bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                    <h3 class="text-lg font-bold text-zinc-900 dark:text-white mb-4">Session info</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">User</p>
                            <p class="font-mono text-zinc-900 dark:text-white">{{ $sessionInfo->session_user ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">Session time</p>
                            <p class="font-mono text-zinc-900 dark:text-white">{{ $diff ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">Mapper drivers</p>
                            @foreach($sessionInfo->mapped_drivers as $mappedDrivers)
                                <div class="flex">
                                    <div class="font-mono font-bold text-zinc-900 dark:text-white pr-4">{{ $mappedDrivers['Letter'] }}</div>
                                    <div class="font-mono text-zinc-900 dark:text-white">{{ $mappedDrivers['Path'] }}</div>
                                </div>
                            @endforeach
                        </div>

                        <div>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">Accessible paths</p>
                            @foreach($sessionInfo->accessible_paths as $accessiblePaths)
                                <p class="font-mono text-zinc-900 dark:text-white">
                                    {{ $accessiblePaths }}
                                </p>
                            @endforeach
                        </div>

                    </div>
                </div>
            @endif

            <!-- Aktuální hodnoty metrik pouze s progress bary -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach(['cpu' => 'CPU', 'ram' => 'RAM', 'gpu' => 'GPU'] as $metric => $label)
                    <div class="bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-700 p-4"
                         x-data="{ value: {{ $currentMetrics[$metric] ?? 0 }} }"
                         x-init="$watch('$wire.currentMetrics.{{ $metric }}', val => value = val || 0)">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <h4 class="text-sm font-semibold text-zinc-900 dark:text-white">{{ $label }}</h4>
                                <span class="inline-flex px-2 py-1 rounded text-sm font-semibold transition-colors duration-300"
                                      :class="{
                                          'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300': value > 80,
                                          'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300': value > 60 && value <= 80,
                                          'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300': value > 0 && value <= 60,
                                          'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400': value === 0
                                      }">
                                    <span x-text="value"></span>%
                                </span>
                            </div>

                            <!-- Progress bar s animací -->
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3 overflow-hidden">
                                <div class="h-3 rounded-full transition-all duration-500 ease-out"
                                     :class="{
                                         'bg-red-500': value > 80,
                                         'bg-yellow-500': value > 60 && value <= 80,
                                         'bg-green-500': value > 0 && value <= 60,
                                         'bg-gray-400': value === 0
                                     }"
                                     :style="`width: ${value}%`"></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Upozornění pokud nejsou data -->
            @if(!$hasCurrentData)
                <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <div class="flex-1">
                            <h4 class="text-sm font-semibold text-yellow-800 dark:text-yellow-300">Nejsou dostupná aktuální data</h4>
                            <p class="text-sm text-yellow-700 dark:text-yellow-400 mt-1">
                                Agent je offline nebo nebyly ještě zaznamenány žádné metriky.
                                @if($hasHistoricalData)
                                    Historická data jsou k dispozici níže.
                                @endif
                            </p>
                            @if($suggestedPeriod)
                                <button
                                    wire:click="switchToPeriodWithData"
                                    class="mt-2 inline-flex items-center gap-2 px-3 py-1.5 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg text-sm transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                    </svg>
                                    {{ __('Switch to period with data') }} ({{ $suggestedPeriodLabel }})
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- Period selector -->
            <div class="bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-700 p-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-zinc-900 dark:text-white">
                        {{ __('Historical data') }}
                    </h3>
                    <select
                        wire:model.live="period"
                        class="px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        <option value="hour">
                            {{ __('Last hour') }}
                        </option>
                        <option value="day">
                            {{ __('Last day') }}
                        </option>
                        <option value="week">
                            {{ __('Last week') }}
                        </option>
                        <option value="month">
                            {{ __('Last month') }}
                        </option>
                        <option value="year">
                            {{ __('Last year') }}
                        </option>
                    </select>
                </div>
            </div>

            <!-- Graf -->
            <div class="bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                @if($hasHistoricalData)
                    <div class="h-96">
                        <canvas id="metricsChart" wire:ignore></canvas>
                    </div>
                @else
                    <div class="h-96 flex items-center justify-center">
                        <div class="text-center">
                            <svg class="w-16 h-16 text-zinc-300 dark:text-zinc-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            <h3 class="text-lg font-semibold text-zinc-900 dark:text-white mb-2">
                                {{ __('No historical data') }}
                            </h3>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                {{ __('No historical data available for the selected period.') }}
                            </p>
                            @if($suggestedPeriod && $suggestedPeriod !== $period)
                                <button
                                    wire:click="switchToPeriodWithData"
                                    class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm transition-colors">
                                    Zobrazit data za {{ $suggestedPeriodLabel }}
                                </button>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <!-- Disk status -->
            <div class="bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                <h3 class="text-lg font-bold text-zinc-900 dark:text-white mb-4">
                    {{ __('Disk status') }}
                </h3>
                <div class="space-y-4">
                    @forelse($diskStatus as $disk)
                        <div x-data="{ usage: {{ $disk['usage_percent'] ?? 0 }} }"
                             x-init="$watch('$wire.diskStatus', () => usage = {{ $disk['usage_percent'] ?? 0 }})">
                            <div class="flex items-center justify-between mb-2">
                                <div>
                                    <p class="font-medium text-zinc-900 dark:text-white">{{ $disk['name'] }}</p>
                                    <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                        {{ $disk['free'] }} {{ __('free space out of') }} {{ $disk['total'] }}
                                    </p>
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
                                <div class="h-3 rounded-full transition-all duration-500 ease-out"
                                     :class="{
                                         'bg-red-500': usage > 90,
                                         'bg-yellow-500': usage > 75 && usage <= 90,
                                         'bg-green-500': usage <= 75
                                     }"
                                     :style="`width: ${usage}%`"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-zinc-600 dark:text-zinc-400 text-center py-4">
                            {{ __('No disks found') }}
                        </p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="flex justify-end gap-3 px-6 py-6 md:py-3 border-t border-zinc-200 dark:border-zinc-700">
            <button
                wire:click="$parent.closeDetail"
                class="px-4 py-2 text-sm font-medium text-zinc-700 border border-zinc-200 dark:border-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg transition-colors"
            >
                {{ __('Close') }}
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
            const hasData = $wire.get('hasHistoricalData');

            if (!hasData || !data || !data.labels || data.labels.length === 0) {
                updateInProgress = false;
                return;
            }

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
            const hasData = $wire.get('hasHistoricalData');
            const currentPeriod = $wire.get('period');

            if (!hasData || !data || !data.labels || data.labels.length === 0) {
                return;
            }

            if (chart && lastPeriod !== currentPeriod) {
                chart.destroy();
                chart = null;
                lastPeriod = currentPeriod;
            }

            if (chart) {
                updateChartData();
                return;
            }

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

        document.addEventListener('DOMContentLoaded', initChart);

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
