@props([
    'parsed',
    'search' => '',
    'sortBy' => 'memory',
])

@php
    use App\Services\CommandOutput\ParsedOutput;
    /** @var ParsedOutput $parsed */

    $processes = $parsed->data;

    // Apply search filter
    if (!empty($search)) {
        $searchLower = mb_strtolower($search);
        $processes = $processes->filter(fn($p) =>
            str_contains(mb_strtolower($p->name), $searchLower)
        );
    }

    // Apply sort
    $processes = match($sortBy) {
        'name' => $processes->sortBy('name'),
        'pid' => $processes->sortBy('pid'),
        default => $processes->sortByDesc('memoryMB'),
    };
@endphp

<div class="space-y-3">
    {{-- Summary --}}
    <div class="flex items-center gap-4 text-xs">
        <span class="px-2 py-1 rounded bg-zinc-800 text-zinc-300">
            Procesů: <strong>{{ $parsed->getSummary('total') }}</strong>
        </span>
        <span class="px-2 py-1 rounded bg-blue-900/40 text-blue-300">
            RAM: <strong>{{ $parsed->getSummary('totalMemoryFormatted') }}</strong>
        </span>
        @if($parsed->getSummary('topConsumer'))
            <span class="px-2 py-1 rounded bg-yellow-900/40 text-yellow-300">
                Top: <strong>{{ $parsed->getSummary('topConsumer') }}</strong>
                ({{ $parsed->getSummary('topConsumerMemory') }} MB)
            </span>
        @endif
    </div>

    {{-- Filters --}}
    <div class="flex items-center gap-2">
        <input type="text"
               wire:model.live.debounce.300ms="processSearch"
               placeholder="Hledat proces..."
               class="flex-1 px-3 py-1.5 text-xs border border-zinc-600 rounded bg-zinc-800 text-white placeholder-zinc-500 focus:outline-none focus:ring-1 focus:ring-blue-500">

        <select wire:model.live="processSortBy"
                class="px-2 py-1.5 text-xs border border-zinc-600 rounded bg-zinc-800 text-white focus:outline-none focus:ring-1 focus:ring-blue-500">
            <option value="memory">Podle RAM ↓</option>
            <option value="name">Podle názvu</option>
            <option value="pid">Podle PID</option>
        </select>

        @if(!empty($search) || $sortBy !== 'memory')
            <button wire:click="clearProcessFilters"
                    class="px-2 py-1.5 text-xs text-zinc-400 hover:text-white">
                ✕
            </button>
        @endif
    </div>

    {{-- Results info --}}
    @if($processes->count() !== $parsed->data->count())
        <div class="text-xs text-zinc-500">
            Zobrazeno {{ $processes->count() }} z {{ $parsed->data->count() }} procesů
        </div>
    @endif

    {{-- Table --}}
    <div class="max-h-80 overflow-y-auto rounded border border-zinc-700">
        <table class="min-w-full text-xs">
            <thead class="bg-zinc-800 text-zinc-300 sticky top-0">
            <tr>
                <th class="px-3 py-2 text-left font-medium">Proces</th>
                <th class="px-3 py-2 text-right font-medium w-20">PID</th>
                <th class="px-3 py-2 text-right font-medium w-24">RAM</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-zinc-700">
            @forelse($processes as $process)
                @php
                    $memoryClass = match(true) {
                        $process->memoryMB >= 500 => 'text-red-400',
                        $process->memoryMB >= 200 => 'text-yellow-400',
                        $process->memoryMB >= 100 => 'text-blue-400',
                        default => 'text-zinc-300',
                    };
                @endphp
                <tr class="hover:bg-zinc-800/50 transition-colors">
                    <td class="px-3 py-2">
                        <span class="font-mono text-zinc-200">{{ $process->name }}</span>
                    </td>
                    <td class="px-3 py-2 text-right text-zinc-500 font-mono">
                        {{ $process->pid }}
                    </td>
                    <td class="px-3 py-2 text-right font-medium {{ $memoryClass }}">
                        {{ $process->memoryMB }} MB
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="px-3 py-8 text-center text-zinc-500">
                        Žádné procesy neodpovídají filtru
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{-- Memory bar visualization --}}
    @if($processes->isNotEmpty())
        <div class="pt-2">
            <div class="text-xs text-zinc-500 mb-1">Top 5 podle paměti:</div>
            <div class="space-y-1">
                @foreach($parsed->data->sortByDesc('memoryMB')->take(5) as $process)
                    @php
                        $maxMemory = $parsed->data->max('memoryMB');
                        $percentage = $maxMemory > 0 ? ($process->memoryMB / $maxMemory) * 100 : 0;
                        $barColor = match(true) {
                            $process->memoryMB >= 500 => 'bg-red-500',
                            $process->memoryMB >= 200 => 'bg-yellow-500',
                            $process->memoryMB >= 100 => 'bg-blue-500',
                            default => 'bg-zinc-500',
                        };
                    @endphp
                    <div class="flex items-center gap-2 text-xs">
                        <span class="w-32 truncate text-zinc-400">{{ $process->name }}</span>
                        <div class="flex-1 bg-zinc-800 rounded-full h-2 overflow-hidden">
                            <div class="{{ $barColor }} h-2 rounded-full transition-all"
                                 style="width: {{ $percentage }}%"></div>
                        </div>
                        <span class="w-16 text-right text-zinc-500">{{ $process->memoryMB }} MB</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
