<!-- TABLE WRAPPER — scroll na mobilech -->
<div class="w-full overflow-x-auto rounded-lg border border-zinc-200 bg-white shadow-sm">
    <table class="w-full text-left text-sm">
        <thead class="bg-zinc-50 font-medium text-zinc-600">
            <tr>
                <th class="px-4 py-3">Web</th>
                <th class="px-4 py-3">Hosting</th>
                <th class="px-4 py-3 hidden md:table-cell">Dropshipping</th>
                <th class="px-4 py-3 hidden md:table-cell">SSL</th>
                <th class="px-4 py-3 hidden md:table-cell">Status</th>
                <th class="px-4 py-3 text-right">Akce</th>
            </tr>
        </thead>

        <tbody class="divide-y divide-zinc-100">
            @foreach ($this->myWebsitesFiltered as $website)
            <tr class="hover:bg-zinc-50">
                
                <!-- FIRST COLUMN -->
                <td class="px-4 py-4">
                    <div class="flex flex-col">
                        <span class="font-semibold text-zinc-800">{{ $website->domain->domain }}</span>
                        <span class="text-xs text-zinc-500 mt-1">{{ $website->domain->nameserver }}</span>
                    </div>
                </td>

                <!-- HOSTING -->
                <td class="px-4 py-4">
                    <span class="inline-flex items-center gap-1.5 rounded-md px-2 py-1 text-xs
                        {{ $website->hosting_plan ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                        {{ $website->hosting_plan ? 'Ano' : 'Ne' }}
                    </span>
                </td>

                <!-- DROPSHIPPING (hidden on mobile) -->
                <td class="px-4 py-4 hidden md:table-cell">
                    <span class="inline-flex items-center gap-1.5 rounded-md px-2 py-1 text-xs
                        {{ $website->is_dropshipping ? 'bg-blue-100 text-blue-700' : 'bg-zinc-100 text-zinc-600' }}">
                        {{ $website->is_dropshipping ? 'Aktivní' : 'Neaktivní' }}
                    </span>
                </td>

                <!-- SSL (hidden on mobile) -->
                <td class="px-4 py-4 hidden md:table-cell">
                    <span class="inline-flex items-center gap-1.5 rounded-md px-2 py-1 text-xs
                        {{ $website->ssl_status === 'valid' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                        {{ ucfirst($website->ssl_status) }}
                    </span>
                </td>

                <!-- STATUS (hidden on mobile) -->
                <td class="px-4 py-4 hidden md:table-cell">
                    @php
                        $statusClasses = [
                            'deployed' => 'bg-green-100 text-green-700',
                            'deploying' => 'bg-blue-100 text-blue-700 animate-pulse',
                            'queued' => 'bg-yellow-100 text-yellow-700',
                            'failed' => 'bg-red-100 text-red-700',
                        ];
                    @endphp

                    <span class="inline-flex items-center gap-1.5 rounded-md px-2 py-1 text-xs {{ $statusClasses[$website->status] ?? 'bg-zinc-100 text-zinc-600' }}">
                        {{ ucfirst($website->status) }}
                    </span>
                </td>

                <!-- ACTIONS — mobile-safe ✔️ -->
                <td class="px-4 py-4 text-right relative">
                    <div x-data="{ open: false }" class="inline-block text-left">

                        <!-- BUTTON -->
                        <button @click="open = !open"
                            class="rounded-md border border-zinc-300 bg-white px-3 py-2 text-sm font-medium text-zinc-800 shadow-sm
                                hover:bg-zinc-100 focus:outline-none focus:ring-2 focus:ring-zinc-400">
                            Akce
                        </button>

                        <!-- DROPDOWN ✔️ Z-index, responsive, scroll-safe -->
                        <div
                            x-show="open"
                            @click.away="open = false"
                            x-transition
                            class="absolute right-0 z-50 mt-2 w-40 rounded-md border border-zinc-200 bg-white shadow-lg">

                            <ul class="py-1 text-sm text-zinc-700">
                                <li>
                                    <a href="{{ route('websites.show', ['domain' => $website->domain->domain]) }}"
                                        class="block px-4 py-2 hover:bg-zinc-50">
                                        Zobrazit
                                    </a>
                                </li>
                                <li>
                                    <button wire:click="deleteWebsite({{ $website->id }})"
                                        class="block w-full px-4 py-2 text-left text-red-600 hover:bg-red-50">
                                        Smazat
                                    </button>
                                </li>
                            </ul>
                        </div>

                    </div>
                </td>

            </tr>
            @endforeach
        </tbody>
    </table>
</div>
