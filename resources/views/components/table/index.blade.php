<div class="space-y-4">

    <!-- Toolbar -->
    <div class="flex flex-wrap items-center justify-between gap-3">
        <!-- Global Search -->
        <input
            type="text"
            wire:model.debounce.500ms="globalSearch"
            placeholder="Hledat…"
            class="border rounded px-3 py-2 w-64"
        >

        <!-- PerPage select -->
        <select wire:model.live="perPage" class="border rounded px-3 py-2">
            @foreach($perPageOptions as $option)
                <option value="{{ $option }}">{{ $option }} / stránka</option>
            @endforeach
        </select>

        <!-- Columns visibility -->
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" class="border rounded px-3 py-2">Sloupce ▼</button>
            <div
                x-show="open"
                @click.away="open = false"
                class="absolute right-0 mt-2 w-56 bg-white border rounded shadow z-20 p-2 space-y-1"
            >
                @foreach($this->columns() as $key => $col)
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" wire:click="toggleColumn('{{ $key }}')" @checked($columnsVisible[$key])>
                        <span>{{ $col['label'] }}</span>
                    </label>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Table -->
    <table class="w-full border-collapse border text-sm">
        <thead>
        <tr class="bg-gray-100">
            @foreach($columns() as $key => $col)
                @if($columnsVisible[$key])
                    <th class="border p-2 text-left cursor-pointer @if($col['sortable'] ?? false) hover:bg-gray-200 @endif"
                        @if($col['sortable'] ?? false)
                            wire:click="sort('{{ $key }}')"
                        @endif
                    >
                        {{ $col['label'] }}

                        @if(($col['sortable'] ?? false) && $sortBy === $key)
                            <span class="ml-1">
                                    @if($sortDirection === 'asc') ▲ @else ▼ @endif
                                </span>
                        @endif
                    </th>
                @endif
            @endforeach
        </tr>

        <!-- Column Filters -->
        <tr class="bg-gray-50">
            @foreach($columns() as $key => $col)
                @if($columnsVisible[$key])
                    <th class="border p-1">
                        @if($col['filterable'] ?? false)
                            <input
                                type="text"
                                wire:model.debounce.500ms="filters.{{ $key }}"
                                class="w-full border rounded px-1 py-1 text-xs"
                                placeholder="Filtrovat…"
                            >
                        @endif
                    </th>
                @endif
            @endforeach
        </tr>
        </thead>

        <tbody>
        @forelse($this->getRows() as $row)
            <tr>
                @foreach($this->columns() as $key => $col)
                    @if($columnsVisible[$key])
                        <td class="border p-2">
                            {{ data_get($row, $key) }}
                        </td>
                    @endif
                @endforeach
            </tr>
        @empty
            <tr>
                <td colspan="{{ count(array_filter($columnsVisible)) }}" class="border p-2 text-center">Žádné záznamy.</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <!-- Pagination -->
    <div>
        {{ $this->getRows()->links() }}
    </div>
</div>
