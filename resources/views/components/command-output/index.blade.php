@props([
    'command',
    'serviceSearch' => '',
    'serviceStatusFilter' => '',
    'processSearch' => '',
    'processSortBy' => 'memory',
])

@php
    use App\Models\RemoteCommand;

    assert($command instanceof RemoteCommand);
    $parsed = $command->parsed_output;
@endphp

{{-- DEBUG: Remove after testing --}}
@if(config('app.debug'))
    <div class="text-xs text-yellow-500 mb-2 p-2 bg-yellow-900/20 rounded">
        DEBUG: type={{ $command->type->value }},
        has_output={{ $command->output ? 'yes' : 'no' }},
        parsed={{ $parsed ? 'yes' : 'no' }},
        parsed_type={{ $parsed?->type ?? 'null' }},
        is_services={{ $parsed?->isServices() ? 'yes' : 'no' }},
        is_processes={{ $parsed?->isProcesses() ? 'yes' : 'no' }},
        count={{ $parsed?->count() ?? 0 }}
    </div>
@endif

@if($parsed)
    @if($parsed->isServices())
        <x-command-output.services
            :parsed="$parsed"
            :search="$serviceSearch"
            :status-filter="$serviceStatusFilter"
        />

    @elseif($parsed->isProcesses())
        <x-command-output.processes
            :parsed="$parsed"
            :search="$processSearch"
            :sort-by="$processSortBy"
        />

    @elseif($parsed->isRaw())
        <x-command-output.raw :output="$parsed->raw"/>

    @elseif($parsed->isJson())
        {{-- Generic JSON - show formatted --}}
        <div class="space-y-2">
            <div class="text-xs text-zinc-500">
                JSON výstup ({{ $parsed->getSummary('count') }} položek)
            </div>
            <pre
                class="text-xs bg-zinc-950 text-blue-400 p-3 rounded-lg overflow-x-auto max-h-64 overflow-y-auto font-mono">{{ json_encode($parsed->data->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
        </div>
    @endif

@elseif($command->output)
    {{-- Fallback - raw output --}}
    <x-command-output.raw :output="$command->output"/>
@endif
