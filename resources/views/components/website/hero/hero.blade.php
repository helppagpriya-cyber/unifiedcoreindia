<section class="relative overflow-hidden bg-slate-950 text-white min-h-screen flex items-center">

    {{-- Background --}}
    <div class="absolute inset-0">

        <div class="absolute inset-0 bg-gradient-to-br from-slate-950 via-blue-950 to-cyan-900">
        </div>

        <div class="absolute top-0 left-0 h-96 w-96 rounded-full bg-cyan-500/10 blur-3xl">
        </div>

        <div class="absolute bottom-0 right-0 h-[30rem] w-[30rem] rounded-full bg-blue-600/10 blur-3xl">
        </div>

        <div class="absolute inset-0 opacity-10"
            style="
                background-image:
                linear-gradient(rgba(255,255,255,.08) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.08) 1px, transparent 1px);
                background-size:60px 60px;
            ">
        </div>

    </div>

    <div class="relative z-10 mx-auto w-full max-w-7xl px-6 lg:px-8">

        <div class="grid lg:grid-cols-2 gap-16 items-center">

            {{-- LEFT --}}
            <div>

                <span
                    class="inline-flex items-center rounded-full border border-cyan-400/30 bg-cyan-500/10 px-4 py-2 text-sm font-medium text-cyan-300 mt-4">

                    Digital Transformation Partner

                </span>

                <h1 class="mt-8 text-5xl md:text-6xl xl:text-7xl font-black leading-tight tracking-tight">

                    DIGITIZE.
                    <br>

                    <span class="text-cyan-400">

                        SECURE.

                    </span>

                    <br>

                    INNOVATE.

                </h1>

                <p class="mt-8 text-lg leading-8 text-slate-300 max-w-2xl">

                    Unified Core India delivers enterprise-grade
                    Document Digitization,
                    AI Powered OCR,
                    Enterprise Content Management,
                    Cloud Infrastructure,
                    Cyber Security
                    and Digital Transformation solutions for Government,
                    PSUs and Corporate organizations.

                </p>

                <div class="mt-10 flex flex-wrap gap-4">

                    <a href="#services"
                        class="rounded-xl bg-cyan-500 px-7 py-4 font-semibold text-slate-950 transition hover:scale-105 hover:bg-cyan-400">

                        Explore Services

                    </a>

                    <a href="#contact"
                        class="rounded-xl border border-white/20 px-7 py-4 font-semibold backdrop-blur transition hover:bg-white/10">

                        Request Consultation

                    </a>

                </div>

                <div class="mt-14 grid grid-cols-2 md:grid-cols-4 gap-5">

                    <div class="rounded-2xl border border-white/10 bg-white/5 p-5 backdrop-blur">

                        <div class="text-3xl font-black text-cyan-400">

                            500+

                        </div>

                        <div class="mt-2 text-sm text-slate-300">

                            Projects Delivered

                        </div>

                    </div>

                    <div class="rounded-2xl border border-white/10 bg-white/5 p-5 backdrop-blur">

                        <div class="text-3xl font-black text-cyan-400">

                            10M+

                        </div>

                        <div class="mt-2 text-sm text-slate-300">

                            Pages Digitized

                        </div>

                    </div>

                    <div class="rounded-2xl border border-white/10 bg-white/5 p-5 backdrop-blur">

                        <div class="text-3xl font-black text-cyan-400">

                            99.9%

                        </div>

                        <div class="mt-2 text-sm text-slate-300">

                            Accuracy

                        </div>

                    </div>

                    <div class="rounded-2xl border border-white/10 bg-white/5 p-5 backdrop-blur">

                        <div class="text-3xl font-black text-cyan-400">

                            24×7

                        </div>

                        <div class="mt-2 text-sm text-slate-300">

                            Support

                        </div>

                    </div>

                </div>

            </div>

            {{-- RIGHT --}}
            <div class="relative">

                <div class="overflow-hidden rounded-3xl border border-white/10 bg-white/5 shadow-2xl backdrop-blur">

                    {{-- Replace later with actual hero image --}}
                    <img src="{{ asset('images/website/hero/building.jpg') }}" alt="Unified Core India"
                        class="h-[650px] w-full object-cover">

                </div>

                {{-- Floating Card --}}
                <div
                    class="absolute -left-10 top-10 hidden lg:block rounded-2xl border border-white/10 bg-slate-900/80 p-6 backdrop-blur">

                    <div class="text-cyan-400 font-bold">

                        AI Powered OCR

                    </div>

                    <div class="mt-2 text-sm text-slate-300">

                        Intelligent Document Processing

                    </div>

                </div>

                {{-- Floating Card --}}
                <div
                    class="absolute -right-8 bottom-24 hidden lg:block rounded-2xl border border-white/10 bg-slate-900/80 p-6 backdrop-blur">

                    <div class="text-4xl font-black text-cyan-400">

                        10M+

                    </div>

                    <div class="text-sm text-slate-300">

                        Pages Processed

                    </div>

                </div>

                {{-- Floating Card --}}
                <div
                    class="absolute left-10 bottom-5 hidden lg:block rounded-2xl border border-white/10 bg-slate-900/80 p-6 backdrop-blur">

                    <div class="text-green-400 font-bold">

                        ISO Ready

                    </div>

                    <div class="text-sm text-slate-300">

                        Secure & Compliant

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>
