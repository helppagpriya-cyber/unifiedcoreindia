<nav class="sticky top-0 z-50 border-b border-slate-200/80 bg-white/95 shadow-sm backdrop-blur" aria-label="Main navigation">
    <div class="mx-auto flex min-h-16 max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
        <a href="{{ route('home') }}" class="flex shrink-0 items-center gap-3" aria-label="Unified Core India home">
            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-cyan-500 to-blue-700 text-sm font-black text-white shadow-sm">UC</span>
            <span class="text-base font-extrabold tracking-tight text-slate-900 sm:text-lg">Unified Core <span class="text-cyan-700">India</span></span>
        </a>

        <div class="hidden items-center gap-7 md:flex">
            <a href="{{ route('home') }}" class="text-sm font-semibold text-slate-600 transition hover:text-cyan-700">Home</a>
            <a href="{{ route('home') }}#about" class="text-sm font-semibold text-slate-600 transition hover:text-cyan-700">About</a>
            <a href="{{ route('home') }}#services" class="text-sm font-semibold text-slate-600 transition hover:text-cyan-700">Services</a>
            <a href="{{ route('home') }}#industries" class="text-sm font-semibold text-slate-600 transition hover:text-cyan-700">Industries</a>
            <a href="{{ route('home') }}#contact" class="text-sm font-semibold text-slate-600 transition hover:text-cyan-700">Contact</a>
        </div>

        <div class="hidden items-center gap-3 md:flex">
            <a href="/admin/login" class="rounded-lg px-3 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-100">Admin</a>
            <a href="/egjewel/login" class="rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-cyan-800 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2">CRM / ERP login</a>
        </div>

        <details class="group relative md:hidden">
            <summary class="flex h-10 w-10 cursor-pointer list-none items-center justify-center rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50" aria-label="Open navigation menu">
                <x-heroicon-o-bars-3 class="h-5 w-5 group-open:hidden" />
                <x-heroicon-o-x-mark class="hidden h-5 w-5 group-open:block" />
            </summary>
            <div class="absolute right-0 top-12 z-50 w-64 rounded-2xl border border-slate-200 bg-white p-3 shadow-xl">
                <div class="grid gap-1">
                    <a href="{{ route('home') }}" class="rounded-lg px-3 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Home</a>
                    <a href="{{ route('home') }}#about" class="rounded-lg px-3 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">About</a>
                    <a href="{{ route('home') }}#services" class="rounded-lg px-3 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Services</a>
                    <a href="{{ route('home') }}#industries" class="rounded-lg px-3 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Industries</a>
                    <a href="{{ route('home') }}#contact" class="rounded-lg px-3 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Contact</a>
                    <a href="/egjewel/login" class="mt-1 rounded-lg bg-slate-900 px-3 py-2.5 text-center text-sm font-semibold text-white">CRM / ERP login</a>
                </div>
            </div>
        </details>
    </div>
</nav>
