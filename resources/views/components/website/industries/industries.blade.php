<section id="industries" class="relative overflow-hidden bg-slate-50 py-24">

    <div class="mx-auto max-w-3xl text-center">

        <span class="inline-flex rounded-full bg-cyan-100 px-4 py-2 text-sm font-semibold text-cyan-700">

            Industries We Serve

        </span>

        <h2 class="mt-6 text-4xl font-extrabold tracking-tight text-slate-900 lg:text-5xl">

            Enterprise Digital Transformation Solutions for Every Industry

        </h2>

        <p class="mt-6 text-lg leading-8 text-slate-600">

            Unified Core India delivers comprehensive document digitization, intelligent automation, enterprise content
            management, and secure digital transformation solutions designed for governments, public sector
            organizations, and modern enterprises across diverse industries.

        </p>

    </div>

    {{-- Decorative Background --}}
    <div class="absolute inset-0">

        <div class="absolute left-0 top-0 h-96 w-96 rounded-full bg-cyan-500/5 blur-3xl"></div>

        <div class="absolute bottom-0 right-0 h-[28rem] w-[28rem] rounded-full bg-blue-600/5 blur-3xl"></div>

    </div>

    <div class="relative mx-auto max-w-7xl px-6 lg:px-8">



        <div class="mt-20 grid gap-8 md:grid-cols-2 xl:grid-cols-4">

            @foreach (config('website.industries') as $industry)
                <x-website.industry-card :industry="$industry" />
            @endforeach

        </div>

    </div>

</section>
