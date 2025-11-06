<div class="space-y-3">
    @forelse ($logs as $log)
        @php
            $type = strtolower($log['EntryType'] ?? 'info');
            $colors = [
                'info' => [
                    'bg' => 'bg-blue-50 dark:bg-blue-900/10',
                    'border' => 'border-blue-200 dark:border-blue-800',
                    'text' => 'text-blue-700 dark:text-blue-300',
                    'badge' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300'
                ],
                'warning' => [
                    'bg' => 'bg-yellow-50 dark:bg-yellow-900/10',
                    'border' => 'border-yellow-200 dark:border-yellow-800',
                    'text' => 'text-yellow-700 dark:text-yellow-300',
                    'badge' => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300'
                ],
                'error' => [
                    'bg' => 'bg-red-50 dark:bg-red-900/10',
                    'border' => 'border-red-200 dark:border-red-800',
                    'text' => 'text-red-700 dark:text-red-300',
                    'badge' => 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300'
                ],
            ];
            $color = $colors[$type] ?? $colors['info'];
        @endphp

        <div class="rounded-lg border {{ $color['border'] }} {{ $color['bg'] }} p-4 transition-all hover:shadow-md">
            <div class="flex items-start gap-3">
                <!-- Icon -->
                <div class="{{ $color['text'] }} flex-shrink-0">
                    @if ($type === 'error')
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    @elseif ($type === 'warning')
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    @endif
                </div>

                <!-- Content -->
                <div class="flex-1 min-w-0">
                    <!-- Header -->
                    <div class="flex items-start justify-between gap-2 mb-2">
                        <span class="inline-flex px-2 py-1 rounded text-xs font-semibold {{ $color['badge'] }}">
                            {{ strtoupper($log['EntryType'] ?? 'INFO') }}
                        </span>
                        <time class="text-xs text-zinc-500 dark:text-zinc-400 font-mono whitespace-nowrap">
                            {{ \Carbon\Carbon::parse($log['Time'])->format('Y-m-d H:i:s') }}
                        </time>
                    </div>

                    <!-- Message -->
                    <div class="text-sm text-zinc-700 dark:text-zinc-300 whitespace-pre-wrap break-words">
                        {{ $log['Message'] ?? 'No message' }}
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="flex flex-col items-center justify-center py-12 text-center">
            <svg class="w-16 h-16 text-zinc-300 dark:text-zinc-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <p class="text-lg font-semibold text-zinc-900 dark:text-white mb-1">Žádné agent logy</p>
            <p class="text-sm text-zinc-500 dark:text-zinc-400">
                @if(!empty($search) || !empty($filterType))
                    Zkuste upravit filtr nebo vyhledávání
                @else
                    Zatím nebyly zaznamenány žádné agent logy
                @endif
            </p>
        </div>
    @endforelse
</div>
