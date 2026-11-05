<div class="space-y-4">
    <div class="flex items-center justify-between gap-3 text-sm">
        <span class="text-gray-500 dark:text-gray-400">Review individual rows, update invalid values, and compare duplicate matches before importing.</span>
        <span class="shrink-0 rounded-full bg-indigo-50 px-3 py-1 font-semibold text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300">{{ number_format($summary['included'] ?? 0) }} selected</span>
    </div>
    @php
        $cards = [
            ['key' => 'total', 'label' => 'All rows', 'tone' => 'bg-gray-50 text-gray-700 dark:bg-gray-800 dark:text-gray-200'],
            ['key' => 'ready', 'label' => 'Ready', 'tone' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300'],
            ['key' => 'invalid', 'label' => 'Needs correction', 'tone' => 'bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-300'],
            ['key' => 'duplicates', 'label' => 'Exact duplicates', 'tone' => 'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300'],
            ['key' => 'possible_duplicates', 'label' => 'Possible duplicates', 'tone' => 'bg-sky-50 text-sky-700 dark:bg-sky-950/40 dark:text-sky-300'],
            ['key' => 'included', 'label' => 'Selected to import', 'tone' => 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/40 dark:text-indigo-300'],
        ];
        $filters = [
            'all' => ['All rows', 'total'],
            'ready' => ['Ready', 'ready'],
            'invalid' => ['Needs correction', 'invalid'],
            'duplicate' => ['Exact duplicates', 'duplicates'],
            'possible_duplicate' => ['Possible duplicates', 'possible_duplicates'],
            'selected' => ['Selected to import', 'included'],
        ];
    @endphp

    <div class="grid gap-2 sm:grid-cols-2 xl:grid-cols-6">
        @foreach ($cards as $card)
            <div class="rounded-xl px-3 py-2.5 {{ $card['tone'] }}">
                <div class="text-[11px] font-semibold uppercase tracking-wide opacity-75">{{ $card['label'] }}</div>
                <div class="mt-1 text-xl font-semibold tabular-nums">{{ number_format($summary[$card['key']] ?? 0) }}</div>
            </div>
        @endforeach
    </div>

    <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
        <div class="flex gap-1.5 overflow-x-auto pb-1">
            @foreach ($filters as $filter => [$label, $countKey])
                <button type="button" wire:click="$set('statusFilter', '{{ $filter }}')" class="inline-flex shrink-0 items-center gap-1.5 rounded-full px-3 py-1.5 text-xs font-semibold transition {{ $statusFilter === $filter ? 'bg-indigo-700 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700' }}">
                    {{ $label }}
                    <span class="tabular-nums opacity-75">{{ number_format($summary[$countKey] ?? 0) }}</span>
                </button>
            @endforeach
        </div>

        <label class="relative block w-full lg:max-w-sm">
            <x-heroicon-o-magnifying-glass class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
            <input type="search" wire:model.live.debounce.300ms="search" placeholder="Company, phone, email, GST or issue…" class="w-full rounded-xl border-gray-300 bg-white py-2 pl-9 pr-3 text-sm shadow-sm placeholder:text-gray-400 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-950 dark:text-white" />
        </label>
    </div>

    <div class="max-h-[60vh] overflow-y-auto rounded-xl border border-gray-200 dark:border-gray-700">
        {{ $this->table }}
    </div>

    <div class="flex items-start gap-2 rounded-xl bg-indigo-50 px-3.5 py-3 text-xs leading-5 text-indigo-900 dark:bg-indigo-950/50 dark:text-indigo-200">
        <x-heroicon-o-information-circle class="mt-0.5 h-4 w-4 shrink-0" />
        <p>Ready rows are selected by default. Correct invalid rows using <strong>Review / fix</strong>. Possible duplicates can be included individually; exact duplicates remain excluded unless you correct their matching values.</p>
    </div>
</div>
