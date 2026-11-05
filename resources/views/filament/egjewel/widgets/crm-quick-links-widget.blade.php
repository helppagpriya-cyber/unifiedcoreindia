<x-filament-widgets::widget>
    @php
        $metricTones = [
            'amber' => ['icon' => 'bg-amber-50 text-amber-700 ring-amber-100', 'value' => 'text-amber-950'],
            'sky' => ['icon' => 'bg-sky-50 text-sky-700 ring-sky-100', 'value' => 'text-sky-950'],
            'rose' => ['icon' => 'bg-rose-50 text-rose-700 ring-rose-100', 'value' => 'text-rose-950'],
            'emerald' => ['icon' => 'bg-emerald-50 text-emerald-700 ring-emerald-100', 'value' => 'text-emerald-950'],
            'violet' => ['icon' => 'bg-violet-50 text-violet-700 ring-violet-100', 'value' => 'text-violet-950'],
        ];
        $linkTones = [
            'indigo' => 'bg-indigo-50 text-indigo-700 group-hover:bg-indigo-100',
            'sky' => 'bg-sky-50 text-sky-700 group-hover:bg-sky-100',
            'emerald' => 'bg-emerald-50 text-emerald-700 group-hover:bg-emerald-100',
            'violet' => 'bg-violet-50 text-violet-700 group-hover:bg-violet-100',
        ];
        $icons = [
            'clock' => 'heroicon-o-clock',
            'user-group' => 'heroicon-o-user-group',
            'bolt' => 'heroicon-o-bolt',
            'check' => 'heroicon-o-check-circle',
            'plus' => 'heroicon-o-plus',
            'inbox' => 'heroicon-o-inbox',
            'building' => 'heroicon-o-building-office-2',
            'key' => 'heroicon-o-key',
        ];
    @endphp

    <div class="space-y-6">
        <section class="relative isolate overflow-hidden rounded-3xl bg-gradient-to-br from-slate-950 via-indigo-950 to-indigo-800 px-6 py-8 text-white shadow-xl shadow-indigo-950/10 sm:px-9 sm:py-10">
            <div class="absolute -right-12 -top-24 -z-10 h-80 w-80 rounded-full bg-sky-400/20 blur-3xl"></div>
            <div class="absolute -bottom-32 right-1/3 -z-10 h-64 w-64 rounded-full bg-violet-400/20 blur-3xl"></div>
            <div class="flex flex-col justify-between gap-7 lg:flex-row lg:items-end">
                <div class="max-w-2xl">
                    <div class="mb-4 inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-3 py-1.5 text-xs font-semibold uppercase tracking-[0.16em] text-indigo-100">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-300"></span>
                        EGJEWEL sales workspace
                    </div>
                    <h1 class="text-3xl font-semibold tracking-tight sm:text-4xl">Good day, {{ $firstName }}.</h1>
                    <p class="mt-3 max-w-xl text-sm leading-6 text-indigo-100 sm:text-base">Keep customer conversations moving. Your follow-ups, active opportunities, and daily actions are all in one place.</p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <div class="mr-1 hidden text-right sm:block">
                        <div class="text-xs font-medium uppercase tracking-wider text-indigo-200">Today</div>
                        <div class="mt-1 text-sm font-semibold text-white">{{ $todayLabel }}</div>
                    </div>
                    <a href="{{ \App\Filament\EGJEWEL\Resources\CrmLeads\CrmLeadResource::getUrl('create') }}" class="inline-flex items-center gap-2 rounded-xl bg-white px-4 py-3 text-sm font-semibold text-indigo-950 shadow-lg shadow-black/10 transition hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-indigo-900">
                        <x-heroicon-o-plus class="h-4 w-4" />
                        New lead
                    </a>
                    <a href="{{ \App\Filament\EGJEWEL\Resources\CrmLeads\CrmLeadResource::getUrl('index') }}" class="inline-flex items-center gap-2 rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm font-semibold text-white transition hover:bg-white/15 focus:outline-none focus:ring-2 focus:ring-white">
                        Open lead inbox
                        <x-heroicon-o-arrow-up-right class="h-4 w-4" />
                    </a>
                </div>
            </div>
        </section>

        <section class="grid gap-4 sm:grid-cols-2 2xl:grid-cols-5">
            @foreach ($metrics as $metric)
                <a href="{{ $metric['url'] }}" class="group rounded-2xl border border-gray-200/80 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-gray-300 hover:shadow-md dark:border-gray-700 dark:bg-gray-900">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $metric['label'] }}</div>
                            <div class="mt-3 text-3xl font-semibold tracking-tight {{ $metricTones[$metric['tone']]['value'] }} dark:text-white">{{ number_format($metric['value']) }}</div>
                        </div>
                        <span class="inline-flex h-11 w-11 items-center justify-center rounded-xl ring-1 {{ $metricTones[$metric['tone']]['icon'] }}">
                            <x-dynamic-component :component="$icons[$metric['icon']]" class="h-5 w-5" />
                        </span>
                    </div>
                    <div class="mt-4 flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                        <span>{{ $metric['hint'] }}</span>
                        <x-heroicon-o-arrow-right class="h-4 w-4 transition group-hover:translate-x-0.5" />
                    </div>
                </a>
            @endforeach
        </section>

        <section class="grid gap-6 xl:grid-cols-[1.35fr_0.85fr]">
            <div class="overflow-hidden rounded-2xl border border-gray-200/80 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
                <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4 dark:border-gray-800 sm:px-6">
                    <div>
                        <h2 class="text-base font-semibold text-gray-950 dark:text-white">Follow-ups needing attention</h2>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Your overdue and due customer conversations</p>
                    </div>
                    <a href="{{ \App\Filament\EGJEWEL\Resources\CrmLeads\CrmLeadResource::getUrl('index', ['tab' => 'due']) }}" class="text-sm font-semibold text-indigo-700 hover:text-indigo-900 dark:text-indigo-300">View all due</a>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse ($followUps as $lead)
                        <a href="{{ \App\Filament\EGJEWEL\Resources\CrmLeads\CrmLeadResource::getUrl('edit', ['record' => $lead]) }}" class="group flex items-center gap-4 px-5 py-4 transition hover:bg-gray-50 dark:hover:bg-gray-800/60 sm:px-6">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-indigo-50 text-sm font-semibold text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300">{{ str($lead->company_name)->substr(0, 1)->upper() }}</span>
                            <span class="min-w-0 flex-1">
                                <span class="flex flex-wrap items-center gap-2">
                                    <span class="truncate text-sm font-semibold text-gray-900 dark:text-white">{{ $lead->company_name }}</span>
                                    @if ($lead->status)
                                        <span class="rounded-full bg-gray-100 px-2 py-0.5 text-[11px] font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-300">{{ $lead->status->name }}</span>
                                    @endif
                                </span>
                                <span class="mt-1 block truncate text-xs text-gray-500 dark:text-gray-400">{{ $lead->contact_person ?: 'No contact name' }}{{ $lead->mobile ? ' · ' . $lead->mobile : '' }}</span>
                            </span>
                            <span class="hidden shrink-0 text-right sm:block">
                                <span class="block text-[11px] font-medium uppercase tracking-wide text-gray-400">Follow-up</span>
                                <span class="mt-1 block text-xs font-semibold {{ $lead->next_follow_up_at->isPast() ? 'text-rose-600' : 'text-gray-700 dark:text-gray-300' }}">{{ $lead->next_follow_up_at->format('d M, h:i A') }}</span>
                            </span>
                            <x-heroicon-o-chevron-right class="h-4 w-4 shrink-0 text-gray-300 transition group-hover:translate-x-0.5 group-hover:text-indigo-600" />
                        </a>
                    @empty
                        <div class="px-6 py-12 text-center">
                            <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 dark:bg-emerald-950 dark:text-emerald-300"><x-heroicon-o-check class="h-6 w-6" /></span>
                            <h3 class="mt-4 text-sm font-semibold text-gray-900 dark:text-white">You’re all caught up</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">No follow-ups are overdue right now.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200/80 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-900 sm:p-6">
                <div class="flex items-start justify-between">
                    <div>
                        <h2 class="text-base font-semibold text-gray-950 dark:text-white">My pipeline</h2>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Active leads by stage</p>
                    </div>
                    <span class="rounded-lg bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300">{{ number_format($pipelineTotal) }} leads</span>
                </div>
                <div class="mt-6 space-y-5">
                    @forelse ($pipeline as $stage)
                        @php $width = min(100, (int) round(($stage->leads_count / $pipelineTotal) * 100)); @endphp
                        <a href="{{ \App\Filament\EGJEWEL\Resources\CrmLeads\CrmLeadResource::getUrl('index', ['tab' => 'my-open', 'tableFilters[status_id][value]' => $stage->id]) }}" class="group block rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            <div class="mb-2 flex items-center justify-between gap-3 text-sm">
                                <span class="font-medium text-gray-700 dark:text-gray-300">{{ $stage->name }}</span>
                                <span class="font-semibold tabular-nums text-gray-950 dark:text-white">{{ number_format($stage->leads_count) }}</span>
                            </div>
                            <div class="h-2 overflow-hidden rounded-full bg-gray-100 dark:bg-gray-800">
                                <div class="h-full rounded-full bg-gradient-to-r from-indigo-600 to-sky-400 transition-all group-hover:from-sky-500 group-hover:to-cyan-400" style="width: {{ $width }}%"></div>
                            </div>
                        </a>
                    @empty
                        <p class="py-8 text-center text-sm text-gray-500">No active lead stages yet.</p>
                    @endforelse
                </div>
            </div>
        </section>

        <section>
            <div class="mb-3 flex items-end justify-between">
                <div>
                    <h2 class="text-base font-semibold text-gray-950 dark:text-white">Quick actions</h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Common tasks, one click away</p>
                </div>
            </div>
            <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                @foreach ($quickLinks as $link)
                    <a href="{{ $link['url'] }}" class="group flex items-center gap-3 rounded-2xl border border-gray-200/80 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:border-gray-700 dark:bg-gray-900">
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl {{ $linkTones[$link['tone']] }}">
                            <x-dynamic-component :component="$icons[$link['icon']]" class="h-5 w-5" />
                        </span>
                        <span class="min-w-0 flex-1">
                            <span class="block text-sm font-semibold text-gray-900 dark:text-white">{{ $link['title'] }}</span>
                            <span class="mt-0.5 block text-xs text-gray-500 dark:text-gray-400">{{ $link['description'] }}</span>
                        </span>
                        <x-heroicon-o-arrow-right class="h-4 w-4 text-gray-300 transition group-hover:translate-x-0.5 group-hover:text-indigo-600" />
                    </a>
                @endforeach
            </div>
        </section>
    </div>
</x-filament-widgets::widget>
