<div class="fixed inset-0 z-50 overflow-hidden bg-black/50 flex items-center justify-center p-4"
     wire:poll.2s="requestOutput"
     x-data="{
         scrollToBottom() {
             this.$nextTick(() => {
                 const terminal = document.getElementById('terminal-output');
                 if (terminal) terminal.scrollTop = terminal.scrollHeight;
             });
         }
     }"
     x-init="scrollToBottom(); Livewire.on('input-sent', () => scrollToBottom()); Livewire.on('terminal-changed', () => scrollToBottom());">

    <div class="bg-zinc-900 rounded-lg shadow-xl w-full max-w-6xl h-[85vh] flex flex-col">
        @php
            $prettyName = $agent->pretty_name ?: $agent->hostname;
        @endphp

            <!-- Header -->
        <div class="flex items-center justify-between px-4 py-3 bg-zinc-800 border-b border-zinc-700 rounded-t-lg flex-shrink-0">
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2">
                    <div class="flex gap-1.5">
                        <div class="w-3 h-3 rounded-full bg-red-500"></div>
                        <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                        <div class="w-3 h-3 rounded-full bg-green-500"></div>
                    </div>
                </div>
                <div>
                    <span class="text-white font-medium">Terminal: {{ $prettyName }}</span>
                    @if($activeSession)
                        <span class="text-zinc-400 text-sm ml-2">
                            ({{ $activeSession->type->label() }}@if($activeSession->user_session_id) - User Session {{ $activeSession->user_session_id }}@else - SYSTEM @endif)
                        </span>
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button wire:click="openCreateModal"
                        class="px-3 py-1.5 text-sm font-medium bg-green-600 text-white rounded hover:bg-green-700 transition-colors">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Nový terminál
                </button>

                <button wire:click="$parent.closeTerminal"
                        class="p-2 text-zinc-400 hover:text-white hover:bg-zinc-700 rounded transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <div class="flex flex-1 overflow-hidden">
            <!-- Sessions Sidebar -->
            <div class="w-56 bg-zinc-800 border-r border-zinc-700 flex flex-col flex-shrink-0">
                <div class="p-3 border-b border-zinc-700">
                    <h3 class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">Sessions</h3>
                </div>
                <div class="flex-1 overflow-y-auto">
                    @forelse($sessions as $session)
                        <button wire:click="selectSession('{{ $session->id }}')"
                                class="w-full px-3 py-2 text-left transition-colors
                                       {{ $activeSessionId === $session->id ? 'bg-zinc-700 text-white' : 'text-zinc-300 hover:bg-zinc-700/50' }}">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2 min-w-0">
                                    <div class="w-2 h-2 rounded-full flex-shrink-0
                                                {{ $session->status->value === 'running' ? 'bg-green-500' : 'bg-zinc-500' }}"></div>
                                    <span class="text-sm truncate">{{ $session->type->value }}</span>
                                </div>
                                @if($session->isActive())
                                    <button wire:click.stop="closeSession('{{ $session->id }}')"
                                            class="p-1 text-zinc-400 hover:text-red-400 rounded">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                @endif
                            </div>
                            <div class="text-xs text-zinc-500 mt-0.5">
                                {{ $session->created_at->format('H:i:s') }}
                                @if(!$session->isActive())
                                    <span class="text-red-400">(closed)</span>
                                @endif
                            </div>
                        </button>
                    @empty
                        <div class="p-4 text-center text-zinc-500 text-sm">
                            Žádné terminály
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Terminal Area -->
            <div class="flex-1 flex flex-col min-w-0">
                @if($activeSession)
                    <!-- Terminal Output -->
                    <div id="terminal-output"
                         class="flex-1 overflow-y-auto p-4 font-mono text-sm bg-zinc-950 text-green-400"
                         x-effect="scrollToBottom()">
                        @if($terminalLogs->isEmpty())
                            <div class="text-zinc-500">
                                <p>Session started at {{ $activeSession->started_at?->format('Y-m-d H:i:s') }}</p>
                                <p>Type: {{ $activeSession->type->label() }}</p>
                                <p>Context: {{ $activeSession->user_session_id ? 'User Session ' . $activeSession->user_session_id : 'SYSTEM' }}</p>
                                <p class="mt-2">Waiting for output...</p>
                            </div>
                        @else
                            @foreach($terminalLogs as $log)
                                <div class="mb-1 {{ $log->direction->value === 'input' ? 'text-cyan-400' : 'text-green-400' }}">
                                    @if($log->direction->value === 'input')
                                        <span class="text-yellow-400">PS&gt;</span>
                                    @endif
                                    <span class="whitespace-pre-wrap break-all">{{ $log->content }}</span>
                                </div>
                            @endforeach
                        @endif

                        @if($activeSession->isActive())
                            <div class="inline-block w-2 h-4 bg-green-400 animate-pulse"></div>
                        @else
                            <div class="mt-4 text-red-400">
                                [Session closed at {{ $activeSession->closed_at?->format('H:i:s') }}]
                            </div>
                        @endif
                    </div>

                    <!-- Input Area -->
                    @if($activeSession->isActive())
                        <div class="border-t border-zinc-700 bg-zinc-900 p-3">
                            <form wire:submit="sendInput" class="flex gap-2">
                                <div class="flex-1 flex items-center bg-zinc-950 rounded border border-zinc-700 focus-within:border-green-500">
                                    <span class="text-yellow-400 px-3 font-mono text-sm">PS&gt;</span>
                                    <input wire:model="terminalInput"
                                           type="text"
                                           class="flex-1 bg-transparent text-green-400 font-mono text-sm py-2 pr-3 focus:outline-none"
                                           placeholder="Enter command..."
                                           autocomplete="off"
                                           autofocus>
                                </div>
                                <button type="submit"
                                        class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition-colors text-sm font-medium">
                                    Send
                                </button>
                                <button type="button"
                                        wire:click="sendCtrlC"
                                        class="px-3 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition-colors text-sm font-medium"
                                        title="Send Ctrl+C">
                                    ^C
                                </button>
                            </form>
                            <div class="flex gap-4 mt-2 text-xs text-zinc-500">
                                <span>Enter: Send command</span>
                                <span>^C: Interrupt</span>
                            </div>
                        </div>
                    @endif
                @else
                    <!-- No Session Selected -->
                    <div class="flex-1 flex items-center justify-center bg-zinc-950">
                        <div class="text-center">
                            <svg class="w-16 h-16 text-zinc-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <h3 class="text-lg font-medium text-zinc-300 mb-2">Žádný terminál</h3>
                            <p class="text-zinc-500 mb-4">Vyberte existující session nebo vytvořte nový terminál</p>
                            <button wire:click="openCreateModal"
                                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Nový terminál
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Create Terminal Modal -->
    @if($showCreateModal)
        <div class="fixed inset-0 z-60 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/50" wire:click="closeCreateModal"></div>
            <div class="relative bg-zinc-900 rounded-lg shadow-xl w-full max-w-md border border-zinc-700">
                <div class="p-6 border-b border-zinc-700">
                    <h3 class="text-lg font-bold text-white">Nový terminál</h3>
                </div>

                <form wire:submit="createSession" class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-2">
                            Typ terminálu
                        </label>
                        <select wire:model="terminalType"
                                class="w-full px-3 py-2 border border-zinc-600 rounded-lg bg-zinc-800 text-white focus:outline-none focus:ring-2 focus:ring-green-500">
                            @foreach($terminalTypes as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-2">
                            Kontext
                        </label>
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" wire:model="userSessionId" value=""
                                       class="text-green-500 focus:ring-green-500 bg-zinc-800 border-zinc-600">
                                <span class="text-zinc-300">SYSTEM (služba)</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" wire:model="userSessionId" value="1"
                                       class="text-green-500 focus:ring-green-500 bg-zinc-800 border-zinc-600">
                                <span class="text-zinc-300">User Session 1</span>
                            </label>
                        </div>
                        <p class="mt-2 text-xs text-zinc-500">
                            SYSTEM kontext běží jako služba, User Session běží v kontextu přihlášeného uživatele.
                        </p>
                    </div>

                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button"
                                wire:click="closeCreateModal"
                                class="px-4 py-2 text-sm font-medium text-zinc-300 hover:bg-zinc-800 rounded-lg transition-colors">
                            Zrušit
                        </button>
                        <button type="submit"
                                class="px-4 py-2 text-sm font-medium bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                            Vytvořit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
