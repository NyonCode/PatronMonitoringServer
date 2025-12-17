@props([
    'parsed',
    'search' => '',
    'statusFilter' => '',
])

@php
    use App\Services\CommandOutput\ParsedOutput;
    /** @var ParsedOutput $parsed */

    $services = $parsed->data;

    // Apply search filter
    if (!empty($search)) {
        $searchLower = mb_strtolower($search);
        $services = $services->filter(fn($s) =>
            str_contains(mb_strtolower($s->name), $searchLower) ||
            str_contains(mb_strtolower($s->displayName), $searchLower)
        );
    }

    // Apply status filter
    if ($statusFilter === 'running') {
        $services = $services->where('isRunning', true);
    } elseif ($statusFilter === 'stopped') {
        $services = $services->where('isRunning', false);
    }

    // Sort: running first, then alphabetically
    $services = $services->sortBy([
        ['isRunning', 'desc'],
        ['displayName', 'asc'],
    ]);
@endphp

<div class="space-y-3">
    {{-- Summary --}}
    <div class="flex items-center gap-4 text-xs">
        <span class="px-2 py-1 rounded bg-zinc-800 text-zinc-300">
            Celkem: <strong>{{ $parsed->getSummary('total') }}</strong>
        </span>
        <span class="px-2 py-1 rounded bg-green-900/40 text-green-300">
            Běží: <strong>{{ $parsed->getSummary('running') }}</strong>
        </span>
        <span class="px-2 py-1 rounded bg-zinc-700 text-zinc-400">
            Zastaveno: <strong>{{ $parsed->getSummary('stopped') }}</strong>
        </span>
    </div>

    {{-- Filters --}}
    <div class="flex items-center gap-2">
        <input type="text"
               wire:model.live.debounce.300ms="serviceSearch"
               placeholder="Hledat službu..."
               class="flex-1 px-3 py-1.5 text-xs border border-zinc-600 rounded bg-zinc-800 text-white placeholder-zinc-500 focus:outline-none focus:ring-1 focus:ring-blue-500">

        <select wire:model.live="serviceStatusFilter"
                class="px-2 py-1.5 text-xs border border-zinc-600 rounded bg-zinc-800 text-white focus:outline-none focus:ring-1 focus:ring-blue-500">
            <option value="">Všechny</option>
            <option value="running">Běžící</option>
            <option value="stopped">Zastavené</option>
        </select>

        @if(!empty($search) || !empty($statusFilter))
            <button wire:click="clearServiceFilters"
                    class="px-2 py-1.5 text-xs text-zinc-400 hover:text-white">
                ✕
            </button>
        @endif
    </div>

    {{-- Results info --}}
    @if($services->count() !== $parsed->data->count())
        <div class="text-xs text-zinc-500">
            Zobrazeno {{ $services->count() }} z {{ $parsed->data->count() }} služeb
        </div>
    @endif

    {{-- Table --}}
    <div class="max-h-80 overflow-y-auto rounded border border-zinc-700">
        <table class="min-w-full text-xs">
            <thead class="bg-zinc-800 text-zinc-300 sticky top-0">
            <tr>
                <th class="px-3 py-2 text-left font-medium">Služba</th>
                <th class="px-3 py-2 text-left font-medium w-24">Stav</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-zinc-700">
            @forelse($services as $service)
                <tr class="hover:bg-zinc-800/50 transition-colors">
                    <td class="px-3 py-2">
                        <div class="font-medium text-zinc-200">{{ $service->displayName }}</div>
                        <div class="text-zinc-500 font-mono text-[10px]">{{ $service->name }}</div>
                    </td>
                    <td class="px-3 py-2">
                        @if($service->isRunning)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium bg-green-900/40 text-green-300">
                                    <span class="w-1.5 h-1.5 bg-green-400 rounded-full"></span>
                                    Running
                                </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium bg-zinc-700 text-zinc-400">
                                    <span class="w-1.5 h-1.5 bg-zinc-500 rounded-full"></span>
                                    Stopped
                                </span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" class="px-3 py-8 text-center text-zinc-500">
                        Žádné služby neodpovídají filtru
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
