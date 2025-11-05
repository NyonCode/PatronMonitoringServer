<div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700 overflow-hidden shadow-sm">
    <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700 flex items-center justify-between">
        <h3 class="text-lg font-semibold text-zinc-800 dark:text-zinc-100">System Logs</h3>
        <span class="text-sm text-zinc-500">Last updated {{ now()->format('H:i:s') }}</span>
    </div>

    <div class="divide-y divide-zinc-100 dark:divide-zinc-800 max-h-[600px] overflow-y-auto font-mono text-sm leading-relaxed">
        @forelse ($logs as $log)
            @php
                $type = strtolower($log['EntryType'] ?? 'info');
                $colors = [
                    'info' => 'text-blue-600 dark:text-blue-400',
                    'warning' => 'text-amber-600 dark:text-amber-400',
                    'error' => 'text-red-600 dark:text-red-400',
                ];
                $color = $colors[$type] ?? 'text-zinc-400';
            @endphp

            <div class="flex items-start gap-3 px-6 py-3 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition">
                <div class="{{ $color }}">
                    @if ($type === 'error')
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.29 3.86L1.82 18a1 1 0 00.86 1.5h18.64a1 1 0 00.86-1.5L13.71 3.86a1 1 0 00-1.72 0zM12 9v4m0 4h.01" />
                        </svg>
                    @elseif ($type === 'warning')
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M4.93 19h14.14c1.54 0 2.5-1.68 1.73-3L13.73 4a2 2 0 00-3.46 0L3.2 16c-.77 1.32.19 3 1.73 3z" />
                        </svg>
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 4a8 8 0 100 16 8 8 0 000-16z" />
                        </svg>
                    @endif                </div>
                <div class="flex-1">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="font-semibold {{ $color }}">{{ strtoupper($log['EntryType'] ?? 'INFO') }}</span>
                            <span class="text-zinc-400 text-xs">{{ $log['Source'] ?? 'Unknown Source' }}</span>
                        </div>
                        <span class="text-zinc-500 text-xs font-medium">
                            {{ \Carbon\Carbon::parse($log['Time'])->format('Y-m-d H:i:s') }}
                        </span>
                    </div>
                    <div class="bg-zinc-900/40 border border-zinc-700 rounded-xl p-4">
                        <p class="font-semibold mb-2 text-zinc-100">{{ $log->EntryType }}</p>
                        <p class="text-zinc-300 mb-4">{{ $log->Time }}</p>
                        {!! $log->formatted_message !!}
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center text-zinc-500 py-6">Žádné systémové logy k zobrazení.</div>
        @endforelse
    </div>
</div>
