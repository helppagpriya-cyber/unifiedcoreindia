<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $title ?? 'Unified Core India Pvt. Ltd.' }}</title>

    <meta name="description"
        content="{{ $description ?? 'Unified Core India Pvt. Ltd. - Document Digitization, Records Management & Digital Transformation Solutions.' }}">
    <meta name="robots" content="index, follow">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $title ?? 'Unified Core India Pvt. Ltd.' }}">
    <meta property="og:description" content="{{ $description ?? 'Document digitization, records management, and digital transformation solutions.' }}">
    <meta property="og:url" content="{{ url()->current() }}">

    <meta name="theme-color" content="#0F4C81">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles

    @stack('styles')
</head>

<body class="min-h-screen bg-white text-slate-800 antialiased selection:bg-cyan-200 selection:text-slate-900">

    @include('website.navbar.navbar')

    <main>
        {{ $slot }}
    </main>

    @include('website.footer.footer')

    @livewireScripts

    @stack('scripts')

</body>

</html>
