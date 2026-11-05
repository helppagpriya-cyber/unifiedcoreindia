<footer class="bg-slate-950 text-white">
    <div class="mx-auto grid max-w-7xl gap-10 px-4 py-12 sm:px-6 md:grid-cols-2 lg:grid-cols-4 lg:px-8">
        <div class="lg:col-span-2">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-3 text-lg font-extrabold">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-cyan-500 text-sm font-black text-slate-950">UC</span>
                Unified Core India
            </a>
            <p class="mt-4 max-w-md leading-7 text-slate-400">Document digitization, records management, and digital transformation solutions for government and enterprise teams.</p>
        </div>
        <div>
            <h2 class="text-sm font-bold uppercase tracking-wider text-slate-300">Explore</h2>
            <div class="mt-4 grid gap-3 text-sm text-slate-400">
                <a class="hover:text-white" href="{{ route('home') }}#about">About us</a>
                <a class="hover:text-white" href="{{ route('home') }}#services">Services</a>
                <a class="hover:text-white" href="{{ route('home') }}#industries">Industries</a>
                <a class="hover:text-white" href="{{ route('home') }}#contact">Contact</a>
            </div>
        </div>
        <div>
            <h2 class="text-sm font-bold uppercase tracking-wider text-slate-300">Team access</h2>
            <div class="mt-4 grid gap-3 text-sm text-slate-400">
                <a class="hover:text-white" href="/egjewel/login">CRM / ERP login</a>
                <a class="hover:text-white" href="/admin/login">Administration</a>
            </div>
        </div>
    </div>
    <div class="border-t border-white/10">
        <div class="mx-auto flex max-w-7xl flex-col gap-2 px-4 py-5 text-xs text-slate-500 sm:px-6 md:flex-row md:items-center md:justify-between lg:px-8">
            <span>© {{ now()->year }} Unified Core India Pvt. Ltd. All rights reserved.</span>
            <a href="#top" class="font-semibold text-slate-400 hover:text-white">Back to top ↑</a>
        </div>
    </div>
</footer>
