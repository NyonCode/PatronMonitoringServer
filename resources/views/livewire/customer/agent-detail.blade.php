<div class="fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4">

    <div class="bg-white dark:bg-zinc-900 rounded-lg shadow-xl w-full max-w-6xl max-h-[90vh] overflow-y-auto">
        <!-- Header -->
        <div class="flex items-center justify-between p-6 border-b border-zinc-200 dark:border-zinc-700">
            <!-- Header s inline editací názvu -->
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
                wire:click="closeDetail"
                class="text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300"
            >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <div wire:poll.1s="refreshMetrics">
            <div class="space-y-6 p-4">


                <!-- Základní informace -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- IP adresa -->
                    <div class="bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-700 p-4">
                        <div class="space-y-1">
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">IP adresa</p>
                            <p class="text-lg font-semibold text-zinc-900 dark:text-white font-mono">
                                {{ $agent->ip_address ?? 'N/A' }}
                            </p>
                        </div>
                    </div>

                    <!-- UUID -->
                    <div class="bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-700 p-4">
                        <div class="space-y-1">
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">UUID</p>
                            <p class="text-xs font-semibold text-zinc-900 dark:text-white font-mono overflow-hidden text-ellipsis">
                                {{ $agent->uuid }}
                            </p>
                        </div>
                    </div>

                    <!-- Poslední komunikace -->
                    <div class="bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-700 p-4">
                        <div class="space-y-1">
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">Poslední komunikace</p>
                            <p class="text-lg font-semibold text-zinc-900 dark:text-white">
                                {{ $agent->last_seen_at?->diffForHumans() ?? 'Nikdy' }}
                            </p>
                        </div>
                    </div>

                    <!-- Update interval -->
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

                <!-- Aktuální hodnoty metrik s animovanými progress bary -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- CPU Card -->
                    <div class="bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-700 p-4">
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <h4 class="text-sm font-semibold text-zinc-900 dark:text-white">CPU</h4>
                                <span class="inline-flex px-2 py-1 rounded text-sm font-semibold
                            @if($currentMetrics['cpu'] > 80)
                                bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300
                            @elseif($currentMetrics['cpu'] > 60)
                                bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300
                            @else
                                bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300
                            @endif
                        ">
                            {{ $currentMetrics['cpu'] }}%
                        </span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 overflow-hidden">
                                <div
                                    data-progress-bar
                                    data-target-width="{{ $currentMetrics['cpu'] }}%"
                                    class="h-2 rounded-full transition-all duration-500 ease-out
                            @if($currentMetrics['cpu'] > 80)
                                bg-red-500
                            @elseif($currentMetrics['cpu'] > 60)
                                bg-yellow-500
                            @else
                                bg-green-500
                            @endif"
                                    style="width: 0%"
                                ></div>
                            </div>
                        </div>
                    </div>

                    <!-- RAM Card -->
                    <div class="bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-700 p-4">
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <h4 class="text-sm font-semibold text-zinc-900 dark:text-white">RAM</h4>
                                <span class="inline-flex px-2 py-1 rounded text-sm font-semibold
                            @if($currentMetrics['ram'] > 80)
                                bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300
                            @elseif($currentMetrics['ram'] > 60)
                                bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300
                            @else
                                bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300
                            @endif
                        ">
                            {{ $currentMetrics['ram'] }}%
                        </span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 overflow-hidden">
                                <div
                                    data-progress-bar
                                    data-target-width="{{ $currentMetrics['ram'] }}%"
                                    class="h-2 rounded-full transition-all duration-500 ease-out
                            @if($currentMetrics['ram'] > 80)
                                bg-red-500
                            @elseif($currentMetrics['ram'] > 60)
                                bg-yellow-500
                            @else
                                bg-green-500
                            @endif"
                                    style="width: 0%"
                                ></div>
                            </div>
                        </div>
                    </div>

                    <!-- GPU Card -->
                    <div class="bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-700 p-4">
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <h4 class="text-sm font-semibold text-zinc-900 dark:text-white">GPU</h4>
                                <span class="inline-flex px-2 py-1 rounded text-sm font-semibold
                            @if($currentMetrics['gpu'] > 80)
                                bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300
                            @elseif($currentMetrics['gpu'] > 60)
                                bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300
                            @else
                                bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300
                            @endif
                        ">
                            {{ $currentMetrics['gpu'] }}%
                        </span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 overflow-hidden">
                                <div
                                    data-progress-bar
                                    data-target-width="{{ $currentMetrics['gpu'] }}%"
                                    class="h-2 rounded-full transition-all duration-500 ease-out
                            @if($currentMetrics['gpu'] > 80)
                                bg-red-500
                            @elseif($currentMetrics['gpu'] > 60)
                                bg-yellow-500
                            @else
                                bg-green-500
                            @endif"
                                    style="width: 0%"
                                ></div>
                            </div>
                        </div>
                    </div>
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
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <div>
                                        <p class="font-medium text-zinc-900 dark:text-white">{{ $disk['name'] }}</p>
                                        <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ $disk['free'] }} volných
                                            z {{ $disk['size'] }}</p>
                                    </div>
                                    <span class="inline-flex px-2 py-1 rounded text-sm font-semibold
                                @if($disk['usage_percent'] > 90)
                                    bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300
                                @elseif($disk['usage_percent'] > 75)
                                    bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300
                                @else
                                    bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300
                                @endif
                            ">
                                {{ $disk['usage_percent'] }}%
                            </span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3 overflow-hidden">
                                    <div
                                        data-progress-bar
                                        data-target-width="{{ $disk['usage_percent'] }}%"
                                        class="h-3 rounded-full transition-all duration-500 ease-out
                                @if($disk['usage_percent'] > 90)
                                    bg-red-500
                                @elseif($disk['usage_percent'] > 75)
                                    bg-yellow-500
                                @else
                                    bg-green-500
                                @endif"
                                        style="width: 0%"
                                    ></div>
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
                    wire:click="closeDetail"
                    class="px-4 py-2 text-sm font-medium text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg transition-colors"
                >
                    Zavřít
                </button>
            </div>

            @script
            <script>
                let chart = null;
                let chartInitialized = false;

                function updateChart() {
                    const ctx = document.getElementById('metricsChart');
                    if (!ctx) {
                        console.error('Canvas element not found');
                        return;
                    }

                    const data = @json($chartData);

                    if (!data || !data.labels || data.labels.length === 0) {
                        console.warn('No chart data available');
                        return;
                    }

                    // Pokud graf už existuje, plynule aktualizuj pouze data
                    if (chart && chartInitialized) {
                        // Porovnej délku dat - pokud se změnila, musíme aktualizovat celé datasety
                        const dataChanged = chart.data.labels.length !== data.labels.length;

                        if (dataChanged || $wire.period !== chart.currentPeriod) {
                            // Při změně období nebo počtu dat, aktualizuj celé datasety
                            chart.data.labels = data.labels;
                            chart.data.datasets = data.datasets;
                            chart.currentPeriod = $wire.period;
                            chart.update('none');
                        } else {
                            // Při běžném pollingu pouze aktualizuj poslední hodnoty
                            data.datasets.forEach((dataset, index) => {
                                if (chart.data.datasets[index]) {
                                    // Aktualizuj pouze poslední hodnotu pro plynulý přechod
                                    const lastIndex = dataset.data.length - 1;
                                    if (lastIndex >= 0) {
                                        chart.data.datasets[index].data[lastIndex] = dataset.data[lastIndex];
                                    }
                                }
                            });
                            // Použij animaci pro plynulé přechody hodnot
                            chart.update('default');
                        }
                        return;
                    }

                    // Vytvoř nový graf pouze pokud ještě neexistuje
                    chart = new Chart(ctx, {
                        type: 'line',
                        data: data,
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            animation: {
                                duration: 250, // Krátká animace pro plynulé přechody
                                easing: 'easeInOutQuad'
                            },
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
                                        callback: function (value) {
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

                    chart.currentPeriod = $wire.period;
                    chartInitialized = true;
                    console.log('Chart initialized successfully');
                }

                // Funkce pro animaci progress barů
                function animateProgressBars() {
                    document.querySelectorAll('[data-progress-bar]').forEach(bar => {
                        const targetWidth = bar.getAttribute('data-target-width');
                        if (targetWidth) {
                            bar.style.width = targetWidth;
                        }
                    });
                }

                // Inicializace při načtení
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', () => {
                        updateChart();
                        setTimeout(animateProgressBars, 50);
                    });
                } else {
                    updateChart();
                    setTimeout(animateProgressBars, 50);
                }

                // Aktualizace při Livewire refresh (polling)
                Livewire.hook('morph.updated', ({el, component}) => {
                    if (component.name === 'customer.agent-detail') {
                        updateChart();
                        setTimeout(animateProgressBars, 50);
                    }
                });

                // Při změně období
                Livewire.on('periodChanged', () => {
                    updateChart();
                });
            </script>
            @endscript

            @assets
            <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
            @endassets
        </div>
    </div>
</div>
