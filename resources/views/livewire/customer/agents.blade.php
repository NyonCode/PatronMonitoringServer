<div class="space-y-6 p-4 md:p-6" wire:poll.5s>
    <!-- Header se statistikami -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-2">Celkem agentů</p>
                    <p class="text-3xl font-bold text-zinc-900 dark:text-white">{{ $this->agents->total() }}</p>
                </div>
                <svg class="w-12 h-12 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20m0 0l-.75 3M9 20a6 6 0 1112 0m0 0l.75 3M21 20l.75 3"></path>
                </svg>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-2">Online</p>
                    <p class="text-3xl font-bold text-green-600">{{ $this->agents->filter(fn($agent) => $this->getAgentStatus($agent) === 'online')->count() }}</p>
                </div>
                <svg class="w-12 h-12 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"></path>
                </svg>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-2">Offline</p>
                    <p class="text-3xl font-bold text-red-600">{{ $this->agents->filter(fn($agent) => $this->getAgentStatus($agent) === 'offline')->count() }}</p>
                </div>
                <svg class="w-12 h-12 text-red-600" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12 19 6.41z"></path>
                </svg>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-2">Varování</p>
                    <p class="text-3xl font-bold text-yellow-600">{{ $this->agents->filter(function($agent) {
                        $metrics = $this->getCurrentMetrics($agent);
                        return $metrics['cpu'] > 80 || $metrics['ram'] > 80;
                    })->count() }}</p>
                </div>
                <svg class="w-12 h-12 text-yellow-600" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Vyhledávání a filtry -->
    <div class="bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex-1">
                <div class="relative">
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Hledat podle názvu, pretty name nebo IP adresy..."
                        class="w-full px-4 py-2 pl-10 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white placeholder-zinc-500 dark:placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                    <svg class="absolute left-3 top-2.5 w-5 h-5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>
            <select wire:model.live="perPage" class="px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="5">5 / stránka</option>
                <option value="10">10 / stránka</option>
                <option value="25">25 / stránka</option>
                <option value="50">50 / stránka</option>
            </select>
        </div>
    </div>

    <!-- Tabulka agentů -->
    <div class="bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-zinc-50 dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
                <tr>
                    <th class="px-6 py-3 text-left">
                        <button
                            wire:click="sortBy('hostname')"
                            class="flex items-center gap-2 font-semibold text-sm text-zinc-700 dark:text-zinc-300 hover:text-zinc-900 dark:hover:text-zinc-100"
                        >
                            Název
                            @if($sortBy === 'hostname')
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M3 8l2.828-2.828a2 2 0 112.828 0L9 8m2 0l2.828-2.828a2 2 0 112.828 0L17 8m-8 8l2.828-2.828a2 2 0 112.828 0L17 16" stroke="currentColor" stroke-width="2" fill="none"></path>
                                </svg>
                            @endif
                        </button>
                    </th>
                    <th class="px-6 py-3 text-left">
                        <span class="font-semibold text-sm text-zinc-700 dark:text-zinc-300">Status</span>
                    </th>
                    <th class="px-6 py-3 text-left">
                        <button
                            wire:click="sortBy('ip_address')"
                            class="flex items-center gap-2 font-semibold text-sm text-zinc-700 dark:text-zinc-300 hover:text-zinc-900 dark:hover:text-zinc-100"
                        >
                            IP adresa
                            @if($sortBy === 'ip_address')
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M3 8l2.828-2.828a2 2 0 112.828 0L9 8m2 0l2.828-2.828a2 2 0 112.828 0L17 8m-8 8l2.828-2.828a2 2 0 112.828 0L17 16" stroke="currentColor" stroke-width="2" fill="none"></path>
                                </svg>
                            @endif
                        </button>
                    </th>
                    <th class="px-6 py-3 text-left">
                        <span class="font-semibold text-sm text-zinc-700 dark:text-zinc-300">CPU</span>
                    </th>
                    <th class="px-6 py-3 text-left">
                        <span class="font-semibold text-sm text-zinc-700 dark:text-zinc-300">RAM</span>
                    </th>
                    <th class="px-6 py-3 text-left">
                        <span class="font-semibold text-sm text-zinc-700 dark:text-zinc-300">GPU</span>
                    </th>
                    <th class="px-6 py-3 text-left">
                        <span class="font-semibold text-sm text-zinc-700 dark:text-zinc-300">Disk</span>
                    </th>
                    <th class="px-6 py-3 text-right">
                        <span class="font-semibold text-sm text-zinc-700 dark:text-zinc-300">Akce</span>
                    </th>
                </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                @forelse($this->agents as $agent)
                    @dump($agent)
                    @php
                        $status = $this->getAgentStatus($agent);
                        $metrics = $this->getCurrentMetrics($agent);
                        $disk = $this->getMostUsedDisk($agent);
                        $sparkline = $this->getSparklineData($agent);
                        $name = empty($agent->pretty_name) $agent->hostname : $agent->pretty_name;
                    @endphp
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors" 
                        x-data="{
                            cpu: {{ $metrics['cpu'] ?? 0 }},
                            ram: {{ $metrics['ram'] ?? 0 }},
                            gpu: {{ $metrics['gpu'] ?? 0 }}
                        }">
                        <!-- Název -->
                        <td class="px-6 py-4">
                            <div>
                                <div class="font-medium text-zinc-900 dark:text-zinc-100">
                                    {{ $name }}
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
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"></path>
                                    </svg>
                                    Online
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm font-medium bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"></path>
                                    </svg>
                                    Offline
                                </span>
                            @endif
                        </td>

                        <!-- IP adresa -->
                        <td class="px-6 py-4">
                            <span class="text-sm text-zinc-700 dark:text-zinc-300 font-mono">
                                {{ $agent->ip_address ?? 'N/A' }}
                            </span>
                        </td>

                        <!-- CPU -->
                        <td class="px-6 py-4">
                            <div class="space-y-1 min-w-[120px]">
                                <div class="flex items-center justify-end gap-2">
                                    <!--
                                    <span class="text-sm text-zinc-700 dark:text-zinc-300">CPU</span>
                                    -->
                                    <span class="inline-flex px-2 py-1 rounded text-sm font-semibold transition-colors duration-300"
                                          :class="{
                                              'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300': cpu > 80,
                                              'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300': cpu > 60 && cpu <= 80,
                                              'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300': cpu > 0 && cpu <= 60,
                                              'bg-gray-100 dark:bg-gray-900/30 text-gray-800 dark:text-gray-300': cpu === 0
                                          }"
                                          x-text="cpu + '%'"></span>
                                </div>
                                <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-2">
                                    <div class="h-2 rounded-full transition-all duration-500"
                                         :class="{
                                             'bg-red-500': cpu > 80,
                                             'bg-yellow-500': cpu > 60 && cpu <= 80,
                                             'bg-green-500': cpu > 0 && cpu <= 60,
                                             'bg-gray-400': cpu === 0
                                         }"
                                         :style="`width: ${cpu}%`"></div>
                                </div>
                                <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                    {{-- $disk['free'] }} / {{ $disk['size'] --}}
                                </div>
                            </div>
                        </td>

                        <!-- RAM -->
                        <td class="px-6 py-4">
                            <div class="space-y-1 min-w-[120px]">
                                <div class="flex items-center justify-end gap-2">
                                    <!--
                                    <span class="text-sm text-zinc-700 dark:text-zinc-300">RAM</span>
                                    -->
                                    <span class="inline-flex px-2 py-1 rounded text-sm font-semibold transition-colors duration-300"
                                          :class="{
                                              'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300': ram > 80,
                                              'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300': ram > 60 && ram <= 80,
                                              'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300': ram > 0 && ram <= 60,
                                              'bg-gray-100 dark:bg-gray-900/30 text-gray-800 dark:text-gray-300': ram === 0
                                          }"
                                          x-text="ram + '%'"></span>
                                </div>
                                <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-2">
                                    <div class="h-2 rounded-full transition-all duration-500"
                                         :class="{
                                             'bg-red-500': ram > 80,
                                             'bg-yellow-500': ram > 60 && ram <= 80,
                                             'bg-green-500': ram > 0 && ram <= 60,
                                             'bg-gray-400': ram === 0
                                         }"
                                         :style="`width: ${ram}%`"></div>
                                </div>
                                <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                    {{-- $disk['free'] }} / {{ $disk['size'] --}}
                                </div>
                            </div>
                        </td>

                        <!-- GPU -->
                        <td class="px-6 py-4">
                            <div class="space-y-1 min-w-[120px]">
                                <div class="flex items-center justify-end gap-2">
                                    <!--
                                    <span class="text-sm text-zinc-700 dark:text-zinc-300">GPU</span>
                                    -->
                                    <span class="inline-flex px-2 py-1 rounded text-sm font-semibold transition-colors duration-300"
                                          :class="{
                                              'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300': gpu > 80,
                                              'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300': gpu > 60 && gpu <= 80,
                                              'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300': gpu > 0 && gpu <= 60,
                                              'bg-gray-100 dark:bg-gray-900/30 text-gray-800 dark:text-gray-300': gpu === 0
                                          }"
                                          x-text="gpu + '%'"></span>
                                </div>
                                <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-2">
                                    <div class="h-2 rounded-full transition-all duration-500"
                                         :class="{
                                             'bg-red-500': gpu > 80,
                                             'bg-yellow-500': gpu > 60 && gpu <= 80,
                                             'bg-green-500': gpu > 0 && gpu <= 60,
                                             'bg-gray-400': gpu === 0
                                         }"
                                         :style="`width: ${gpu}%`"></div>
                                </div>
                                <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                    {{-- $disk['free'] }} / {{ $disk['size'] --}}
                                </div>
                            </div>
                        </td>

                        <!-- Nejvíce zaplněný disk -->
                        <td class="px-6 py-4">
                            @if($disk)
                                <div class="space-y-1 min-w-[150px]">
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="text-sm text-zinc-700 dark:text-zinc-300">{{ $disk['name'] }}</span>
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
                                    <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-2">
                                        <div
                                            class="h-2 rounded-full transition-all duration-500
                                            @if($disk['usage_percent'] > 90)
                                                bg-red-500
                                            @elseif($disk['usage_percent'] > 75)
                                                bg-yellow-500
                                            @else
                                                bg-green-500
                                            @endif
                                            "
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
                            <button
                                wire:click="showDetail({{ $agent->id }})"
                                class="inline-flex items-center gap-1 px-3 py-2 text-sm font-medium text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg transition-colors"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                Detail
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="w-12 h-12 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                </svg>
                                <p class="font-semibold text-zinc-900 dark:text-white">Žádní agenti nenalezeni</p>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">Zkuste upravit vyhledávací kritéria</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if($this->agents->hasPages())
            <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700">
                {{ $this->agents->links() }}
            </div>
        @endif
    </div>

    <!-- Modal s detaily agenta -->
    @if($showDetailModal && $selectedAgentId)
        @livewire('customer.agent-detail', ['agent' => $this->agents->find($selectedAgentId)], key('agent-detail-'.$selectedAgentId))
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

                ctx.clearRect(0, 0, width, height);
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
                ctx.lineTo(width, height);
                ctx.lineTo(0, height);
                ctx.closePath();
                ctx.fillStyle = color.replace('rgb', 'rgba').replace(')', ', 0.1)');
                ctx.fill();
            });
        }

        document.addEventListener('DOMContentLoaded', initSparklines);

        let debounceTimer;
        Livewire.hook('morph.updated', () => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(initSparklines, 50);
        });
    </script>
    @endscript
</div>
