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

        <details class="group relative z-[60] shrink-0">
            <summary class="flex cursor-pointer list-none items-center gap-2 rounded-xl bg-gradient-to-r from-slate-900 to-slate-800 px-3 py-2.5 text-sm font-semibold text-white shadow-md shadow-slate-900/10 transition hover:-translate-y-0.5 hover:from-cyan-900 hover:to-slate-900 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2 sm:px-4">
                <x-heroicon-o-user-circle class="h-5 w-5 text-cyan-300" />
                <span>@auth Account @endauth @guest Login @endguest</span>
                <x-heroicon-o-chevron-down class="h-4 w-4 transition group-open:rotate-180" />
            </summary>
            <div class="absolute right-0 top-[calc(100%+0.65rem)] z-[60] w-72 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl shadow-slate-900/15">
                @auth
                    <div class="border-b border-slate-100 bg-slate-50 px-4 py-3"><p class="text-xs font-medium uppercase tracking-wider text-slate-400">Signed in as</p><p class="mt-1 truncate text-sm font-semibold text-slate-900">{{ auth()->user()->email }}</p></div>
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-slate-700 hover:bg-cyan-50 hover:text-cyan-900"><x-heroicon-o-user class="h-5 w-5 text-slate-400" />My Profile</a>
                    <form method="POST" action="{{ route('logout') }}">@csrf<button class="flex w-full items-center gap-3 px-4 py-3 text-left text-sm font-medium text-slate-700 hover:bg-slate-50"><x-heroicon-o-arrow-right-on-rectangle class="h-5 w-5 text-slate-400" />Sign out</button></form>
                @else
                    <div class="p-3"><a href="{{ route('register') }}" class="flex items-center justify-between rounded-xl bg-cyan-700 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-cyan-800">Create an account <x-heroicon-o-arrow-right class="h-4 w-4" /></a></div>
                    <div class="border-t border-slate-100 px-4 pb-2 pt-3"><p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Sign in to your workspace</p></div>
                    <a href="/admin/login" class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-slate-700 hover:bg-slate-50"><span class="flex h-9 w-9 items-center justify-center rounded-lg bg-amber-50 text-amber-700"><x-heroicon-o-shield-check class="h-5 w-5" /></span><span><span class="block font-semibold">Admin Login</span><span class="block text-xs text-slate-500">Manage the platform</span></span></a>
                    <a href="/egjewel/login" class="mb-2 flex items-center gap-3 px-4 py-3 text-sm font-medium text-slate-700 hover:bg-slate-50"><span class="flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-50 text-indigo-700"><x-heroicon-o-building-office-2 class="h-5 w-5" /></span><span><span class="block font-semibold">EGJewel ERP Login</span><span class="block text-xs text-slate-500">Open your ERP workspace</span></span></a>
                @endauth
            </div>
        </details>

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
                </div>
            </div>
        </details>
    </div>
</nav>
