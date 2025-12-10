<x-layouts.app.sidebar :title="$title ?? null">
    <flux:main>
        {{ $slot }}
    </flux:main>

    <x-toast.group>
        <x-toast />
    </x-toast.group>

</x-layouts.app.sidebar>
