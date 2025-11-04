<section class="relative overflow-hidden bg-gray-950 text-gray-100">
    {{-- HERO sekce --}}
    <div class="relative flex flex-col items-center justify-center text-center min-h-screen px-6 sm:px-10">
        <div class="max-w-5xl">
            <h1 class="text-5xl sm:text-7xl font-extrabold mb-6 text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 via-blue-400 to-indigo-500 animate-fade-in">
                Parton Monitoring System
            </h1>

            <p class="text-gray-400 text-lg sm:text-xl max-w-3xl mx-auto mb-10 leading-relaxed">
                Inteligentní systém pro <span class="text-cyan-400 font-semibold">monitorování počítačů</span> v síti.  
                Každý agent bezpečně odesílá systémové údaje přes REST API, zatímco server poskytuje téměř realtime přehled
                díky integrovaným notifikacím a WebSocket komunikaci.
            </p>

            <div class="flex flex-wrap justify-center gap-4">
                <a href="{{ route('login') }}" class="px-8 py-3 rounded-lg bg-gradient-to-r from-cyan-500 to-blue-600 hover:opacity-90 font-semibold text-white transition duration-300 shadow-lg shadow-cyan-500/20">
                    Přihlásit se
                </a>
                <a href="#features" class="px-8 py-3 rounded-lg border border-gray-700 hover:bg-gray-800 text-gray-200 transition duration-300">
                    Zjistit více
                </a>
            </div>
        </div>

        {{-- Motion pozadí --}}
        <div class="absolute inset-0 -z-10 overflow-hidden">
            <div class="absolute w-[40rem] h-[40rem] bg-cyan-600/20 blur-3xl rounded-full -top-20 -left-40 animate-pulse"></div>
            <div class="absolute w-[40rem] h-[40rem] bg-blue-700/20 blur-3xl rounded-full -bottom-40 -right-20 animate-pulse"></div>
        </div>
    </div>

    {{-- Sekce o systému --}}
    <section id="features" class="py-24 bg-gray-900/60 backdrop-blur-sm border-t border-gray-800">
        <div class="max-w-6xl mx-auto px-6 text-center">
            <h2 class="text-4xl font-bold mb-12 text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-blue-500">
                Proč Parton Monitoring System?
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-10">
                <div class="p-8 rounded-2xl bg-gray-800/70 border border-gray-700 hover:border-cyan-500 transition duration-300 shadow-md hover:shadow-cyan-500/20">
                    <h3 class="text-xl font-semibold mb-3 text-cyan-400">REST API komunikace</h3>
                    <p class="text-gray-400">Každý agent odesílá údaje o stavu zařízení (CPU, RAM, disk, síť) bezpečně přes REST API, bez nutnosti trvalého připojení.</p>
                </div>
                <div class="p-8 rounded-2xl bg-gray-800/70 border border-gray-700 hover:border-cyan-500 transition duration-300 shadow-md hover:shadow-cyan-500/20">
                    <h3 class="text-xl font-semibold mb-3 text-cyan-400">Téměř realtime monitoring</h3>
                    <p class="text-gray-400">Server zpracovává data okamžitě po přijetí a pomocí WebSocketů či Livewire polling zajišťuje aktuální zobrazení stavu.</p>
                </div>
                <div class="p-8 rounded-2xl bg-gray-800/70 border border-gray-700 hover:border-cyan-500 transition duration-300 shadow-md hover:shadow-cyan-500/20">
                    <h3 class="text-xl font-semibold mb-3 text-cyan-400">Bezpečnost a spolehlivost</h3>
                    <p class="text-gray-400">Komunikace mezi agentem a serverem probíhá šifrovaně a autentizovaně, aby nedošlo k neoprávněnému přístupu.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Architektura sekce --}}
    <section class="py-24 bg-gray-950 border-t border-gray-800">
        <div class="max-w-6xl mx-auto px-6 text-center">
            <h2 class="text-4xl font-bold mb-10 text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-indigo-500">
                Architektura systému
            </h2>
            <p class="text-gray-400 max-w-3xl mx-auto mb-10">
                Parton Monitoring System je navržen modulárně – <span class="text-cyan-400">Frontend</span> pro prezentaci a dokumentaci,
                <span class="text-cyan-400">Backend</span> pro administrátory a správu agentů, a
                <span class="text-cyan-400">Customer Zone</span> pro klienty sledující vlastní zařízení.
            </p>

            <img src="/images/architecture-diagram.svg" alt="System Architecture" class="mx-auto w-full max-w-4xl rounded-xl shadow-lg shadow-blue-500/10">
        </div>
    </section>

    {{-- Footer --}}
    <footer class="py-10 bg-gray-950 text-center text-gray-500 text-sm border-t border-gray-800">
        &copy; {{ date('Y') }} <span class="text-cyan-400 font-semibold">Parton Monitoring System</span>.  
        Všechna práva vyhrazena.
    </footer>
</section>
