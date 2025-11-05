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
                $icons = [
                    'info' => 'i-lucide-info',
                    'warning' => 'i-lucide-alert-triangle',
                    'error' => 'i-lucide-x-octagon',
                ];
                $color = $colors[$type] ?? 'text-zinc-400';
                $icon = $icons[$type] ?? 'i-lucide-info';
            @endphp

            <div class="flex items-start gap-3 px-6 py-3 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition">
                <div class="{{ $color }}">
                    <x-dynamic-component :component="$icon" class="w-5 h-5 mt-0.5" />
                </div>
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
                    <p class="text-zinc-700 dark:text-zinc-300 mt-1 text-sm whitespace-pre-line">
                        {{ $log['Message'] ?? '' }}
                    </p>
                </div>
            </div>
        @empty
            <div class="text-center text-zinc-500 py-6">Žádné systémové logy k zobrazení.</div>
        @endforelse
    </div>
</div>
