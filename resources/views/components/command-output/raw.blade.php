@props([
    'output',
    'maxHeight' => '64',
])

<pre class="text-xs bg-zinc-950 text-green-400 p-3 rounded-lg overflow-x-auto max-h-{{ $maxHeight }} overflow-y-auto font-mono whitespace-pre-wrap break-all">{{ $output }}</pre>
