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
            {{--
                <a href="#architecture" class="hover:text-cyan-400 transition">Architektura</a>
                <a href="#about" class="hover:text-cyan-400 transition">O projektu</a>
            --}}
                <a href="{{ route('login') }}" class="px-4 py-2 bg-cyan-600 hover:bg-cyan-700 text-white rounded-lg transition">Přihlásit</a>
            </div>
        </div>
    </nav>

    {{-- === HERO s VANTA.js animací === --}}
    <div id="vanta-bg" class="relative flex flex-col items-center justify-center text-center min-h-screen px-6 sm:px-10 pt-32 sm:pt-40 overflow-hidden">
        {{-- Obsah --}}
        <div class="max-w-5xl relative z-10 animate-fade-in">
            <h1 class="text-5xl sm:text-7xl font-extrabold mb-6 text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 via-blue-400 to-indigo-500 leading-tight">
                Monitoruj. Analyzuj. Reaguj.
            </h1>

            <p class="text-gray-400 text-lg sm:text-xl max-w-3xl mx-auto mb-10 leading-relaxed">
                <span class="text-cyan-400 font-semibold">Parton Monitoring System</span> –
                inteligentní platforma pro sledování a správu zařízení v reálném čase,
                komunikující přes REST API a zobrazující živá data pomocí WebSocketů.
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

        {{-- Gradient overlay pro čitelnost textu --}}
        <div class="absolute inset-0 bg-gradient-to-b from-gray-950/70 via-gray-950/50 to-gray-950/90 z-0"></div>
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
                    <p class="text-gray-400">Každý počítač odesílá systémová data přes REST API do centrálního serveru.</p>
                </div>
                <div class="p-8 rounded-2xl bg-gray-800/70 border border-gray-700 hover:border-cyan-500 transition duration-300 shadow-md hover:shadow-cyan-500/20">
                    <h3 class="text-xl font-semibold mb-3 text-cyan-400">Realtime přehled</h3>
                    <p class="text-gray-400">Díky WebSocketům a Livewire pollingu se dashboard aktualizuje okamžitě bez reloadu.</p>
                </div>
                <div class="p-8 rounded-2xl bg-gray-800/70 border border-gray-700 hover:border-cyan-500 transition duration-300 shadow-md hover:shadow-cyan-500/20">
                    <h3 class="text-xl font-semibold mb-3 text-cyan-400">Bezpečnost</h3>
                    <p class="text-gray-400">Šifrovaná komunikace a autentizace tokeny chrání veškerá přenášená data.</p>
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
