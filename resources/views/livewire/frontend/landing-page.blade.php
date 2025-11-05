<section class="relative bg-gray-950 text-gray-100 overflow-hidden">
    {{-- === NAVBAR === --}}
    <nav
        x-data="{ scrolled: false }"
        x-init="window.addEventListener('scroll', () => scrolled = window.scrollY > 50)"
        :class="scrolled ? 'bg-gray-900/70 backdrop-blur-md shadow-lg shadow-cyan-500/10 border-b border-gray-800' : 'bg-transparent'"
        class="fixed top-0 left-0 w-full z-50 transition-all duration-500 ease-in-out"
    >
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
            <div class="text-2xl font-bold text-cyan-400 tracking-tight">
                Parton<span class="text-blue-400">MS</span>
            </div>
            <div class="hidden md:flex gap-8 text-sm font-medium">
                <a href="#features" class="hover:text-cyan-400 transition">Vlastnosti</a>
{{--                <a href="#architecture" class="hover:text-cyan-400 transition">Architektura</a>
                <a href="#about" class="hover:text-cyan-400 transition">O projektu</a>--}}
                <a href="{{ route('login') }}" class="px-4 py-2 bg-cyan-600 hover:bg-cyan-700 text-white rounded-lg transition">Přihlásit</a>
            </div>
        </div>
    </nav>

    {{-- === HERO === --}}
    <div class="relative flex flex-col items-center justify-center text-center min-h-screen px-6 sm:px-10 pt-32 sm:pt-40">
        <div class="max-w-5xl animate-fade-in">
            <h1 class="text-5xl sm:text-7xl font-extrabold mb-6 text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 via-blue-400 to-indigo-500 leading-tight">
                Monitoruj. Analyzuj. Reaguj.
            </h1>

            <p class="text-gray-400 text-lg sm:text-xl max-w-3xl mx-auto mb-10 leading-relaxed">
                <span class="text-cyan-400 font-semibold">Parton Monitoring System</span> poskytuje komplexní přehled o stavu vašich zařízení –
                využívá REST API agenty a moderní backend, který zajišťuje téměř realtime aktualizace pomocí WebSocketů a Livewire.
            </p>

            <div class="flex flex-wrap justify-center gap-4">
                <a href="{{ route('login') }}" class="px-8 py-3 rounded-lg bg-gradient-to-r from-cyan-500 to-blue-600 hover:opacity-90 font-semibold text-white transition duration-300 shadow-lg shadow-cyan-500/20">
                    Spustit konzoli
                </a>
                <a href="#features" class="px-8 py-3 rounded-lg border border-gray-700 hover:bg-gray-800 text-gray-200 transition duration-300">
                    Zjistit více
                </a>
            </div>
        </div>

        {{-- Motion pozadí (gradientové bubliny) --}}
        <div class="absolute inset-0 -z-10 overflow-hidden">
            <div class="absolute w-[40rem] h-[40rem] bg-cyan-600/30 blur-3xl rounded-full -top-40 -left-40 animate-pulse"></div>
            <div class="absolute w-[50rem] h-[50rem] bg-blue-700/20 blur-3xl rounded-full -bottom-40 -right-20 animate-pulse"></div>
            <div class="absolute w-96 h-96 bg-indigo-500/20 blur-3xl rounded-full top-1/3 left-1/2 -translate-x-1/2 animate-float-slow"></div>
        </div>

        {{-- Light reflection animation --}}
        <div class="absolute top-0 left-1/2 w-[120%] h-[120%] bg-gradient-to-br from-cyan-400/10 via-transparent to-indigo-600/10 blur-3xl opacity-70 animate-gradient-move -z-20"></div>
    </div>

    {{-- === FEATURE sekce === --}}
    <section id="features" class="py-24 bg-gray-900/60 backdrop-blur-sm border-t border-gray-800">
        <div class="max-w-6xl mx-auto px-6 text-center">
            <h2 class="text-4xl font-bold mb-12 text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-blue-500">
                Klíčové vlastnosti
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-10">
                <div class="p-8 rounded-2xl bg-gray-800/70 border border-gray-700 hover:border-cyan-500 transition duration-300 shadow-md hover:shadow-cyan-500/20">
                    <h3 class="text-xl font-semibold mb-3 text-cyan-400">REST API agenty</h3>
                    <p class="text-gray-400">Každý počítač běží s agentem, který bezpečně odesílá systémová data přes REST API do centrálního serveru.</p>
                </div>
                <div class="p-8 rounded-2xl bg-gray-800/70 border border-gray-700 hover:border-cyan-500 transition duration-300 shadow-md hover:shadow-cyan-500/20">
                    <h3 class="text-xl font-semibold mb-3 text-cyan-400">Realtime přehled</h3>
                    <p class="text-gray-400">Díky WebSocketům a Livewire pollingu vidíš všechny změny okamžitě na dashboardu bez reloadu.</p>
                </div>
                <div class="p-8 rounded-2xl bg-gray-800/70 border border-gray-700 hover:border-cyan-500 transition duration-300 shadow-md hover:shadow-cyan-500/20">
                    <h3 class="text-xl font-semibold mb-3 text-cyan-400">Bezpečnost</h3>
                    <p class="text-gray-400">Šifrovaná komunikace, autorizace tokenů a audit logy zaručují maximální bezpečí přenášených dat.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- === FOOTER === --}}
    <footer class="py-10 bg-gray-950 text-center text-gray-500 text-sm border-t border-gray-800">
        &copy; {{ date('Y') }} <span class="text-cyan-400 font-semibold">Parton Monitoring System</span>.
        Všechna práva vyhrazena.
    </footer>
</section>
