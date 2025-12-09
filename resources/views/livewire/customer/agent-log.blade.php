<div class="fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4"
     x-data="{ activeTab: 'agent' }"
     @if($autoRefresh) wire:poll.5s="refreshLogs" @endif>

    <div class="bg-white dark:bg-zinc-900 rounded-lg shadow-xl w-full max-w-7xl max-h-[90vh] flex flex-col">
        @php
            $prettyName = empty($agent->pretty_name) ? $agent->hostname : $agent->pretty_name;
        @endphp

            <!-- Header -->
        <div class="flex items-center justify-between px-6 py-3 border-b border-zinc-200 dark:border-zinc-700 flex-shrink-0">
            <div class="flex items-center gap-4 flex-1">
                <div class="flex flex-col">
                    <h2 class="text-2xl font-bold text-zinc-900 dark:text-white">
                        Log: {{ $prettyName }}
                    </h2>
                    @if(!empty($agent->pretty_name))
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ $agent->hostname }}</p>
                    @endif
                </div>

                <!-- Auto-refresh toggle -->
                <button
                    wire:click="toggleAutoRefresh"
                    class="ml-4 px-3 py-2 rounded-lg text-sm font-medium transition-colors
                           {{ $autoRefresh ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400' }}">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 {{ $autoRefresh ? 'animate-spin' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        {{ $autoRefresh ? 'Auto-refresh ON' : 'Auto-refresh OFF' }}
                    </div>
                </button>

                <!-- Manual refresh -->
                <button
                    wire:click="refreshLogs"
                    class="px-3 py-2 rounded-lg text-sm font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 hover:bg-blue-200 dark:hover:bg-blue-900/50 transition-colors">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Refresh
                    </div>
                </button>
            </div>

            <button wire:click="$parent.closeLog" class="text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300 p-2 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Tabs -->
        <div class="border-b border-zinc-200 dark:border-zinc-700 px-6 flex-shrink-0">
            <div class="flex gap-4">
                <button
                    @click="activeTab = 'agent'"
                    :class="activeTab === 'agent' ? 'border-blue-600 text-blue-600' : 'border-transparent text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200'"
                    class="px-4 py-3 border-b-2 font-medium text-sm transition-colors">
                    Agent Logs
                    <span class="ml-2 px-2 py-0.5 rounded-full text-xs bg-zinc-100 dark:bg-zinc-800">
                        {{ count($agentLogs) }}
                    </span>
                </button>
                <button
                    @click="activeTab = 'system'"
                    :class="activeTab === 'system' ? 'border-blue-600 text-blue-600' : 'border-transparent text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200'"
                    class="px-4 py-3 border-b-2 font-medium text-sm transition-colors">
                    System Logs
                    <span class="ml-2 px-2 py-0.5 rounded-full text-xs bg-zinc-100 dark:bg-zinc-800">
                        {{ count($systemLogs) }}
                    </span>
                </button>
            </div>
        </div>

        <!-- Filters -->
        <div class="px-6 py-4 bg-zinc-50 dark:bg-zinc-800/50 border-b border-zinc-200 dark:border-zinc-700 flex-shrink-0" x-show="activeTab === 'agent'">
            <div class="flex gap-3 items-center flex-wrap">
                <input
                    type="text"
                    wire:model.live.debounce.300ms="searchAgent"
                    placeholder="Hledat v agent logu..."
                    class="flex-1 min-w-[200px] px-3 py-2 text-sm border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">

                <select
                    wire:model.live="filterTypeAgent"
                    class="px-3 py-2 text-sm border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Všechny typy</option>
                    <option value="info">Info</option>
                    <option value="warning">Warning</option>
                    <option value="error">Error</option>
                </select>

                @if($searchAgent || $filterTypeAgent)
                    <button
                        wire:click="clearFilters"
                        class="px-3 py-2 text-sm text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200">
                        Vymazat filtry
                    </button>
                @endif
            </div>
        </div>

        <div class="px-6 py-4 bg-zinc-50 dark:bg-zinc-800/50 border-b border-zinc-200 dark:border-zinc-700 flex-shrink-0" x-show="activeTab === 'system'">
            <div class="flex gap-3 items-center flex-wrap">
                <input
                    type="text"
                    wire:model.live.debounce.300ms="searchSystem"
                    placeholder="Hledat v system logu..."
                    class="flex-1 min-w-[200px] px-3 py-2 text-sm border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">

                <select
                    wire:model.live="filterTypeSystem"
                    class="px-3 py-2 text-sm border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Všechny typy</option>
                    <option value="info">Info</option>
                    <option value="warning">Warning</option>
                    <option value="error">Error</option>
                </select>

                @if($searchSystem || $filterTypeSystem)
                    <button
                        wire:click="clearFilters"
                        class="px-3 py-2 text-sm text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200">
                        Vymazat filtry
                    </button>
                @endif
            </div>
        </div>

        <!-- Content -->
        <div class="flex-1 overflow-y-auto p-6">
            <!-- Agent Logs -->
            <div x-show="activeTab === 'agent'">
                <x-system.logs.agent-log :logs="$agentLogs" />
            </div>

            <!-- System Logs -->
            <div x-show="activeTab === 'system'">
                <x-system.logs.system-log :logs="$systemLogs" />
            </div>
        </div>

        <!-- Footer -->
        <div class="flex justify-between items-center gap-3 px-6 py-3 border-t border-zinc-200 dark:border-zinc-700 flex-shrink-0">
            <div class="text-sm text-zinc-500">
                Poslední aktualizace: <span class="font-mono">{{ now()->format('H:i:s') }}</span>
            </div>
            <button
                wire:click="$parent.closeLog"
                class="px-4 py-2 text-sm font-medium text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg transition-colors">
                Zavřít
            </button>
        </div>
    </div>
</div>
