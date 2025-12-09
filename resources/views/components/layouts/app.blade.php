<x-layouts.app.sidebar :title="$title ?? null">
    <flux:main class="p-6 lg:p-4">
        {{ $slot }}
    </flux:main>
</x-layouts.app.sidebar>
