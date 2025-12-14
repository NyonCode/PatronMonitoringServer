<div class="space-y-4 p-3 md:p-6" wire:poll.60s>
    <!-- Style pro x-cloak - nutné pro zabránění problikávání -->
    <style>
        [x-cloak] { display: none !important; }
    </style>

    <!-- Header se statistikami -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <!-- Celkem agentů -->
        <div class="bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-700 p-4">
            <div class="flex flex-col space-y-2">
                <div class="flex items-center justify-between">
                    <p class="text-xs md:text-sm text-zinc-600 dark:text-zinc-400">Celkem agentů</p>
                    <svg class="w-8 h-8 md:w-10 md:h-10 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20m0 0l-.75 3M9 20a6 6 0 1112 0m0 0l.75 3M21 20l.75 3"></path>
                    </svg>
                </div>
                <p class="text-2xl md:text-3xl font-bold text-zinc-900 dark:text-white">{{ $this->agents->total() }}</p>
            </div>
        </div>

        <!-- Online -->
        <div class="bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-700 p-4">
            <div class="flex flex-col space-y-2">
                <div class="flex items-center justify-between">
                    <p class="text-xs md:text-sm text-zinc-600 dark:text-zinc-400">Online</p>
                    <svg class="w-8 h-8 md:w-10 md:h-10 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"></path>
                    </svg>
                </div>
                <p class="text-2xl md:text-3xl font-bold text-green-600">{{ $this->agents->filter(fn($agent) => $this->getAgentStatus($agent) === 'online')->count() }}</p>
            </div>
        </div>

        <!-- Offline -->
        <div class="bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-700 p-4">
            <div class="flex flex-col space-y-2">
                <div class="flex items-center justify-between">
                    <p class="text-xs md:text-sm text-zinc-600 dark:text-zinc-400">Offline</p>
                    <svg class="w-8 h-8 md:w-10 md:h-10 text-red-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12 19 6.41z"></path>
                    </svg>
                </div>
                <p class="text-2xl md:text-3xl font-bold text-red-600">{{ $this->agents->filter(fn($agent) => $this->getAgentStatus($agent) === 'offline')->count() }}</p>
            </div>
        </div>

        <!-- Varování -->
        <div class="bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-700 p-4">
            <div class="flex flex-col space-y-2">
                <div class="flex items-center justify-between">
                    <p class="text-xs md:text-sm text-zinc-600 dark:text-zinc-400">Varování</p>
                    <svg class="w-8 h-8 md:w-10 md:h-10 text-yellow-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"></path>
                    </svg>
                </div>
                <p class="text-2xl md:text-3xl font-bold text-yellow-600">{{ $this->agents->filter(function($agent) {
                    $metrics = $this->getCurrentMetrics($agent);
                    return $metrics['cpu'] > 80 || $metrics['ram'] > 80;
                })->count() }}</p>
            </div>
        </div>
    </div>

    <!-- Vyhledávání a filtry -->
    <div class="bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-700 p-4">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1">
                <div class="relative">
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Hledat agenta..."
                        class="w-full px-4 py-2.5 pl-10 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white placeholder-zinc-500 dark:placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                    />
                    <svg class="absolute left-3 top-3 w-5 h-5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>
            <select wire:model.live="perPage" class="px-3 py-2.5 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm min-w-[120px]">
                <option value="5">5 / stránka</option>
                <option value="10">10 / stránka</option>
                <option value="25">25 / stránka</option>
                <option value="50">50 / stránka</option>
            </select>
        </div>
    </div>

    <!-- Desktop Table View - table-fixed bez horizontálního scrollu -->
    <div class="hidden lg:block bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-700">
        <table class="w-full table-fixed">
            <thead class="bg-zinc-50 dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
            <tr>
                <th class="w-[18%] px-3 py-3 text-left">
                    <button wire:click="sortBy('hostname')" class="flex items-center gap-1 font-semibold text-sm text-zinc-700 dark:text-zinc-300 hover:text-zinc-900 dark:hover:text-zinc-100">
                        Název
                        @if($sortBy === 'hostname')
                            <svg class="w-3 h-3 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path d="{{ $sortDirection === 'asc' ? 'M5 10l5-5 5 5H5z' : 'M5 10l5 5 5-5H5z' }}"></path>
                            </svg>
                        @endif
                    </button>
                </th>
                <th class="w-[9%] px-3 py-3 text-left"><span class="font-semibold text-sm text-zinc-700 dark:text-zinc-300">Status</span></th>
                <th class="w-[13%] px-3 py-3 text-left">
                    <button wire:click="sortBy('ip_address')" class="flex items-center gap-1 font-semibold text-sm text-zinc-700 dark:text-zinc-300 hover:text-zinc-900 dark:hover:text-zinc-100">
                        IP adresa
                        @if($sortBy === 'ip_address')
                            <svg class="w-3 h-3 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path d="{{ $sortDirection === 'asc' ? 'M5 10l5-5 5 5H5z' : 'M5 10l5 5 5-5H5z' }}"></path>
                            </svg>
                        @endif
                    </button>
                </th>
                <th class="w-[12%] px-3 py-3 text-left"><span class="font-semibold text-sm text-zinc-700 dark:text-zinc-300">CPU</span></th>
                <th class="w-[12%] px-3 py-3 text-left"><span class="font-semibold text-sm text-zinc-700 dark:text-zinc-300">RAM</span></th>
                <th class="w-[12%] px-3 py-3 text-left"><span class="font-semibold text-sm text-zinc-700 dark:text-zinc-300">GPU</span></th>
                <th class="w-[12%] px-3 py-3 text-left"><span class="font-semibold text-sm text-zinc-700 dark:text-zinc-300">Disk</span></th>
                <th class="w-[12%] px-3 py-3 text-center"><span class="font-semibold text-sm text-zinc-700 dark:text-zinc-300">Akce</span></th>
            </tr>
            </thead>
            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
            @forelse($this->agents as $index => $agent)
                @php
                    $status = $this->getAgentStatus($agent);
                    $metrics = $this->isOnline($agent) ? $this->getCurrentMetrics($agent) : [];
                    $disk = $this->getMostUsedDisk($agent);
                    $name = empty($agent->pretty_name) ? $agent->hostname : $agent->pretty_name;
                    $isNearBottom = $index >= ($this->agents->count() - 2);
                @endphp
                <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors"
                    x-data="{
                        cpu: {{ $metrics['cpu'] ?? 0 }},
                        ram: {{ $metrics['ram'] ?? 0 }},
                        gpu: {{ $metrics['gpu'] ?? 0 }}
                    }">
                    <!-- Název -->
                    <td class="px-3 py-3">
                        <div class="min-w-0">
                            <div class="font-medium text-zinc-900 dark:text-zinc-100 truncate" title="{{ $name }}">{{ $name }}</div>
                            @if(!empty($agent->pretty_name))
                                <div class="text-xs text-zinc-500 dark:text-zinc-400 truncate" title="{{ $agent->hostname }}">{{ $agent->hostname }}</div>
                            @endif
                        </div>
                    </td>
                    <!-- Status -->
                    <td class="px-3 py-3">
                        @if($status === 'online')
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
                                <span class="w-1.5 h-1.5 bg-green-500 rounded-full shrink-0"></span>
                                Online
                            </span>
                        @elseif($status === 'shutdown')
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-900/30 text-gray-800 dark:text-gray-300">
                                <span class="w-1.5 h-1.5 bg-gray-500 rounded-full shrink-0"></span>
                                Shutdown
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300">
                                <span class="w-1.5 h-1.5 bg-red-500 rounded-full shrink-0"></span>
                                Offline
                            </span>
                        @endif
                    </td>
                    <!-- IP -->
                    <td class="px-3 py-3">
                        <span class="text-sm text-zinc-700 dark:text-zinc-300 font-mono truncate block">{{ $agent->ip_address ?? 'N/A' }}</span>
                    </td>
                    <!-- CPU -->
                    <td class="px-3 py-3">
                        <div class="space-y-1">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-bold transition-colors"
                                      :class="{
                                          'text-red-600 dark:text-red-400': cpu > 80,
                                          'text-yellow-600 dark:text-yellow-400': cpu > 60 && cpu <= 80,
                                          'text-green-600 dark:text-green-400': cpu > 0 && cpu <= 60,
                                          'text-zinc-400': cpu === 0
                                      }"
                                      x-text="cpu + '%'"></span>
                            </div>
                            <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-1.5">
                                <div class="h-1.5 rounded-full transition-all duration-500"
                                     :class="{
                                         'bg-red-500': cpu > 80,
                                         'bg-yellow-500': cpu > 60 && cpu <= 80,
                                         'bg-green-500': cpu > 0 && cpu <= 60,
                                         'bg-gray-400': cpu === 0
                                     }"
                                     :style="`width: ${cpu}%`"></div>
                            </div>
                        </div>
                    </td>
                    <!-- RAM -->
                    <td class="px-3 py-3">
                        <div class="space-y-1">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-bold transition-colors"
                                      :class="{
                                          'text-red-600 dark:text-red-400': ram > 80,
                                          'text-yellow-600 dark:text-yellow-400': ram > 60 && ram <= 80,
                                          'text-green-600 dark:text-green-400': ram > 0 && ram <= 60,
                                          'text-zinc-400': ram === 0
                                      }"
                                      x-text="ram + '%'"></span>
                            </div>
                            <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-1.5">
                                <div class="h-1.5 rounded-full transition-all duration-500"
                                     :class="{
                                         'bg-red-500': ram > 80,
                                         'bg-yellow-500': ram > 60 && ram <= 80,
                                         'bg-green-500': ram > 0 && ram <= 60,
                                         'bg-gray-400': ram === 0
                                     }"
                                     :style="`width: ${ram}%`"></div>
                            </div>
                        </div>
                    </td>
                    <!-- GPU -->
                    <td class="px-3 py-3">
                        <div class="space-y-1">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-bold transition-colors"
                                      :class="{
                                          'text-red-600 dark:text-red-400': gpu > 80,
                                          'text-yellow-600 dark:text-yellow-400': gpu > 60 && gpu <= 80,
                                          'text-green-600 dark:text-green-400': gpu > 0 && gpu <= 60,
                                          'text-zinc-400': gpu === 0
                                      }"
                                      x-text="gpu + '%'"></span>
                            </div>
                            <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-1.5">
                                <div class="h-1.5 rounded-full transition-all duration-500"
                                     :class="{
                                         'bg-red-500': gpu > 80,
                                         'bg-yellow-500': gpu > 60 && gpu <= 80,
                                         'bg-green-500': gpu > 0 && gpu <= 60,
                                         'bg-gray-400': gpu === 0
                                     }"
                                     :style="`width: ${gpu}%`"></div>
                            </div>
                        </div>
                    </td>
                    <!-- Disk -->
                    <td class="px-3 py-3">
                        @if($disk)
                            <div class="space-y-1">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-bold
                                        @if($disk['usage_percent'] > 90) text-red-600 dark:text-red-400
                                        @elseif($disk['usage_percent'] > 75) text-yellow-600 dark:text-yellow-400
                                        @else text-green-600 dark:text-green-400 @endif">
                                        {{ $disk['usage_percent'] }}%
                                    </span>
                                    <span class="text-xs text-zinc-500 dark:text-zinc-400 truncate ml-1">{{ $disk['name'] }}</span>
                                </div>
                                <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-1.5">
                                    <div class="h-1.5 rounded-full transition-all duration-500
                                        @if($disk['usage_percent'] > 90) bg-red-500
                                        @elseif($disk['usage_percent'] > 75) bg-yellow-500
                                        @else bg-green-500 @endif"
                                         style="width: {{ $disk['usage_percent'] }}%"></div>
                                </div>
                                <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $disk['free'] }} {{ __('free space out of') }} {{ $disk['total'] }}</div>
                            </div>
                        @else
                            <div class="space-y-1">
                                <span class="text-sm font-bold text-zinc-400">N/A</span>
                                <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-1.5">
                                    <div class="h-1.5 rounded-full bg-gray-400" style="width: 0%"></div>
                                </div>
                            </div>
                        @endif
                    </td>
                    <!-- Akce - Smart dropdown s dynamickým pozicováním -->
                    <td class="px-3 py-3">
                        <div x-data="{
                                open: false,
                                dropUp: false,
                                checkPosition() {
                                    const btn = this.$refs.dropdownBtn;
                                    if (!btn) return;
                                    const rect = btn.getBoundingClientRect();
                                    const spaceBelow = window.innerHeight - rect.bottom;
                                    this.dropUp = spaceBelow < 220;
                                }
                             }"
                             @click.outside="open = false"
                             class="relative flex justify-center">
                            <button x-ref="dropdownBtn"
                                    @click="checkPosition(); open = !open"
                                    class="p-1.5 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors">
                                <svg class="w-5 h-5 text-zinc-600 dark:text-zinc-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 12.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 18.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5Z" />
                                </svg>
                            </button>
                            <div x-cloak
                                 x-show="open"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 :class="dropUp ? 'bottom-full mb-1' : 'top-full mt-1'"
                                 class="absolute right-0 w-44 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg shadow-xl z-50 py-1">
                                <button wire:click="showDetail({{ $agent->id }})" @click="open = false" class="flex items-center gap-2 w-full px-3 py-2 text-sm text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-700 transition-colors">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    {{ __('Detail') }}
                                </button>
                                <button wire:click="showLog({{ $agent->id }})" @click="open = false" class="flex items-center gap-2 w-full px-3 py-2 text-sm text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-700 transition-colors">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                    </svg>
                                    {{ __('Log') }}
                                </button>
                                <button disabled class="flex items-center gap-2 w-full px-3 py-2 text-sm text-zinc-400 dark:text-zinc-600 cursor-not-allowed">
                                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-4 h-4 shrink-0">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.78.93.398.164.855.142 1.205-.108l.737-.527a1.125 1.125 0 0 1 1.45.12l.773.774c.39.389.44 1.002.12 1.45l-.527.737c-.25.35-.272.806-.107 1.204.165.397.505.71.93.78l.893.15c.543.09.94.559.94 1.109v1.094c0 .55-.397 1.02-.94 1.11l-.894.149c-.424.07-.764.383-.929.78-.165.398-.143.854.107 1.204l.527.738c.32.447.269 1.06-.12 1.45l-.774.773a1.125 1.125 0 0 1-1.449.12l-.738-.527c-.35-.25-.806-.272-1.203-.107-.398.165-.71.505-.781.929l-.149.894c-.09.542-.56.94-1.11.94h-1.094c-.55 0-1.019-.398-1.11-.94l-.148-.894c-.071-.424-.384-.764-.781-.93-.398-.164-.854-.142-1.204.108l-.738.527c-.447.32-1.06.269-1.45-.12l-.773-.774a1.125 1.125 0 0 1-.12-1.45l.527-.737c.25-.35.272-.806.108-1.204-.165-.397-.506-.71-.93-.78l-.894-.15c-.542-.09-.94-.56-.94-1.109v-1.094c0-.55.398-1.02.94-1.11l.894-.149c.424-.07.765-.383.93-.78.165-.398.143-.854-.108-1.204l-.526-.738a1.125 1.125 0 0 1 .12-1.45l.773-.773a1.125 1.125 0 0 1 1.45-.12l.737.527c.35.25.807.272 1.204.107.397-.165.71-.505.78-.929l.15-.894Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                    {{ __('agents.configuration') }}
                                </button>
                                <div class="border-t border-zinc-200 dark:border-zinc-700 my-1"></div>
                                <button wire:click="showDelete({{ $agent }})" @click="open = false" class="flex items-center gap-2 w-full px-3 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-4 h-4 shrink-0">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                    </svg>
                                    {{ __('agents.delete') }}
                                </button>
                            </div>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center gap-3">
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
        @if($this->agents->hasPages())
            <div class="px-4 py-3 border-t border-zinc-200 dark:border-zinc-700">
                {{ $this->agents->links() }}
            </div>
        @endif
    </div>

    <!-- Mobile Card View -->
    <div class="lg:hidden space-y-3">
        @forelse($this->agents as $agent)
            @php
                $status = $this->getAgentStatus($agent);
                $metrics = $this->isOnline($agent) ? $this->getCurrentMetrics($agent) : [];
                $disk = $this->getMostUsedDisk($agent);
                $name = empty($agent->pretty_name) ? $agent->hostname : $agent->pretty_name;
            @endphp
            <div class="bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-700 p-4"
                 x-data="{
                     cpu: {{ $metrics['cpu'] ?? 0 }},
                     ram: {{ $metrics['ram'] ?? 0 }},
                     gpu: {{ $metrics['gpu'] ?? 0 }},
                     showDetails: false
                 }">
                <!-- Header s názvem a statusem -->
                <div class="flex items-start justify-between mb-3">
                    <div class="flex-1 min-w-0">
                        <h3 class="font-semibold text-zinc-900 dark:text-white truncate">{{ $name }}</h3>
                        @if(!empty($agent->pretty_name))
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 truncate">{{ $agent->hostname }}</p>
                        @endif
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 font-mono mt-1">{{ $agent->ip_address ?? 'N/A' }}</p>
                    </div>
                    <div class="ml-3 shrink-0">
                        @if($status === 'online')
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
                                <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                                Online
                            </span>
                        @elseif($status === 'shutdown')
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-900/30 text-gray-800 dark:text-gray-300">
                                <span class="w-1.5 h-1.5 bg-gray-500 rounded-full"></span>
                                Shutdown
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300">
                                <span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span>
                                Offline
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Rychlý přehled metrik - 3 sloupce -->
                <div class="grid grid-cols-3 gap-2 mb-3">
                    <!-- CPU -->
                    <div class="bg-zinc-50 dark:bg-zinc-800 rounded-lg p-2">
                        <div class="text-[10px] text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">CPU</div>
                        <div class="text-base font-bold transition-colors"
                             :class="{
                                 'text-red-600 dark:text-red-400': cpu > 80,
                                 'text-yellow-600 dark:text-yellow-400': cpu > 60 && cpu <= 80,
                                 'text-green-600 dark:text-green-400': cpu > 0 && cpu <= 60,
                                 'text-zinc-400': cpu === 0
                             }"
                             x-text="cpu + '%'"></div>
                        <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-1 mt-1">
                            <div class="h-1 rounded-full transition-all duration-500"
                                 :class="{
                                     'bg-red-500': cpu > 80,
                                     'bg-yellow-500': cpu > 60 && cpu <= 80,
                                     'bg-green-500': cpu > 0 && cpu <= 60,
                                     'bg-gray-400': cpu === 0
                                 }"
                                 :style="`width: ${cpu}%`"></div>
                        </div>
                    </div>

                    <!-- RAM -->
                    <div class="bg-zinc-50 dark:bg-zinc-800 rounded-lg p-2">
                        <div class="text-[10px] text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">RAM</div>
                        <div class="text-base font-bold transition-colors"
                             :class="{
                                 'text-red-600 dark:text-red-400': ram > 80,
                                 'text-yellow-600 dark:text-yellow-400': ram > 60 && ram <= 80,
                                 'text-green-600 dark:text-green-400': ram > 0 && ram <= 60,
                                 'text-zinc-400': ram === 0
                             }"
                             x-text="ram + '%'"></div>
                        <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-1 mt-1">
                            <div class="h-1 rounded-full transition-all duration-500"
                                 :class="{
                                     'bg-red-500': ram > 80,
                                     'bg-yellow-500': ram > 60 && ram <= 80,
                                     'bg-green-500': ram > 0 && ram <= 60,
                                     'bg-gray-400': ram === 0
                                 }"
                                 :style="`width: ${ram}%`"></div>
                        </div>
                    </div>

                    <!-- GPU -->
                    <div class="bg-zinc-50 dark:bg-zinc-800 rounded-lg p-2">
                        <div class="text-[10px] text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">GPU</div>
                        <div class="text-base font-bold transition-colors"
                             :class="{
                                 'text-red-600 dark:text-red-400': gpu > 80,
                                 'text-yellow-600 dark:text-yellow-400': gpu > 60 && gpu <= 80,
                                 'text-green-600 dark:text-green-400': gpu > 0 && gpu <= 60,
                                 'text-zinc-400': gpu === 0
                             }"
                             x-text="gpu + '%'"></div>
                        <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-1 mt-1">
                            <div class="h-1 rounded-full transition-all duration-500"
                                 :class="{
                                     'bg-red-500': gpu > 80,
                                     'bg-yellow-500': gpu > 60 && gpu <= 80,
                                     'bg-green-500': gpu > 0 && gpu <= 60,
                                     'bg-gray-400': gpu === 0
                                 }"
                                 :style="`width: ${gpu}%`"></div>
                        </div>
                    </div>
                </div>

                <!-- Expandable disk info -->
                    <div x-show="showDetails" x-collapse class="mb-3">
                        @if($disk)
                            <div class="bg-zinc-50 dark:bg-zinc-800 rounded-lg p-3">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-[10px] text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">{{ $disk['name'] }}</span>
                                    <span class="text-sm font-bold
                                        @if($disk['usage_percent'] > 90) text-red-600 dark:text-red-400
                                        @elseif($disk['usage_percent'] > 75) text-yellow-600 dark:text-yellow-400
                                        @else text-green-600 dark:text-green-400 @endif">
                                        {{ $disk['usage_percent'] }}%
                                    </span>
                                </div>
                                <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-1.5 mb-1">
                                    <div class="h-1.5 rounded-full transition-all duration-500
                                        @if($disk['usage_percent'] > 90) bg-red-500
                                        @elseif($disk['usage_percent'] > 75) bg-yellow-500
                                        @else bg-green-500 @endif"
                                         style="width: {{ $disk['usage_percent'] }}%"></div>
                                </div>
                                <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $disk['free'] }} {{ __('free space out of') }} {{ $disk['total'] }}</div>
                            </div>
                        @else
                            <div class="bg-zinc-50 dark:bg-zinc-800 rounded-lg p-3">
                                <div class="text-[10px] text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Disk</div>
                                <div class="text-sm font-bold text-zinc-400">N/A</div>
                            </div>
                        @endif
                    </div>

                <!-- Primary action buttons -->
                <div class="flex items-center gap-2 pt-3 border-t border-zinc-200 dark:border-zinc-700">
                    <button @click="showDetails = !showDetails" class="flex-1 inline-flex items-center justify-center gap-1 px-3 py-2 text-xs font-medium text-zinc-700 dark:text-zinc-300 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 rounded-lg transition-colors">
                        <svg class="w-4 h-4 transition-transform duration-200" :class="showDetails && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                        <span x-text="showDetails ? 'Méně' : 'Více'"></span>
                    </button>
                    <button wire:click="showDetail({{ $agent->id }})" class="flex-1 inline-flex items-center justify-center gap-1 px-3 py-2 text-xs font-medium text-blue-700 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/30 hover:bg-blue-100 dark:hover:bg-blue-900/50 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        Detail
                    </button>
                    <button wire:click="showLog({{ $agent->id }})" class="flex-1 inline-flex items-center justify-center gap-1 px-3 py-2 text-xs font-medium text-zinc-700 dark:text-zinc-300 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                        Log
                    </button>
                </div>

                <!-- Secondary actions - expandable -->
                <template x-if="showDetails">
                    <div class="flex items-center gap-2 pt-3" x-show="showDetails" x-collapse>
                        <button disabled class="flex-1 inline-flex items-center justify-center gap-1 px-3 py-2 text-xs font-medium text-zinc-400 dark:text-zinc-600 bg-zinc-100 dark:bg-zinc-800 rounded-lg cursor-not-allowed">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.78.93.398.164.855.142 1.205-.108l.737-.527a1.125 1.125 0 0 1 1.45.12l.773.774c.39.389.44 1.002.12 1.45l-.527.737c-.25.35-.272.806-.107 1.204.165.397.505.71.93.78l.893.15c.543.09.94.559.94 1.109v1.094c0 .55-.397 1.02-.94 1.11l-.894.149c-.424.07-.764.383-.929.78-.165.398-.143.854.107 1.204l.527.738c.32.447.269 1.06-.12 1.45l-.774.773a1.125 1.125 0 0 1-1.449.12l-.738-.527c-.35-.25-.806-.272-1.203-.107-.398.165-.71.505-.781.929l-.149.894c-.09.542-.56.94-1.11.94h-1.094c-.55 0-1.019-.398-1.11-.94l-.148-.894c-.071-.424-.384-.764-.781-.93-.398-.164-.854-.142-1.204.108l-.738.527c-.447.32-1.06.269-1.45-.12l-.773-.774a1.125 1.125 0 0 1-.12-1.45l.527-.737c.25-.35.272-.806.108-1.204-.165-.397-.506-.71-.93-.78l-.894-.15c-.542-.09-.94-.56-.94-1.109v-1.094c0-.55.398-1.02.94-1.11l.894-.149c.424-.07.765-.383.93-.78.165-.398.143-.854-.108-1.204l-.526-.738a1.125 1.125 0 0 1 .12-1.45l.773-.773a1.125 1.125 0 0 1 1.45-.12l.737.527c.35.25.807.272 1.204.107.397-.165.71-.505.78-.929l.15-.894Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                            {{ __('agents.configuration') }}
                        </button>
                        <button wire:click="showDelete({{ $agent }})" class="flex-1 inline-flex items-center justify-center gap-1 px-3 py-2 text-xs font-medium text-red-700 dark:text-red-300 bg-red-100 dark:bg-red-900/30 hover:bg-red-200 dark:hover:bg-red-900/50 rounded-lg transition-colors">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                            </svg>
                            {{ __('agents.delete') }}
                        </button>
                    </div>
                </template>
            </div>
        @empty
            <div class="bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-700 p-8">
                <div class="flex flex-col items-center gap-3 text-center">
                    <svg class="w-12 h-12 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                    <div>
                        <p class="font-semibold text-zinc-900 dark:text-white mb-1">
                            {{ __('No agents found') }}
                        </p>
                        @if(!empty($search))
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                {{ __('Try adjusting your search criteria') }}
                            </p>
                        @else
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                {{ __('You can download the agent') }}
                                <a href="#" class="text-blue-600 underline ml-1">
                                    {{ __('here') }}
                                </a>
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        @endforelse

        <!-- Mobile Pagination -->
        @if($this->agents->hasPages())
            <div class="bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-700 p-4">
                {{ $this->agents->links() }}
            </div>
        @endif
    </div>

    <!-- Modals -->
    @if($showDetailModal && $selectedAgentId)
        @livewire('customer.agent-detail', ['agent' => $this->agents->find($selectedAgentId)], key('agent-detail-'.$selectedAgentId))
    @endif

    @if($showLogModal && $selectedAgentId)
        @livewire('customer.agent-log', ['agent' => $this->agents->find($selectedAgentId)], key('agent-log-'.$selectedAgentId))
    @endif

    @if($showDeleteModal && $selectedAgentId)
        @livewire('customer.agent-delete', ['agent' => $this->agents->find($selectedAgentId)], key('agent-delete-'.$selectedAgentId))
    @endif
</div>
