<div class="fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-zinc-900 rounded-lg shadow-xl w-full max-w-6xl max-h-[90vh] overflow-y-auto">
        @php
            $prettyName = empty($agent->pretty_name) ? $agent->hostname : $agent->pretty_name;
        @endphp
        <!-- Header -->
        <div class="flex items-center justify-between p-6 border-b border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between w-full">
                <div class="flex items-center gap-3">
                    <div class="flex flex-col">
                        <div class="flex items-center gap-2">
                            <h2 class="text-2xl font-bold text-zinc-900 dark:text-white">
                                Log: {{ $prettyName }}
                            </h2>
                        </div>
                        @if(! empty($agent->pretty_name))
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ $agent->hostname }}</p>
                        @endif
                    </div>
                </div>
                <button wire:click="$parent.closeLog" class="text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Content -->
        <div class="space-y-6 p-4">

            <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

                <x-system.logs.agent-log :logs="$agentLog->agent_log" />
                <x-system.logs.system-log :logs="$agentLog->system_logs" />
            </div>

        </div>

        <!-- Footer -->
        <div class="flex justify-end gap-3 p-6 border-t border-zinc-200 dark:border-zinc-700">
            <button
                wire:click="$parent.closeLog"
                class="px-4 py-2 text-sm font-medium text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg transition-colors"
            >
                Zavřít
            </button>
        </div>
    </div>
</div>
