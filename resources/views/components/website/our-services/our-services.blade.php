<section id="services" class="bg-white py-24">

    <div class="mx-auto max-w-7xl px-6 lg:px-8">

        {{-- Heading --}}
        <div class="mx-auto max-w-3xl text-center">

            <span class="inline-flex rounded-full bg-cyan-100 px-4 py-2 text-sm font-semibold text-cyan-700">

                Our Services

            </span>

            <h2 class="mt-6 text-4xl font-extrabold tracking-tight text-slate-900 lg:text-5xl">

                Enterprise Digital Transformation Solutions

            </h2>

            <p class="mt-6 text-lg leading-8 text-slate-600">

                Unified Core India delivers comprehensive document digitization,
                intelligent automation, enterprise content management, and secure
                digital transformation solutions designed for governments,
                public sector organizations, and modern enterprises.

            </p>

        </div>

        {{-- Cards --}}
        <div class="mt-20 grid gap-8 md:grid-cols-2 xl:grid-cols-4">

            @php
                $services = [
                    [
                        'icon' => '📄',
                        'title' => 'Document Digitization',
                        'description' =>
                            'Convert physical records into searchable, structured, and secure digital archives.',
                    ],
                    [
                        'icon' => '🖨️',
                        'title' => 'Document Scanning',
                        'description' =>
                            'High-speed scanning with barcode recognition, indexing, and quality validation.',
                    ],
                    [
                        'icon' => '🤖',
                        'title' => 'AI Powered OCR',
                        'description' =>
                            'Extract text and business data from documents using intelligent OCR workflows.',
                    ],
                    [
                        'icon' => '🗂️',
                        'title' => 'Enterprise DMS',
                        'description' =>
                            'Centralized document storage, retrieval, version control, and workflow automation.',
                    ],
                    [
                        'icon' => '📊',
                        'title' => 'Data Capture & Indexing',
                        'description' => 'Metadata extraction and structured indexing for fast document retrieval.',
                    ],
                    [
                        'icon' => '☁️',
                        'title' => 'Cloud Infrastructure',
                        'description' =>
                            'Secure deployment of scalable document repositories and enterprise applications.',
                    ],
                    [
                        'icon' => '🔐',
                        'title' => 'Cyber Security',
                        'description' =>
                            'Protect sensitive information with secure infrastructure and access controls.',
                    ],
                    [
                        'icon' => '🚀',
                        'title' => 'Digital Transformation',
                        'description' =>
                            'Modernize business processes with automation, AI, and enterprise technologies.',
                    ],
                ];
            @endphp

            @foreach ($services as $service)
                <div
                    class="group rounded-3xl border border-slate-200 bg-white p-8 shadow-sm transition-all duration-300 hover:-translate-y-2 hover:border-cyan-500 hover:shadow-xl">

                    <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-cyan-100 text-3xl">

                        {{ $service['icon'] }}

                    </div>

                    <h3 class="mt-6 text-xl font-bold text-slate-900">

                        {{ $service['title'] }}

                    </h3>

                    <p class="mt-4 leading-7 text-slate-600">

                        {{ $service['description'] }}

                    </p>

                    <a href="#contact"
                        class="mt-6 inline-flex items-center font-semibold text-cyan-600 transition group-hover:translate-x-1">

                        Learn More →

                    </a>

                </div>
            @endforeach

        </div>

        {{-- CTA --}}
        <div class="mt-20 text-center">

            <a href="#contact"
                class="inline-flex rounded-xl bg-cyan-600 px-8 py-4 font-semibold text-white shadow-lg transition hover:bg-cyan-700">

                Request a Consultation

            </a>

        </div>

    </div>

</section>
