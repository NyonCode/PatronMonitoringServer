<div class="relative min-h-screen bg-gradient-to-br from-slate-950 via-blue-950 to-slate-900 text-white overflow-hidden" wire:poll.5s>

    {{-- Animated background - Custom network visualization --}}
    <div class="fixed inset-0 pointer-events-none">
        <canvas id="network-canvas" class="w-full h-full opacity-20"></canvas>
    </div>

    {{-- Floating metrics particles --}}
    <div class="fixed inset-0 pointer-events-none overflow-hidden">
        <div class="metrics-particle" style="--delay: 0s; --x: 10%; --y: 20%;">CPU: 45%</div>
        <div class="metrics-particle" style="--delay: 2s; --x: 80%; --y: 15%;">RAM: 67%</div>
        <div class="metrics-particle" style="--delay: 4s; --x: 20%; --y: 80%;">GPU: 23%</div>
        <div class="metrics-particle" style="--delay: 6s; --x: 90%; --y: 70%;">DISK: 89%</div>
    </div>

    {{-- NAVBAR --}}
    <nav x-data="{ scrolled: false, open: false }" 
         x-init="window.addEventListener('scroll', () => scrolled = window.scrollY > 48)"
         :class="scrolled ? 'bg-slate-900/80 backdrop-blur-xl border-b border-slate-700/50 shadow-2xl' : 'bg-transparent'"
         class="fixed w-full z-50 transition-all duration-500">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex items-center justify-between h-20">
                {{-- Logo --}}
                <a href="#" class="flex items-center gap-3 group">
                    <div class="relative">
                        <div class="absolute inset-0 bg-gradient-to-br from-cyan-400 to-blue-600 rounded-xl blur-lg opacity-75 group-hover:opacity-100 transition-opacity"></div>
                        <div class="relative w-12 h-12 rounded-xl bg-gradient-to-br from-cyan-500 to-blue-600 flex items-center justify-center text-white font-bold shadow-2xl transform group-hover:scale-110 group-hover:rotate-3 transition-all duration-300">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="hidden sm:block">
                        <div class="text-xl font-bold bg-gradient-to-r from-cyan-400 to-blue-400 bg-clip-text text-transparent">
                            Parton
                        </div>
                        <div class="text-xs text-slate-400 -mt-1">Monitoring System</div>
                    </div>
                </a>

                {{-- Desktop Menu --}}
                <div class="hidden md:flex items-center gap-8">
                    <a href="#features" class="text-slate-300 hover:text-cyan-400 transition-colors font-medium text-sm relative group">
                        Vlastnosti
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-gradient-to-r from-cyan-400 to-blue-400 group-hover:w-full transition-all duration-300"></span>
                    </a>
                    <a href="#demo" class="text-slate-300 hover:text-cyan-400 transition-colors font-medium text-sm relative group">
                        Demo
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-gradient-to-r from-cyan-400 to-blue-400 group-hover:w-full transition-all duration-300"></span>
                    </a>
                    <a href="{{ route('login') }}" wire:navigate 
                       class="group relative px-6 py-3 overflow-hidden rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 text-white font-semibold shadow-lg shadow-cyan-500/50 hover:shadow-cyan-500/75 transition-all duration-300">
                        <span class="relative z-10">Přihlásit se</span>
                        <div class="absolute inset-0 bg-gradient-to-r from-cyan-400 to-blue-500 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    </a>
                </div>

                {{-- Mobile button --}}
                <button @click="open = !open" class="md:hidden p-2 rounded-lg hover:bg-slate-800 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        <path x-show="open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Mobile menu --}}
        <div x-show="open" x-transition class="md:hidden bg-slate-900/95 backdrop-blur-xl border-t border-slate-700/50">
            <div class="px-4 py-6 flex flex-col gap-4">
                <a href="#features" class="text-slate-300 hover:text-cyan-400 px-4 py-3 rounded-lg hover:bg-slate-800/50 transition-all">Vlastnosti</a>
                <a href="#demo" class="text-slate-300 hover:text-cyan-400 px-4 py-3 rounded-lg hover:bg-slate-800/50 transition-all">Demo</a>
                <a href="{{ route('login') }}" wire:navigate class="text-center px-4 py-3 bg-gradient-to-r from-cyan-500 to-blue-600 text-white rounded-lg shadow-lg">Přihlásit se</a>
            </div>
        </div>
    </nav>

    {{-- HERO SECTION - Full page --}}
    <section id="hero" class="min-h-screen flex items-center justify-center relative pt-20">
        <div class="max-w-7xl mx-auto px-6 grid lg:grid-cols-2 gap-16 items-center">
            {{-- Left content --}}
            <div class="space-y-8 z-10" x-data x-intersect="$el.classList.add('animate-fade-in-up')">
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-cyan-500/10 to-blue-500/10 border border-cyan-500/20 rounded-full text-sm font-medium backdrop-blur-sm">
                    <div class="w-2 h-2 bg-cyan-400 rounded-full animate-pulse"></div>
                    <span class="bg-gradient-to-r from-cyan-400 to-blue-400 bg-clip-text text-transparent">
                        Real-time monitoring
                    </span>
                </div>

                <h1 class="text-5xl lg:text-7xl font-extrabold leading-tight">
                    <span class="block text-white">Dokonalý</span>
                    <span class="block bg-gradient-to-r from-cyan-400 via-blue-400 to-purple-400 bg-clip-text text-transparent animate-gradient-x">
                        přehled
                    </span>
                    <span class="block text-white">o vaší infrastruktuře</span>
                </h1>

                <p class="text-xl text-slate-300 leading-relaxed max-w-xl">
                    Profesionální monitorovací systém s REST API, real-time aktualizacemi a přehledným dashboardem. 
                    <span class="text-cyan-400 font-semibold">Sledujte vše na jednom místě.</span>
                </p>

                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="#demo" class="group relative inline-flex items-center justify-center gap-3 px-8 py-4 bg-gradient-to-r from-cyan-500 to-blue-600 rounded-xl font-bold text-lg shadow-2xl shadow-cyan-500/50 hover:shadow-cyan-500/75 overflow-hidden transition-all duration-300 hover:scale-105">
                        <span class="relative z-10">Vyzkoušet demo</span>
                        <svg class="relative z-10 w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                        <div class="absolute inset-0 bg-gradient-to-r from-cyan-400 to-blue-500 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    </a>
                    <a href="#features" class="inline-flex items-center justify-center gap-2 px-8 py-4 border-2 border-slate-600 rounded-xl text-slate-300 hover:border-cyan-500 hover:text-cyan-400 hover:bg-cyan-500/10 transition-all duration-300 font-bold text-lg backdrop-blur-sm">
                        <span>Zjistit více</span>
                    </a>
                </div>

                {{-- Stats --}}
                <div class="grid grid-cols-3 gap-4 pt-8">
                    <div class="p-4 rounded-xl bg-slate-800/50 border border-slate-700/50 backdrop-blur-sm hover:border-cyan-500/50 transition-all">
                        <div class="text-3xl font-bold bg-gradient-to-r from-cyan-400 to-blue-400 bg-clip-text text-transparent">99.9%</div>
                        <div class="text-xs text-slate-400 mt-1">Uptime</div>
                    </div>
                    <div class="p-4 rounded-xl bg-slate-800/50 border border-slate-700/50 backdrop-blur-sm hover:border-cyan-500/50 transition-all">
                        <div class="text-3xl font-bold bg-gradient-to-r from-cyan-400 to-blue-400 bg-clip-text text-transparent">&lt;100ms</div>
                        <div class="text-xs text-slate-400 mt-1">Response</div>
                    </div>
                    <div class="p-4 rounded-xl bg-slate-800/50 border border-slate-700/50 backdrop-blur-sm hover:border-cyan-500/50 transition-all">
                        <div class="text-3xl font-bold bg-gradient-to-r from-cyan-400 to-blue-400 bg-clip-text text-transparent">24/7</div>
                        <div class="text-xs text-slate-400 mt-1">Support</div>
                    </div>
                </div>
            </div>

            {{-- Right - Dashboard Preview with Live Chart --}}
            <div class="relative z-10" x-data x-intersect="$el.classList.add('animate-fade-in-right')">
                <div class="absolute inset-0 bg-gradient-to-r from-cyan-500/20 to-blue-500/20 rounded-3xl blur-3xl"></div>
                <div class="relative bg-slate-900/90 backdrop-blur-xl rounded-3xl shadow-2xl border border-slate-700/50 p-8 transform hover:scale-[1.02] transition-all duration-500">
                    {{-- Header --}}
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <div class="text-sm font-medium text-slate-400 mb-1">Aktivní zařízení</div>
                            <div class="text-5xl font-bold bg-gradient-to-r from-cyan-400 to-blue-400 bg-clip-text text-transparent">
                                {{ count($devices) }}
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-medium text-slate-400 mb-1">Online</div>
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                                <div class="text-3xl font-bold text-green-400">
                                    {{ collect($devices)->where('status', '!=', 'offline')->count() }}
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Chart --}}
                    <div class="mb-6 h-48 relative">
                        <canvas id="hero-chart" class="w-full h-full"></canvas>
                    </div>

                    {{-- Metrics Grid --}}
                    <div class="grid grid-cols-3 gap-4">
                        @php
                            $avgCpu = round(collect($devices)->avg('cpu'));
                            $avgMem = round(collect($devices)->avg('mem'));
                            $criticalCount = collect($devices)->where('status', 'critical')->count();
                        @endphp
                        <div class="p-4 bg-gradient-to-br from-slate-800/50 to-slate-700/30 rounded-xl border border-slate-600/50 hover:border-cyan-500/50 transition-all">
                            <div class="text-3xl font-bold text-cyan-400">{{ $avgCpu }}%</div>
                            <div class="text-xs text-slate-400 mt-1">Prům. CPU</div>
                        </div>
                        <div class="p-4 bg-gradient-to-br from-slate-800/50 to-slate-700/30 rounded-xl border border-slate-600/50 hover:border-blue-500/50 transition-all">
                            <div class="text-3xl font-bold text-blue-400">{{ $avgMem }}%</div>
                            <div class="text-xs text-slate-400 mt-1">Prům. RAM</div>
                        </div>
                        <div class="p-4 bg-gradient-to-br from-slate-800/50 to-slate-700/30 rounded-xl border border-slate-600/50 hover:border-{{ $criticalCount > 0 ? 'red' : 'green' }}-500/50 transition-all">
                            <div class="text-3xl font-bold {{ $criticalCount > 0 ? 'text-red-400' : 'text-green-400' }}">
                                {{ $criticalCount }}
                            </div>
                            <div class="text-xs text-slate-400 mt-1">Kritické</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Scroll indicator --}}
        <div class="absolute bottom-10 left-1/2 transform -translate-x-1/2 animate-bounce">
            <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
            </svg>
        </div>
    </section>

    {{-- FEATURES SECTION --}}
    <section id="features" class="py-32 relative">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-20" x-data x-intersect="$el.classList.add('animate-fade-in-up')">
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-cyan-500/10 border border-cyan-500/20 rounded-full text-sm font-medium mb-6">
                    <span class="text-cyan-400">✨ Funkce</span>
                </div>
                <h2 class="text-5xl font-bold text-white mb-6">
                    Vše, co potřebujete<br/>
                    <span class="bg-gradient-to-r from-cyan-400 to-blue-400 bg-clip-text text-transparent">na jednom místě</span>
                </h2>
                <p class="text-xl text-slate-400 max-w-2xl mx-auto">
                    Komplexní řešení pro sledování výkonu, logování a správu vaší IT infrastruktury
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                {{-- Feature 1 --}}
                <div class="group relative" x-data x-intersect="$el.classList.add('animate-fade-in-up')" style="animation-delay: 0.1s">
                    <div class="absolute inset-0 bg-gradient-to-r from-cyan-500/20 to-blue-500/20 rounded-2xl blur-xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <div class="relative p-8 bg-slate-900/50 backdrop-blur-xl rounded-2xl border border-slate-700/50 hover:border-cyan-500/50 transition-all duration-500 h-full">
                        <div class="w-16 h-16 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-2xl flex items-center justify-center mb-6 transform group-hover:scale-110 group-hover:rotate-3 transition-all duration-300 shadow-lg shadow-cyan-500/50">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-white mb-4">Real-time monitoring</h3>
                        <p class="text-slate-400 leading-relaxed">
                            Sledujte CPU, RAM, GPU a diskový prostor v reálném čase s minimální latencí a okamžitými notifikacemi.
                        </p>
                    </div>
                </div>

                {{-- Feature 2 --}}
                <div class="group relative" x-data x-intersect="$el.classList.add('animate-fade-in-up')" style="animation-delay: 0.2s">
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-500/20 to-purple-500/20 rounded-2xl blur-xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <div class="relative p-8 bg-slate-900/50 backdrop-blur-xl rounded-2xl border border-slate-700/50 hover:border-blue-500/50 transition-all duration-500 h-full">
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center mb-6 transform group-hover:scale-110 group-hover:rotate-3 transition-all duration-300 shadow-lg shadow-blue-500/50">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-white mb-4">Zabezpečená komunikace</h3>
                        <p class="text-slate-400 leading-relaxed">
                            TLS šifrování, API tokeny a role-based přístup zajišťují maximální bezpečnost vašich dat.
                        </p>
                    </div>
                </div>

                {{-- Feature 3 --}}
                <div class="group relative" x-data x-intersect="$el.classList.add('animate-fade-in-up')" style="animation-delay: 0.3s">
                    <div class="absolute inset-0 bg-gradient-to-r from-purple-500/20 to-pink-500/20 rounded-2xl blur-xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <div class="relative p-8 bg-slate-900/50 backdrop-blur-xl rounded-2xl border border-slate-700/50 hover:border-purple-500/50 transition-all duration-500 h-full">
                        <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-pink-600 rounded-2xl flex items-center justify-center mb-6 transform group-hover:scale-110 group-hover:rotate-3 transition-all duration-300 shadow-lg shadow-purple-500/50">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-white mb-4">Flexibilní API</h3>
                        <p class="text-slate-400 leading-relaxed">
                            REST API s kompletní dokumentací pro snadnou integraci s vašimi systémy a aplikacemi.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- DEMO SECTION --}}
    <section id="demo" class="py-32 relative">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-20" x-data x-intersect="$el.classList.add('animate-fade-in-up')">
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-cyan-500/10 border border-cyan-500/20 rounded-full text-sm font-medium mb-6">
                    <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                    <span class="text-cyan-400">Live Demo</span>
                </div>
                <h2 class="text-5xl font-bold text-white mb-6">
                    Sledujte živá data<br/>
                    <span class="bg-gradient-to-r from-cyan-400 to-blue-400 bg-clip-text text-transparent">v reálném čase</span>
                </h2>
            </div>

            <div class="grid lg:grid-cols-2 gap-8">
                {{-- Device list --}}
                <div class="bg-slate-900/50 backdrop-blur-xl rounded-2xl shadow-2xl p-8 border border-slate-700/50" x-data x-intersect="$el.classList.add('animate-fade-in-left')">
                    <div class="flex items-center justify-between mb-8">
                        <h3 class="text-2xl font-bold text-white">Monitorovaná zařízení</h3>
                        <div class="flex items-center gap-2 text-sm text-slate-400">
                            <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                            Aktualizace každých 5s
                        </div>
                    </div>

                    <div class="space-y-3 max-h-[600px] overflow-y-auto custom-scrollbar">
                        @foreach($devices as $device)
                            <div class="group p-5 bg-slate-800/50 rounded-xl border border-slate-700/50 hover:border-cyan-500/50 hover:bg-slate-800/80 transition-all duration-300">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-3 h-3 rounded-full {{ $device['status'] === 'critical' ? 'bg-red-500 shadow-lg shadow-red-500/50' : ($device['status'] === 'warning' ? 'bg-yellow-500 shadow-lg shadow-yellow-500/50' : 'bg-green-500 shadow-lg shadow-green-500/50') }} animate-pulse"></div>
                                        <span class="font-bold text-white">{{ $device['name'] }}</span>
                                    </div>
                                    <span class="px-3 py-1 rounded-full text-xs font-bold {{ $device['status'] === 'critical' ? 'bg-red-500/20 text-red-400 border border-red-500/50' : ($device['status'] === 'warning' ? 'bg-yellow-500/20 text-yellow-400 border border-yellow-500/50' : 'bg-green-500/20 text-green-400 border border-green-500/50') }}">
                                        {{ ucfirst($device['status']) }}
                                    </span>
                                </div>
                                <div class="grid grid-cols-3 gap-4">
                                    <div class="text-center">
                                        <div class="text-xs text-slate-400 mb-1">CPU</div>
                                        <div class="text-lg font-bold text-cyan-400">{{ $device['cpu'] }}%</div>
                                        <div class="w-full bg-slate-700 rounded-full h-1.5 mt-2">
                                            <div class="bg-gradient-to-r from-cyan-500 to-blue-500 h-1.5 rounded-full transition-all duration-500" style="width: {{ $device['cpu'] }}%"></div>
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-xs text-slate-400 mb-1">RAM</div>
                                        <div class="text-lg font-bold text-blue-400">{{ $device['mem'] }}%</div>
                                        <div class="w-full bg-slate-700 rounded-full h-1.5 mt-2">
                                            <div class="bg-gradient-to-r from-blue-500 to-purple-500 h-1.5 rounded-full transition-all duration-500" style="width: {{ $device['mem'] }}%"></div>
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-xs text-slate-400 mb-1">Disk</div>
                                        <div class="text-lg font-bold text-purple-400">{{ $device['disk'] }}%</div>
                                        <div class="w-full bg-slate-700 rounded-full h-1.5 mt-2">
                                            <div class="bg-gradient-to-r from-purple-500 to-pink-500 h-1.5 rounded-full transition-all duration-500" style="width: {{ $device['disk'] }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Live chart --}}
                <div class="bg-slate-900/50 backdrop-blur-xl rounded-2xl shadow-2xl p-8 border border-slate-700/50" x-data x-intersect="$el.classList.add('animate-fade-in-right')">
                    <h3 class="text-2xl font-bold text-white mb-8">Zatížení CPU (live)</h3>
                    <div class="h-[600px]">
                        <canvas id="demo-chart"></canvas>
                    </div>
                    <p class="text-sm text-slate-400 mt-6 text-center">
                        Data jsou v demo režimu simulována pro ukázku funkcionality
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA Section --}}
    <section class="py-32 relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-r from-cyan-500/10 to-blue-500/10"></div>
        <div class="max-w-4xl mx-auto px-6 text-center relative z-10" x-data x-intersect="$el.classList.add('animate-fade-in-up')">
            <h2 class="text-5xl font-bold text-white mb-6">
                Připraveni začít<br/>
                <span class="bg-gradient-to-r from-cyan-400 to-blue-400 bg-clip-text text-transparent">monitorovat?</span>
            </h2>
            <p class="text-xl text-slate-300 mb-10">
                Začněte sledovat vaši infrastrukturu ještě dnes
            </p>
            <a href="{{ route('register') }}" wire:navigate 
               class="group relative inline-flex items-center gap-3 px-10 py-5 bg-gradient-to-r from-cyan-500 to-blue-600 rounded-xl font-bold text-lg shadow-2xl shadow-cyan-500/50 hover:shadow-cyan-500/75 overflow-hidden transition-all duration-300 hover:scale-105">
                <span class="relative z-10">Začít zdarma</span>
                <svg class="relative z-10 w-6 h-6 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                </svg>
                <div class="absolute inset-0 bg-gradient-to-r from-cyan-400 to-blue-500 opacity-0 group-hover:opacity-100 transition-opacity"></div>
            </a>
        </div>
    </section>

    {{-- FOOTER --}}
    <footer class="py-16 bg-slate-950/50 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid md:grid-cols-4 gap-12 mb-12">
                <div class="col-span-2">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-cyan-500 to-blue-600 flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <div>
                            <div class="text-lg font-bold text-white">Parton MS</div>
                            <div class="text-xs text-slate-400">Monitoring System</div>
                        </div>
                    </div>
                    <p class="text-slate-400 max-w-sm leading-relaxed">
                        Profesionální monitorovací systém pro vaši IT infrastrukturu s real-time aktualizacemi a přehledným dashboardem.
                    </p>
                </div>
                <div>
                    <h4 class="text-white font-bold mb-4">Produkt</h4>
                    <ul class="space-y-3">
                        <li><a href="#features" class="text-slate-400 hover:text-cyan-400 transition-colors">Vlastnosti</a></li>
                        <li><a href="#demo" class="text-slate-400 hover:text-cyan-400 transition-colors">Demo</a></li>
                        <li><a href="#" class="text-slate-400 hover:text-cyan-400 transition-colors">Dokumentace</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-bold mb-4">Společnost</h4>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-slate-400 hover:text-cyan-400 transition-colors">O nás</a></li>
                        <li><a href="#" class="text-slate-400 hover:text-cyan-400 transition-colors">Kontakt</a></li>
                        <li><a href="#" class="text-slate-400 hover:text-cyan-400 transition-colors">Podpora</a></li>
                    </ul>
                </div>
            </div>
            <div class="pt-8 border-t border-slate-800 text-center text-sm text-slate-500">
                &copy; {{ date('Y') }} Parton Monitoring System. Všechna práva vyhrazena.
            </div>
        </div>
    </footer>

    {{-- Custom Styles --}}
    <style>
        @keyframes gradient-x {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }
        
        .animate-gradient-x {
            background-size: 200% 200%;
            animation: gradient-x 3s ease infinite;
        }

        @keyframes fade-in-up {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fade-in-left {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes fade-in-right {
            from {
                opacity: 0;
                transform: translateX(30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .animate-fade-in-up {
            animation: fade-in-up 0.8s ease-out forwards;
        }

        .animate-fade-in-left {
            animation: fade-in-left 0.8s ease-out forwards;
        }

        .animate-fade-in-right {
            animation: fade-in-right 0.8s ease-out forwards;
        }

        .metrics-particle {
            position: absolute;
            left: var(--x);
            top: var(--y);
            font-size: 12px;
            font-weight: 600;
            color: rgba(6, 182, 212, 0.4);
            animation: float 8s ease-in-out infinite;
            animation-delay: var(--delay);
        }

        @keyframes float {
            0%, 100% { transform: translateY(0) translateX(0); opacity: 0; }
            10% { opacity: 0.6; }
            50% { transform: translateY(-100px) translateX(50px); opacity: 0.8; }
            90% { opacity: 0.4; }
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 8px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(51, 65, 85, 0.3);
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: linear-gradient(to bottom, #06b6d4, #3b82f6);
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(to bottom, #0891b2, #2563eb);
        }
    </style>

    {{-- Scripts --}}
    <script>
        // Network Canvas Animation
        function initNetworkCanvas() {
            const canvas = document.getElementById('network-canvas');
            if (!canvas) return;
            
            const ctx = canvas.getContext('2d');
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;

            const nodes = [];
            const nodeCount = 50;

            for (let i = 0; i < nodeCount; i++) {
                nodes.push({
                    x: Math.random() * canvas.width,
                    y: Math.random() * canvas.height,
                    vx: (Math.random() - 0.5) * 0.5,
                    vy: (Math.random() - 0.5) * 0.5,
                    radius: Math.random() * 2 + 1
                });
            }

            function animate() {
                ctx.clearRect(0, 0, canvas.width, canvas.height);

                // Update and draw nodes
                nodes.forEach(node => {
                    node.x += node.vx;
                    node.y += node.vy;

                    if (node.x < 0 || node.x > canvas.width) node.vx *= -1;
                    if (node.y < 0 || node.y > canvas.height) node.vy *= -1;

                    ctx.beginPath();
                    ctx.arc(node.x, node.y, node.radius, 0, Math.PI * 2);
                    ctx.fillStyle = 'rgba(6, 182, 212, 0.5)';
                    ctx.fill();
                });

                // Draw connections
                for (let i = 0; i < nodes.length; i++) {
                    for (let j = i + 1; j < nodes.length; j++) {
                        const dx = nodes[i].x - nodes[j].x;
                        const dy = nodes[i].y - nodes[j].y;
                        const distance = Math.sqrt(dx * dx + dy * dy);

                        if (distance < 150) {
                            ctx.beginPath();
                            ctx.moveTo(nodes[i].x, nodes[i].y);
                            ctx.lineTo(nodes[j].x, nodes[j].y);
                            ctx.strokeStyle = `rgba(6, 182, 212, ${0.2 * (1 - distance / 150)})`;
                            ctx.lineWidth = 1;
                            ctx.stroke();
                        }
                    }
                }

                requestAnimationFrame(animate);
            }

            animate();

            window.addEventListener('resize', () => {
                canvas.width = window.innerWidth;
                canvas.height = window.innerHeight;
            });
        }

        // Initialize charts
        let heroChart = null;
        let demoChart = null;

        function initHeroChart() {
            const ctx = document.getElementById('hero-chart');
            if (!ctx) return;

            if (heroChart) {
                heroChart.destroy();
            }

            const data = @json($devices);
            
            heroChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['-25s', '-20s', '-15s', '-10s', '-5s', 'now'],
                    datasets: [{
                        label: 'CPU %',
                        data: [25, 35, 42, 40, 38, 44],
                        tension: 0.4,
                        borderWidth: 3,
                        borderColor: 'rgb(6, 182, 212)',
                        backgroundColor: 'rgba(6, 182, 212, 0.1)',
                        fill: true,
                        pointRadius: 0,
                        pointHoverRadius: 6,
                        pointBackgroundColor: 'rgb(6, 182, 212)',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                        duration: 750,
                        easing: 'easeInOutQuart'
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            backgroundColor: 'rgba(15, 23, 42, 0.9)',
                            borderColor: 'rgb(6, 182, 212)',
                            borderWidth: 1,
                            padding: 12,
                            titleColor: 'rgb(6, 182, 212)',
                            bodyColor: '#fff',
                        }
                    },
                    scales: {
                        x: {
                            display: true,
                            grid: { display: false },
                            ticks: { color: 'rgb(148, 163, 184)' }
                        },
                        y: {
                            min: 0,
                            max: 100,
                            grid: { 
                                color: 'rgba(148, 163, 184, 0.1)',
                                drawBorder: false
                            },
                            ticks: {
                                color: 'rgb(148, 163, 184)',
                                callback: value => value + '%'
                            }
                        }
                    },
                    interaction: {
                        mode: 'nearest',
                        axis: 'x',
                        intersect: false
                    }
                },
            });
        }

        function initDemoChart() {
            const ctx = document.getElementById('demo-chart');
            if (!ctx) return;

            if (demoChart) {
                demoChart.destroy();
            }

            const devices = @json($devices);

            demoChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: devices.map(d => d.name),
                    datasets: [{
                        label: 'CPU %',
                        data: devices.map(d => d.cpu),
                        backgroundColor: devices.map(d => {
                            if (d.status === 'critical') return 'rgb(239, 68, 68)';
                            if (d.status === 'warning') return 'rgb(245, 158, 11)';
                            return 'rgba(6, 182, 212, 0.8)';
                        }),
                        borderColor: devices.map(d => {
                            if (d.status === 'critical') return 'rgb(239, 68, 68)';
                            if (d.status === 'warning') return 'rgb(245, 158, 11)';
                            return 'rgb(6, 182, 212)';
                        }),
                        borderWidth: 2,
                        borderRadius: 8,
                        borderSkipped: false,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                        duration: 1000,
                        easing: 'easeInOutQuart'
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(15, 23, 42, 0.9)',
                            borderColor: 'rgb(6, 182, 212)',
                            borderWidth: 1,
                            padding: 12,
                            titleColor: 'rgb(6, 182, 212)',
                            bodyColor: '#fff',
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
                            grid: { 
                                color: 'rgba(148, 163, 184, 0.1)',
                                drawBorder: false
                            },
                            ticks: {
                                color: 'rgb(148, 163, 184)',
                                callback: value => value + '%'
                            }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { 
                                color: 'rgb(148, 163, 184)',
                                maxRotation: 45,
                                minRotation: 45
                            }
                        }
                    },
                },
            });
        }

        function updateCharts() {
            const devices = @json($devices);

            if (demoChart) {
                demoChart.data.labels = devices.map(d => d.name);
                demoChart.data.datasets[0].data = devices.map(d => d.cpu);
                demoChart.data.datasets[0].backgroundColor = devices.map(d => {
                    if (d.status === 'critical') return 'rgb(239, 68, 68)';
                    if (d.status === 'warning') return 'rgb(245, 158, 11)';
                    return 'rgba(6, 182, 212, 0.8)';
                });
                demoChart.update('none');
            }
        }

        // Initialize everything
        document.addEventListener('DOMContentLoaded', () => {
            initNetworkCanvas();
            initHeroChart();
            initDemoChart();
        });

        // Livewire hooks
        Livewire.hook('morph.updated', () => {
            setTimeout(() => {
                if (!heroChart) initHeroChart();
                if (!demoChart) initDemoChart();
                updateCharts();
            }, 100);
        });
    </script>
</div>
