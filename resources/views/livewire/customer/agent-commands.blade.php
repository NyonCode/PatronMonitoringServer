<div class="fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4"
     wire:poll.3s>

    <div class="bg-white dark:bg-zinc-900 rounded-lg shadow-xl w-full max-w-5xl max-h-[90vh] flex flex-col">
        @php
            $prettyName = $agent->pretty_name ?: $agent->hostname;
        @endphp

            <!-- Header -->
        <div class="flex items-center justify-between p-6 border-b border-zinc-200 dark:border-zinc-700 flex-shrink-0">
            <div class="flex items-center gap-4">
                <div class="flex flex-col">
                    <h2 class="text-2xl font-bold text-zinc-900 dark:text-white">
                        Příkazy: {{ $prettyName }}
                    </h2>
                    @if($agent->pretty_name)
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ $agent->hostname }}</p>
                    @endif
                </div>
            </div>

            <button wire:click="$parent.closeCommands" class="text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300 p-2 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Quick Actions -->
        <div class="px-6 py-4 bg-zinc-50 dark:bg-zinc-800/50 border-b border-zinc-200 dark:border-zinc-700 flex-shrink-0">
            <div class="flex flex-wrap gap-2">
                <span class="text-sm font-medium text-zinc-600 dark:text-zinc-400 self-center mr-2">Rychlé akce:</span>

                <button wire:click="quickCommand('shutdown')"
                        wire:confirm="Opravdu chcete restartovat systém?"
                        class="px-3 py-2 text-sm font-medium bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 rounded-lg hover:bg-red-200 dark:hover:bg-red-900/50 transition-colors">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Shutdown
                </button>

                <button wire:click="quickCommand('restart')"
                        wire:confirm="Opravdu chcete restartovat systém?"
                        class="px-3 py-2 text-sm font-medium bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300 rounded-lg hover:bg-yellow-200 dark:hover:bg-yellow-900/50 transition-colors">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Restart
                </button>

                <button wire:click="quickCommand('get_processes')"
                        class="px-3 py-2 text-sm font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded-lg hover:bg-blue-200 dark:hover:bg-blue-900/50 transition-colors">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    Procesy
                </button>

                <button wire:click="quickCommand('get_services')"
                        class="px-3 py-2 text-sm font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded-lg hover:bg-blue-200 dark:hover:bg-blue-900/50 transition-colors">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2"></path>
                    </svg>
                    Služby
                </button>

                <button wire:click="quickCommand('update')"
                        class="px-3 py-2 text-sm font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded-lg hover:bg-green-200 dark:hover:bg-green-900/50 transition-colors">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Update
                </button>

                <div class="flex-1"></div>

                <button wire:click="openCreateModal"
                        class="px-4 py-2 text-sm font-medium bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Nový příkaz
                </button>
            </div>
        </div>

        <!-- Filters -->
        <div class="px-6 py-3 bg-white dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700 flex-shrink-0">
            <div class="flex gap-3 items-center flex-wrap">
                <select wire:model.live="filterStatus"
                        class="px-3 py-2 text-sm border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Všechny stavy</option>
                    @foreach($statusOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>

                <select wire:model.live="filterType"
                        class="px-3 py-2 text-sm border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Všechny typy</option>
                    @foreach($commandTypes as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>

                @if($filterStatus || $filterType)
                    <button wire:click="clearFilters"
                            class="px-3 py-2 text-sm text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white">
                        Vymazat filtry
                    </button>
                @endif

                <div class="flex-1"></div>

                <span class="text-sm text-zinc-500 dark:text-zinc-400">
                    {{ $commands->count() }} příkazů
                </span>
            </div>
        </div>

        <!-- Commands List -->
        <div class="flex-1 overflow-y-auto p-6">
            <div class="space-y-3">
                @forelse($commands as $command)
                    @php
                        $statusColors = [
                            'pending' => 'bg-gray-100 dark:bg-gray-900/30 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-700',
                            'sent' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 border-blue-200 dark:border-blue-700',
                            'running' => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300 border-yellow-200 dark:border-yellow-700',
                            'completed' => 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 border-green-200 dark:border-green-700',
                            'failed' => 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 border-red-200 dark:border-red-700',
                            'cancelled' => 'bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300 border-orange-200 dark:border-orange-700',
                        ];
                        $colorClass = $statusColors[$command->status->value] ?? $statusColors['pending'];
                    @endphp

                    <div class="rounded-lg border {{ $colorClass }} p-4 transition-all hover:shadow-md"
                         x-data="{ expanded: false }">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-3 mb-2">
                                    <span class="inline-flex px-2 py-1 rounded text-xs font-semibold {{ $colorClass }}">
                                        {{ $command->status->label() }}
                                    </span>
                                    <span class="text-sm font-medium text-zinc-900 dark:text-white">
                                        {{ $command->type->label() }}
                                    </span>
                                    @if($command->status->value === 'running')
                                        <svg class="w-4 h-4 animate-spin text-yellow-600" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    @endif
                                </div>

                                @if($command->command)
                                    <div class="text-sm text-zinc-600 dark:text-zinc-400 font-mono truncate mb-1">
                                        @php $parsed = $command->parsed_output; @endphp

                                        @if($parsed?->isServices())
                                            <div class="text-xs text-zinc-500 mb-2">
                                                {{ $parsed->summary['running'] }} @lang('running') / {{ $parsed->summary['stopped'] }} @lang('stopped')
                                            </div>
                                            <div class="max-h-64 overflow-y-auto">
                                                <table class="min-w-full text-xs">
                                                    <thead class="bg-zinc-800 text-zinc-300 sticky top-0">
                                                    <tr>
                                                        <th class="px-2 py-1 text-left">Název</th>
                                                        <th class="px-2 py-1 text-left">Stav</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach($parsed->data as $service)
                                                        <tr class="border-t border-zinc-700">
                                                            <td class="px-2 py-1 font-mono" title="{{ $service->name }}">
                                                                {{ $service->displayName }}
                                                            </td>
                                                            <td class="px-2 py-1">
                                                                <span class="px-1.5 py-0.5 rounded text-xs {{ $service->isRunning ? 'bg-green-900/40 text-green-300' : 'bg-zinc-700 text-zinc-400' }}">
                                                                    {{ $service->status }}
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>

                                        @elseif($parsed?->isProcesses())
                                            <div class="text-xs text-zinc-500 mb-2">
                                                {{ $parsed->summary['total'] }} procesů, {{ $parsed->summary['totalMemoryMB'] }} MB celkem
                                            </div>
                                            <div class="max-h-64 overflow-y-auto">
                                                <table class="min-w-full text-xs">
                                                    <thead class="bg-zinc-800 text-zinc-300 sticky top-0">
                                                    <tr>
                                                        <th class="px-2 py-1 text-left">Proces</th>
                                                        <th class="px-2 py-1 text-right">PID</th>
                                                        <th class="px-2 py-1 text-right">RAM</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach($parsed->data->sortByDesc('memoryMB') as $process)
                                                        <tr class="border-t border-zinc-700">
                                                            <td class="px-2 py-1 font-mono">{{ $process->name }}</td>
                                                            <td class="px-2 py-1 text-right text-zinc-400">{{ $process->pid }}</td>
                                                            <td class="px-2 py-1 text-right">
                                                                <span class="{{ $process->memoryMB > 200 ? 'text-yellow-400' : 'text-zinc-300' }}">
                                                                    {{ $process->memoryMB }} MB
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>

                                        @elseif($parsed?->isRaw())
                                            <pre class="text-xs bg-zinc-900 text-green-400 p-2 rounded overflow-x-auto max-h-48">{{ $parsed->raw }}</pre>

                                        @elseif($command->output)
                                            <pre class="text-xs bg-zinc-900 text-green-400 p-2 rounded overflow-x-auto max-h-48">{{ $command->output }}</pre>
                                        @endif
                                    </div>
                                @endif

                                <div class="flex items-center gap-4 text-xs text-zinc-500 dark:text-zinc-400">
                                    <span>{{ $command->created_at->format('d.m.Y H:i:s') }}</span>
                                    @if($command->creator)
                                        <span>{{ $command->creator->name }}</span>
                                    @endif
                                    @if($command->exit_code !== null)
                                        <span class="{{ $command->exit_code === 0 ? 'text-green-600' : 'text-red-600' }}">
                                            Exit: {{ $command->exit_code }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                @if($command->output || $command->error)
                                    <button @click="expanded = !expanded"
                                            class="p-2 text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg transition-colors">
                                        <svg class="w-5 h-5 transition-transform" :class="expanded ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                @endif

                                @if($command->canBeCancelled())
                                    <button wire:click="cancelCommand({{ $command->id }})"
                                            wire:confirm="Opravdu chcete zrušit tento příkaz?"
                                            class="p-2 text-red-500 hover:text-red-700 hover:bg-red-100 dark:hover:bg-red-900/30 rounded-lg transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        </div>

                        <!-- Expanded Output -->
                        <div x-show="expanded" x-collapse class="mt-4 space-y-3">
                            @if($command->output)
                                <div>
                                    <div class="text-xs font-semibold text-zinc-600 dark:text-zinc-400 mb-1">Výstup:</div>
                                    <pre class="text-xs bg-zinc-900 text-green-400 p-3 rounded-lg overflow-x-auto max-h-64 overflow-y-auto font-mono">{{ $command->output }}</pre>
                                </div>
                            @endif

                            @if($command->error)
                                <div>
                                    <div class="text-xs font-semibold text-red-600 dark:text-red-400 mb-1">Chyba:</div>
                                    <pre class="text-xs bg-red-950 text-red-300 p-3 rounded-lg overflow-x-auto max-h-64 overflow-y-auto font-mono">{{ $command->error }}</pre>
                                </div>
                            @endif

                            @if($command->sent_at || $command->started_at || $command->completed_at)
                                <div class="flex gap-4 text-xs text-zinc-500 dark:text-zinc-400 pt-2 border-t border-zinc-200 dark:border-zinc-700">
                                    @if($command->sent_at)
                                        <span>Odesláno: {{ $command->sent_at->format('H:i:s') }}</span>
                                    @endif
                                    @if($command->started_at)
                                        <span>Spuštěno: {{ $command->started_at->format('H:i:s') }}</span>
                                    @endif
                                    @if($command->completed_at)
                                        <span>Dokončeno: {{ $command->completed_at->format('H:i:s') }}</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center py-12 text-center">
                        <svg class="w-16 h-16 text-zinc-300 dark:text-zinc-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <p class="text-lg font-semibold text-zinc-900 dark:text-white mb-1">Žádné příkazy</p>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">
                            Zatím nebyly odeslány žádné vzdálené příkazy
                        </p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Footer -->
        <div class="flex justify-between items-center gap-3 p-6 border-t border-zinc-200 dark:border-zinc-700 flex-shrink-0">
            <div class="text-sm text-zinc-500">
                Poslední aktualizace: <span class="font-mono">{{ now()->format('H:i:s') }}</span>
            </div>
            <button wire:click="$parent.closeCommands"
                    class="px-4 py-2 text-sm font-medium text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg transition-colors">
                Zavřít
            </button>
        </div>
    </div>

    <!-- Create Command Modal -->
    @if($showCreateModal)
        <div class="fixed inset-0 z-60 flex items-center justify-center p-4" @keydown.escape.window="$wire.closeCreateModal()">
            <div class="absolute inset-0 bg-black/50" wire:click="closeCreateModal"></div>
            <div class="relative bg-white dark:bg-zinc-900 rounded-lg shadow-xl w-full max-w-lg">
                <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                    <h3 class="text-lg font-bold text-zinc-900 dark:text-white">Nový příkaz</h3>
                </div>

                <form wire:submit="createCommand" class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                            Typ příkazu *
                        </label>
                        <select wire:model="commandType"
                                class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Vyberte typ...</option>
                            @foreach($commandTypes as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('commandType')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                            Příkaz / Parametry
                        </label>
                        <textarea wire:model="commandText"
                                  rows="3"
                                  class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono text-sm"
                                  placeholder="např. Get-Process | Select-Object -First 10"></textarea>
                        @error('commandText')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                            URL (pro download/update)
                        </label>
                        <input wire:model="commandUrl"
                               type="url"
                               class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="https://...">
                        @error('commandUrl')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button"
                                wire:click="closeCreateModal"
                                class="px-4 py-2 text-sm font-medium text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg transition-colors">
                            Zrušit
                        </button>
                        <button type="submit"
                                class="px-4 py-2 text-sm font-medium bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                            Vytvořit příkaz
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
