<div class="bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-700 p-4"
     wire:poll.3s="checkResult">
    <h3 class="text-sm font-semibold text-zinc-900 dark:text-white mb-3 flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
        </svg>
        Rychlý příkaz
    </h3>

    <form wire:submit="executeCommand" class="space-y-3">
        <div class="flex gap-2">
            <select wire:model="commandType"
                    class="px-3 py-2 text-sm border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="powershell">PowerShell</option>
                <option value="cmd">CMD</option>
            </select>

            <input wire:model="command"
                   type="text"
                   class="flex-1 px-3 py-2 text-sm border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white font-mono focus:outline-none focus:ring-2 focus:ring-blue-500"
                   placeholder="Enter command..."
                @disabled($isRunning)>

            <button type="submit"
                    class="px-4 py-2 text-sm font-medium bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                @disabled($isRunning || empty($command))>
                @if($isRunning)
                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                @else
                    Spustit
                @endif
            </button>
        </div>
    </form>

    @if($lastOutput)
        <div class="mt-3">
            <div class="text-xs font-semibold text-zinc-600 dark:text-zinc-400 mb-1">Výstup:</div>
            <pre class="text-xs bg-zinc-950 text-green-400 p-3 rounded-lg overflow-x-auto max-h-48 overflow-y-auto font-mono">{{ $lastOutput }}</pre>
        </div>
    @endif

    @if($lastError)
        <div class="mt-3">
            <div class="text-xs font-semibold text-red-600 dark:text-red-400 mb-1">Chyba:</div>
            <pre class="text-xs bg-red-950 text-red-300 p-3 rounded-lg overflow-x-auto max-h-48 overflow-y-auto font-mono">{{ $lastError }}</pre>
        </div>
    @endif
</div>
