@props(['industry'])

<div
    class="group relative overflow-hidden rounded-3xl border border-slate-200 bg-gradient-to-br from-white to-slate-50 p-8 shadow-sm transition-all duration-500 hover:-translate-y-2 hover:border-cyan-500 hover:shadow-2xl">

    {{-- Top Gradient --}}
    <div
        class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-cyan-500 to-blue-600 scale-x-0 origin-left transition-transform duration-500 group-hover:scale-x-100">
    </div>

    {{-- Icon --}}
    <div
        class="flex h-16 w-16 items-center justify-center rounded-2xl bg-cyan-100 text-cyan-600 transition-all duration-300 group-hover:scale-110 group-hover:rotate-6 group-hover:bg-cyan-600 group-hover:text-white">

        @switch($industry['icon'])
            @case('building-library')
                <x-heroicon-o-building-library class="h-8 w-8" />
            @break

            @case('banknotes')
                <x-heroicon-o-banknotes class="h-8 w-8" />
            @break

            @case('heart')
                <x-heroicon-o-heart class="h-8 w-8" />
            @break

            @case('academic-cap')
                <x-heroicon-o-academic-cap class="h-8 w-8" />
            @break

            @case('scale')
                <x-heroicon-o-scale class="h-8 w-8" />
            @break

            @case('cog-6-tooth')
                <x-heroicon-o-cog-6-tooth class="h-8 w-8" />
            @break

            @case('briefcase')
                <x-heroicon-o-briefcase class="h-8 w-8" />
            @break

            @case('truck')
                <x-heroicon-o-truck class="h-8 w-8" />
            @break
        @endswitch

    </div>

    <h3 class="mt-6 text-2xl font-bold text-slate-900">

        {{ $industry['title'] }}

    </h3>

    <p class="mt-4 leading-7 text-slate-600">

        {{ $industry['description'] }}

    </p>

    <ul class="mt-6 space-y-3">

        @foreach ($industry['features'] as $feature)
            <li class="flex items-center gap-3">

                <x-heroicon-o-check-circle class="h-5 w-5 text-cyan-600" />

                <span class="text-slate-700">

                    {{ $feature }}

                </span>

            </li>
        @endforeach

    </ul>

    <a href="#contact"
        class="mt-8 inline-flex items-center gap-2 font-semibold text-cyan-600 transition-all duration-300 group-hover:gap-4">

        View Solutions

        <x-heroicon-o-arrow-right class="h-5 w-5" />

    </a>

</div>
