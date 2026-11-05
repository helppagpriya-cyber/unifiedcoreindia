<section id="contact" class="scroll-mt-20 bg-slate-950 py-20 text-white sm:py-24">
    <div class="mx-auto grid max-w-7xl gap-12 px-4 sm:px-6 lg:grid-cols-[0.8fr_1.2fr] lg:px-8">
        <div>
            <span class="inline-flex rounded-full border border-cyan-400/20 bg-cyan-400/10 px-4 py-2 text-sm font-semibold text-cyan-300">Start a conversation</span>
            <h2 class="mt-6 text-3xl font-extrabold tracking-tight sm:text-4xl">Tell us what you’re looking to improve.</h2>
            <p class="mt-5 max-w-xl leading-7 text-slate-300">Share a little about your organization and requirements. Your enquiry will be added to our CRM so our team can follow up.</p>
            <div class="mt-8 flex items-start gap-3 rounded-2xl border border-white/10 bg-white/5 p-4 text-sm leading-6 text-slate-300">
                <x-heroicon-o-lock-closed class="mt-0.5 h-5 w-5 shrink-0 text-cyan-300" />
                <p>Contact details are used to respond to this enquiry and manage the follow-up.</p>
            </div>
        </div>

        <div class="rounded-3xl border border-white/10 bg-white p-5 text-slate-900 shadow-2xl sm:p-8">
            @if (session('contact_success'))
                <div role="status" class="mb-6 flex gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800">
                    <x-heroicon-o-check-circle class="h-5 w-5 shrink-0" />
                    {{ session('contact_success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('website.contact.store') }}" class="grid gap-5 sm:grid-cols-2">
                @csrf
                <div class="absolute -left-[10000px] top-auto h-px w-px overflow-hidden" aria-hidden="true">
                    <label for="website">Leave this field empty</label>
                    <input id="website" name="website" type="text" tabindex="-1" autocomplete="off">
                </div>

                <div>
                    <label for="company_name" class="text-sm font-semibold text-slate-700">Company / organization <span class="text-rose-600">*</span></label>
                    <input id="company_name" name="company_name" value="{{ old('company_name') }}" required maxlength="255" autocomplete="organization" class="mt-2 w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-cyan-600 focus:ring-cyan-600" />
                    @error('company_name') <p class="mt-1.5 text-xs text-rose-700">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="contact_person" class="text-sm font-semibold text-slate-700">Your name <span class="text-rose-600">*</span></label>
                    <input id="contact_person" name="contact_person" value="{{ old('contact_person') }}" required maxlength="255" autocomplete="name" class="mt-2 w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-cyan-600 focus:ring-cyan-600" />
                    @error('contact_person') <p class="mt-1.5 text-xs text-rose-700">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="email" class="text-sm font-semibold text-slate-700">Work email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" maxlength="255" autocomplete="email" class="mt-2 w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-cyan-600 focus:ring-cyan-600" />
                    @error('email') <p class="mt-1.5 text-xs text-rose-700">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="mobile" class="text-sm font-semibold text-slate-700">Phone number</label>
                    <input id="mobile" name="mobile" type="tel" value="{{ old('mobile') }}" maxlength="20" autocomplete="tel" class="mt-2 w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-cyan-600 focus:ring-cyan-600" />
                    @error('mobile') <p class="mt-1.5 text-xs text-rose-700">{{ $message }}</p> @enderror
                </div>
                <div class="sm:col-span-2">
                    <label for="product_interested" class="text-sm font-semibold text-slate-700">What can we help with?</label>
                    <select id="product_interested" name="product_interested" class="mt-2 w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-cyan-600 focus:ring-cyan-600">
                        <option value="">Select a service (optional)</option>
                        @foreach (['Document Digitization', 'Document Scanning', 'AI Powered OCR', 'Enterprise DMS', 'Data Capture & Indexing', 'Cloud Infrastructure', 'Cyber Security', 'Digital Transformation', 'Other'] as $service)
                            <option value="{{ $service }}" @selected(old('product_interested') === $service)>{{ $service }}</option>
                        @endforeach
                    </select>
                    @error('product_interested') <p class="mt-1.5 text-xs text-rose-700">{{ $message }}</p> @enderror
                </div>
                <div class="sm:col-span-2">
                    <label for="requirements" class="text-sm font-semibold text-slate-700">Tell us about your requirements <span class="text-rose-600">*</span></label>
                    <textarea id="requirements" name="requirements" rows="4" required maxlength="5000" class="mt-2 w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-cyan-600 focus:ring-cyan-600">{{ old('requirements') }}</textarea>
                    @error('requirements') <p class="mt-1.5 text-xs text-rose-700">{{ $message }}</p> @enderror
                </div>
                <div class="sm:col-span-2">
                    <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-cyan-700 px-5 py-3.5 text-sm font-bold text-white shadow-sm transition hover:bg-cyan-800 focus:outline-none focus:ring-2 focus:ring-cyan-600 focus:ring-offset-2 sm:w-auto">
                        Send enquiry <x-heroicon-o-arrow-right class="h-4 w-4" />
                    </button>
                    <p class="mt-3 text-xs text-slate-500">Provide at least one way for us to contact you: email or phone.</p>
                    @error('contact_method') <p class="mt-1.5 text-xs text-rose-700">{{ $message }}</p> @enderror
                </div>
            </form>
        </div>
    </div>
</section>
