<div class="relative min-h-screen">

    {{-- NAVBAR (sticky při scrollu) --}}
    <nav
        x-data="{ scrolled: false, open: false }"
        x-init="window.addEventListener('scroll', () => scrolled = window.scrollY > 48)"
        :class="scrolled ? 'backdrop-blur-sm bg-white/70 border-b' : 'bg-transparent'"
        class="fixed w-full z-50 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex items-center justify-between h-16">
                <a href="#" class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-md bg-gradient-to-br from-sky-500 to-indigo-500 flex items-center justify-center text-white font-semibold shadow-sm">P</div>
                    <div class="text-gray-900 font-semibold">Parton <span class="text-sky-500">MS</span></div>
                </a>

                <div class="hidden md:flex items-center gap-8 text-sm">
                    <a href="#features" class="text-gray-700 hover:text-sky-600 transition">Vlastnosti</a>
                    <a href="#architecture" class="text-gray-700 hover:text-sky-600 transition">Architektura</a>
                    <a href="#demo" class="text-gray-700 hover:text-sky-600 transition">Demo</a>
                    <a href="#contact" class="px-4 py-2 bg-sky-600 text-white rounded-lg shadow hover:opacity-95">Přihlásit</a>
                </div>

                <div class="md:hidden">
                    <button @click="open = !open" class="p-2 rounded-md bg-white/90 shadow">
                        <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            <path x-show="open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Mobile menu --}}
        <div x-show="open" class="md:hidden bg-white/80 border-t">
            <div class="px-4 py-3 flex flex-col gap-2">
                <a href="#features" class="text-gray-700">Vlastnosti</a>
                <a href="#architecture" class="text-gray-700">Architektura</a>
                <a href="#demo" class="text-gray-700">Demo</a>
                <a href="#contact" class="text-gray-700">Přihlásit</a>
            </div>
        </div>
    </nav>

    {{-- HERO s Vanta background (animované pozadí) --}}
    <header id="hero" class="relative pt-24 pb-16">
        <div id="vanta-bg" class="absolute inset-0 -z-10"></div>

        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <div class="space-y-6">
                    <h1 class="text-4xl md:text-5xl font-extrabold leading-tight text-gray-900">
                        Moderní monitorování infrastruktury — <span class="text-sky-600">spolehlivě a škálovatelně</span>
                    </h1>

                    <p class="text-gray-600 max-w-xl">
                        Parton MS sbírá metriky přes lehké REST API agenty a prezentuje je v přehledném rozhraní.
                        Podpora téměř-realtime zobrazení přes WebSockety nebo optimalizovaný polling pro menší sítě.
                    </p>

                    <div class="flex flex-wrap gap-4">
                        <a href="#demo" class="px-5 py-3 bg-sky-600 text-white rounded-lg shadow hover:opacity-95">Vyzkoušet demo</a>
                        <a href="#features" class="px-5 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Zjistit více</a>
                    </div>

                    <div class="mt-4 flex gap-4 text-sm text-gray-500">
                        <div class="bg-white/80 px-3 py-1 rounded-full shadow">API: REST + WebSocket-ready</div>
                        <div class="bg-white/80 px-3 py-1 rounded-full shadow">Bezpečnost: TLS + token auth</div>
                        <div class="bg-white/80 px-3 py-1 rounded-full shadow">Customer zone & Admin</div>
                    </div>
                </div>

                {{-- Right side: card s preview --}}
                <div class="relative">
                    <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 w-full max-w-md mx-auto">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-sm text-gray-500">Aktivní zařízení</div>
                                <div class="text-2xl font-semibold text-gray-900">6</div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm text-gray-500">Uptime (avg)</div>
                                <div class="text-lg font-medium text-gray-900">712 h</div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <canvas id="demo-cpu-chart" width="400" height="180"></canvas>
                        </div>

                        <div class="mt-4 grid grid-cols-3 gap-3 text-sm text-gray-600">
                            <div class="text-center">
                                <div class="font-semibold text-gray-900">12%</div>
                                <div>Chyby</div>
                            </div>
                            <div class="text-center">
                                <div class="font-semibold text-gray-900">84%</div>
                                <div>Online</div>
                            </div>
                            <div class="text-center">
                                <div class="font-semibold text-gray-900">23 ms</div>
                                <div>Latence</div>
                            </div>
                        </div>
                    </div>

                    {{-- subtle glass reflection --}}
                    <div class="absolute -left-8 -top-8 w-36 h-36 bg-gradient-to-br from-white/30 to-transparent rounded-full blur-3xl pointer-events-none"></div>
                </div>
            </div>
        </div>
    </header>

    {{-- FEATURES --}}
    <section id="features" class="py-20">
        <div class="max-w-6xl mx-auto px-6 text-center">
            <h2 class="text-3xl font-bold text-gray-900 mb-6">Co Parton MS umí</h2>
            <p class="text-gray-600 max-w-2xl mx-auto mb-8">Přehledné metriky, bezpečná komunikace a škálovatelnost pro malé i velké sítě.</p>

            <div class="grid md:grid-cols-3 gap-6">
                <div class="p-6 bg-white rounded-2xl shadow border">
                    <h3 class="font-semibold text-gray-900 mb-2">Sbírání dat (REST API)</h3>
                    <p class="text-gray-600 text-sm">Lehký agent zasílá metriky: CPU, paměť, disk, síť a vlastní telemetrii.</p>
                </div>

                <div class="p-6 bg-white rounded-2xl shadow border">
                    <h3 class="font-semibold text-gray-900 mb-2">Téměř realtime</h3>
                    <p class="text-gray-600 text-sm">Server zpracuje data okamžitě a pushuje události přes WebSockety; pro demo je k dispozici polling.</p>
                </div>

                <div class="p-6 bg-white rounded-2xl shadow border">
                    <h3 class="font-semibold text-gray-900 mb-2">Bezpečnost</h3>
                    <p class="text-gray-600 text-sm">Zabezpečená komunikace přes TLS, API tokeny, role-based přístup a audit logy.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ARCHITECTURE --}}
    <section id="architecture" class="py-20 bg-gray-50 border-t">
        <div class="max-w-6xl mx-auto px-6 text-center">
            <h2 class="text-2xl font-semibold text-gray-900 mb-6">Architektura systému</h2>
            <p class="text-gray-600 max-w-2xl mx-auto mb-8">Modulární: Frontend (marketing + customer zone), Backend (admin), Agents (REST API).</p>

            <div class="mt-6">
                <img src="/images/architecture-light.svg" alt="Architektura" class="mx-auto w-full max-w-3xl">
            </div>
        </div>
    </section>

    {{-- DEMO: reálný vzhled rozhraní s aktualizacemi (wire:poll) --}}
    <section id="demo" class="py-20">
        <div class="max-w-6xl mx-auto px-6">
            <div class="flex items-start gap-8">
                {{-- Device list (Livewire renders and polls) --}}
                <div class="w-full lg:w-1/2">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Ukázková zařízení</h3>
                        <div class="text-sm text-gray-500">Aktualizace každých 5s (demo)</div>
                    </div>

                    <div wire:poll.5s class="space-y-3">
                        @livewire('frontend.landing-page-widget-devices', [], key('devices-list'))
                        {{-- fallback: if you prefer rendering here directly, iterate over $devices (but we are using simple separate partial below) --}}
                    </div>
                </div>

                {{-- Right side: detail card (chart updated via dispatchBrowserEvent) --}}
                <div class="w-full lg:w-1/2">
                    <div class="bg-white p-6 rounded-2xl shadow border">
                        <h4 class="font-semibold text-gray-900 mb-4">Zátěž CPU (posledních 6 zařízení)</h4>
                        <canvas id="live-cpu-chart" width="500" height="260"></canvas>
                        <p class="text-sm text-gray-500 mt-4">Data jsou v demo režimu simulována serverem.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Kontakt / CTA --}}
    <section id="contact" class="py-12 bg-gradient-to-r from-sky-50 to-white border-t">
        <div class="max-w-4xl mx-auto px-6 text-center">
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Chceš nasadit Parton MS ve firmě?</h3>
            <p class="text-gray-600 mb-6">Kontaktuj nás pro demo nasazení nebo technické konzultace.</p>
            <a href="#" class="inline-block px-6 py-3 bg-sky-600 text-white rounded-lg shadow">Kontaktovat pro prodej</a>
        </div>
    </section>

    {{-- FOOTER --}}
    <footer class="py-8 text-center text-sm text-gray-600">
        &copy; {{ date('Y') }} Parton Monitoring System — Všechna práva vyhrazena.
    </footer>
</div>
