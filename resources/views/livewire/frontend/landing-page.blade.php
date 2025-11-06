<div class="relative min-h-screen bg-white" wire:poll.5s>

    {{-- NAVBAR (sticky při scrollu) --}}
    <nav
        x-data="{ scrolled: false, open: false }"
        x-init="window.addEventListener('scroll', () => scrolled = window.scrollY > 48)"
        :class="scrolled ? 'backdrop-blur-md bg-white/90 border-b shadow-sm' : 'bg-white'"
        class="fixed w-full z-50 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex items-center justify-between h-16">
                <a href="#" class="flex items-center gap-3 group">
                    <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-sky-500 to-indigo-600 flex items-center justify-center text-white font-bold shadow-lg group-hover:shadow-xl transition-shadow">
                        P
                    </div>
                    <div class="text-gray-900 font-bold text-lg">
                        Parton <span class="text-sky-600">MS</span>
                    </div>
                </a>

                <div class="hidden md:flex items-center gap-6">
                    <a href="#features" class="text-gray-600 hover:text-sky-600 transition-colors font-medium text-sm">Vlastnosti</a>
                    <a href="#architecture" class="text-gray-600 hover:text-sky-600 transition-colors font-medium text-sm">Architektura</a>
                    <a href="#demo" class="text-gray-600 hover:text-sky-600 transition-colors font-medium text-sm">Demo</a>
                    <a href="{{ route('login') }}" wire:navigate class="px-5 py-2.5 bg-gradient-to-r from-sky-600 to-indigo-600 text-white rounded-lg shadow-lg hover:shadow-xl hover:from-sky-700 hover:to-indigo-700 transition-all font-medium text-sm">
                        Přihlásit se
                    </a>
                </div>

                <button @click="open = !open" class="md:hidden p-2 rounded-lg hover:bg-gray-100 transition-colors">
                    <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        <path x-show="open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Mobile menu --}}
        <div x-show="open" x-transition class="md:hidden bg-white border-t shadow-lg">
            <div class="px-4 py-4 flex flex-col gap-3">
                <a href="#features" class="text-gray-700 hover:text-sky-600 px-3 py-2 rounded-lg hover:bg-gray-50 transition-colors">Vlastnosti</a>
                <a href="#architecture" class="text-gray-700 hover:text-sky-600 px-3 py-2 rounded-lg hover:bg-gray-50 transition-colors">Architektura</a>
                <a href="#demo" class="text-gray-700 hover:text-sky-600 px-3 py-2 rounded-lg hover:bg-gray-50 transition-colors">Demo</a>
                <a href="{{ route('login') }}" wire:navigate class="text-center px-3 py-2 bg-gradient-to-r from-sky-600 to-indigo-600 text-white rounded-lg shadow-lg">Přihlásit se</a>
            </div>
        </div>
    </nav>

    {{-- HERO s animovaným pozadím --}}
    <header id="hero" class="relative pt-32 pb-20 overflow-hidden">
        <!-- Animated background -->
        <div class="absolute inset-0 -z-10">
            <div class="absolute inset-0 bg-gradient-to-br from-sky-50 via-white to-indigo-50"></div>
            <div id="vanta-bg" class="absolute inset-0"></div>
        </div>

        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="space-y-8">
                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-sky-100 text-sky-700 rounded-full text-sm font-medium">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"></path>
                        </svg>
                        Moderní monitoring infrastruktury
                    </div>

                    <h1 class="text-5xl lg:text-6xl font-extrabold leading-tight text-gray-900">
                        Sledujte vaši <span class="text-transparent bg-clip-text bg-gradient-to-r from-sky-600 to-indigo-600">infrastrukturu</span> v reálném čase
                    </h1>

                    <p class="text-xl text-gray-600 leading-relaxed">
                        Parton MS je profesionální monitorovací systém s REST API, real-time aktualizacemi a přehledným dashboardem. Ideální pro malé i velké infrastruktury.
                    </p>

                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="#demo" class="group inline-flex items-center justify-center gap-2 px-8 py-4 bg-gradient-to-r from-sky-600 to-indigo-600 text-white rounded-xl shadow-lg hover:shadow-2xl hover:from-sky-700 hover:to-indigo-700 transition-all font-semibold text-lg">
                            Vyzkoušet demo
                            <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                            </svg>
                        </a>
                        <a href="#features" class="inline-flex items-center justify-center gap-2 px-8 py-4 border-2 border-gray-300 rounded-xl text-gray-700 hover:border-sky-600 hover:text-sky-600 transition-all font-semibold text-lg">
                            Zjistit více
                        </a>
                    </div>

                    <div class="flex flex-wrap gap-3 pt-4">
                        <div class="inline-flex items-center gap-2 px-4 py-2 bg-white rounded-lg shadow-md border border-gray-100">
                            <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-sm font-medium text-gray-700">REST API</span>
                        </div>
                        <div class="inline-flex items-center gap-2 px-4 py-2 bg-white rounded-lg shadow-md border border-gray-100">
                            <svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-sm font-medium text-gray-700">Real-time</span>
                        </div>
                        <div class="inline-flex items-center gap-2 px-4 py-2 bg-white rounded-lg shadow-md border border-gray-100">
                            <svg class="w-5 h-5 text-purple-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-sm font-medium text-gray-700">Zabezpečené</span>
                        </div>
                    </div>
                </div>

                {{-- Dashboard preview card --}}
                <div class="relative">
                    <div class="bg-white rounded-2xl shadow-2xl p-8 border border-gray-100">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <div class="text-sm font-medium text-gray-500 mb-1">Aktivní zařízení</div>
                                <div class="text-4xl font-bold text-gray-900">{{ count($devices) }}</div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-medium text-gray-500 mb-1">Online</div>
                                <div class="text-2xl font-bold text-green-600">
                                    {{ collect($devices)->where('status', '!=', 'offline')->count() }}
                                </div>
                            </div>
                        </div>

                        <div class="mb-6">
                            <canvas id="demo-cpu-chart" height="140"></canvas>
                        </div>

                        <div class="grid grid-cols-3 gap-4">
                            @php
                                $avgCpu = collect($devices)->avg('cpu');
                                $avgMem = collect($devices)->avg('mem');
                                $criticalCount = collect($devices)->where('status', 'critical')->count();
                            @endphp
                            <div class="text-center p-3 bg-gray-50 rounded-lg">
                                <div class="text-2xl font-bold text-gray-900">{{ round($avgCpu) }}%</div>
                                <div class="text-xs text-gray-500 mt-1">Prům. CPU</div>
                            </div>
                            <div class="text-center p-3 bg-gray-50 rounded-lg">
                                <div class="text-2xl font-bold text-gray-900">{{ round($avgMem) }}%</div>
                                <div class="text-xs text-gray-500 mt-1">Prům. RAM</div>
                            </div>
                            <div class="text-center p-3 bg-gray-50 rounded-lg">
                                <div class="text-2xl font-bold {{ $criticalCount > 0 ? 'text-red-600' : 'text-green-600' }}">
                                    {{ $criticalCount }}
                                </div>
                                <div class="text-xs text-gray-500 mt-1">Kritické</div>
                            </div>
                        </div>
                    </div>

                    {{-- Decorative elements --}}
                    <div class="absolute -left-12 -top-12 w-48 h-48 bg-sky-200/30 rounded-full blur-3xl pointer-events-none"></div>
                    <div class="absolute -right-12 -bottom-12 w-48 h-48 bg-indigo-200/30 rounded-full blur-3xl pointer-events-none"></div>
                </div>
            </div>
        </div>
    </header>

    {{-- FEATURES --}}
    <section id="features" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Vše, co potřebujete pro monitoring</h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Komplexní řešení pro sledování výkonu, logování a správu vaší IT infrastruktury
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <div class="group p-8 bg-white rounded-2xl border-2 border-gray-100 hover:border-sky-500 hover:shadow-xl transition-all">
                    <div class="w-14 h-14 bg-gradient-to-br from-sky-500 to-indigo-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Real-time monitoring</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Sledujte CPU, RAM, GPU a diskový prostor v reálném čase s minimální latencí.
                    </p>
                </div>

                <div class="group p-8 bg-white rounded-2xl border-2 border-gray-100 hover:border-sky-500 hover:shadow-xl transition-all">
                    <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Zabezpečená komunikace</h3>
                    <p class="text-gray-600 leading-relaxed">
                        TLS šifrování, API tokeny a role-based přístup zajišťují bezpečnost dat.
                    </p>
                </div>

                <div class="group p-8 bg-white rounded-2xl border-2 border-gray-100 hover:border-sky-500 hover:shadow-xl transition-all">
                    <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Flexibilní API</h3>
                    <p class="text-gray-600 leading-relaxed">
                        REST API s kompletní dokumentací pro snadnou integraci s vašimi systémy.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- DEMO SECTION --}}
    <section id="demo" class="py-24 bg-gradient-to-br from-gray-50 to-sky-50">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Živé demo</h2>
                <p class="text-xl text-gray-600">Sledujte simulovaná data z monitorovacího systému</p>
            </div>

            <div class="grid lg:grid-cols-2 gap-8">
                {{-- Device list --}}
                <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-2xl font-bold text-gray-900">Monitorovaná zařízení</h3>
                        <div class="flex items-center gap-2 text-sm text-gray-500">
                            <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                            Aktualizace každých 5s
                        </div>
                    </div>

                    <div class="space-y-3">
                        @foreach($devices as $device)
                            <div class="p-4 bg-gray-50 rounded-xl border border-gray-200 hover:border-sky-500 hover:shadow-md transition-all">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-3">
                                        <div class="w-3 h-3 rounded-full {{ $device['status'] === 'critical' ? 'bg-red-500' : ($device['status'] === 'warning' ? 'bg-yellow-500' : 'bg-green-500') }}"></div>
                                        <span class="font-semibold text-gray-900">{{ $device['name'] }}</span>
                                    </div>
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $device['status'] === 'critical' ? 'bg-red-100 text-red-700' : ($device['status'] === 'warning' ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700') }}">
                                        {{ ucfirst($device['status']) }}
                                    </span>
                                </div>
                                <div class="grid grid-cols-3 gap-3 text-sm">
                                    <div>
                                        <div class="text-gray-500 text-xs mb-1">CPU</div>
                                        <div class="font-bold text-gray-900">{{ $device['cpu'] }}%</div>
                                    </div>
                                    <div>
                                        <div class="text-gray-500 text-xs mb-1">RAM</div>
                                        <div class="font-bold text-gray-900">{{ $device['mem'] }}%</div>
                                    </div>
                                    <div>
                                        <div class="text-gray-500 text-xs mb-1">Disk</div>
                                        <div class="font-bold text-gray-900">{{ $device['disk'] }}%</div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Live chart --}}
                <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
                    <h3 class="text-2xl font-bold text-gray-900 mb-6">Zatížení CPU (live)</h3>
                    <canvas id="live-cpu-chart" height="400"></canvas>
                    <p class="text-sm text-gray-500 mt-4 text-center">
                        Data jsou v demo režimu simulována pro ukázku funkcionality
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA Section --}}
    <section class="py-20 bg-gradient-to-r from-sky-600 to-indigo-600">
        <div class="max-w-4xl mx-auto px-6 text-center text-white">
            <h2 class="text-4xl font-bold mb-4">Připraveni vyzkoušet Parton MS?</h2>
            <p class="text-xl mb-8 text-sky-100">
                Začněte monitorovat vaši infrastrukturu ještě dnes
            </p>
            <a href="{{ route('register') }}" wire:navigate class="inline-flex items-center gap-2 px-8 py-4 bg-white text-sky-600 rounded-xl shadow-xl hover:shadow-2xl hover:bg-gray-50 transition-all font-bold text-lg">
                Začít zdarma
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                </svg>
            </a>
        </div>
    </section>

    {{-- FOOTER --}}
    <footer class="py-12 bg-gray-900 text-gray-400">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid md:grid-cols-4 gap-8 mb-8">
                <div class="col-span-2">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-sky-500 to-indigo-600 flex items-center justify-center text-white font-bold">
                            P
                        </div>
                        <div class="text-white font-bold text-xl">
                            Parton <span class="text-sky-400">MS</span>
                        </div>
                    </div>
                    <p class="text-gray-500 max-w-sm">
                        Profesionální monitorovací systém pro vaši IT infrastrukturu s real-time aktualizacemi a přehledným dashboardem.
                    </p>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-4">Produkt</h4>
                    <ul class="space-y-2">
                        <li><a href="#features" class="hover:text-white transition-colors">Vlastnosti</a></li>
                        <li><a href="#demo" class="hover:text-white transition-colors">Demo</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Dokumentace</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-4">Společnost</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="hover:text-white transition-colors">O nás</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Kontakt</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Podpora</a></li>
                    </ul>
                </div>
            </div>
            <div class="pt-8 border-t border-gray-800 text-center text-sm">
                &copy; {{ date('Y') }} Parton Monitoring System. Všechna práva vyhrazena.
            </div>
        </div>
    </footer>

    @script
    <script>
        // Chart instances
        let demoChart = null;
        let liveChart = null;

        // Initialize Demo Chart (top dashboard preview)
        function initDemoChart() {
            const ctx = document.getElementById('demo-cpu-chart');
            if (!ctx) return;

            if (demoChart) {
                demoChart.destroy();
            }

            demoChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['-25s', '-20s', '-15s', '-10s', '-5s', 'now'],
                    datasets: [{
                        label: 'CPU %',
                        data: [25, 35, 42, 40, 38, 44],
                        tension: 0.4,
                        borderWidth: 2,
                        borderColor: '#0ea5e9',
                        backgroundColor: 'rgba(14, 165, 233, 0.1)',
                        fill: true,
                        pointRadius: 3,
                        pointHoverRadius: 5,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                        }
                    },
                    scales: {
                        x: {
                            display: true,
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            min: 0,
                            max: 100,
                            ticks: {
                                callback: value => value + '%'
                            }
                        }
                    },
                },
            });
        }

        // Initialize Live Chart (demo section)
        function initLiveChart() {
            const ctx = document.getElementById('live-cpu-chart');
            if (!ctx) return;

            if (liveChart) {
                liveChart.destroy();
            }

            const devices = @json($devices);

            liveChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: devices.map(d => d.name),
                    datasets: [{
                        label: 'CPU %',
                        data: devices.map(d => d.cpu),
                        backgroundColor: devices.map(d => {
                            if (d.status === 'critical') return '#ef4444';
                            if (d.status === 'warning') return '#f59e0b';
                            return '#0ea5e9';
                        }),
                        borderRadius: 6,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'CPU: ' + context.parsed.y + '%';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            min: 0,
                            max: 100,
                            ticks: {
                                callback: value => value + '%'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    },
                },
            });
        }

        // Update charts when data changes
        function updateCharts() {
            const devices = @json($devices);

            // Update live chart
            if (liveChart) {
                liveChart.data.labels = devices.map(d => d.name);
                liveChart.data.datasets[0].data = devices.map(d => d.cpu);
                liveChart.data.datasets[0].backgroundColor = devices.map(d => {
                    if (d.status === 'critical') return '#ef4444';
                    if (d.status === 'warning') return '#f59e0b';
                    return '#0ea5e9';
                });
                liveChart.update('none');
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', () => {
            initDemoChart();
            initLiveChart();
        });

        // Re-initialize after Livewire updates
        Livewire.hook('morph.updated', () => {
            setTimeout(() => {
                if (!demoChart) initDemoChart();
                if (!liveChart) initLiveChart();
                updateCharts();
            }, 100);
        });

        // Update charts on polling
        document.addEventListener('livewire:update', () => {
            updateCharts();
        });
    </script>
    @endscript
</div>
