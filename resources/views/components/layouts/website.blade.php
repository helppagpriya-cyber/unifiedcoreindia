<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $title ?? 'Unified Core India Pvt. Ltd.' }}</title>

    <meta name="description"
        content="{{ $description ?? 'Unified Core India Pvt. Ltd. - Document Digitization, Records Management & Digital Transformation Solutions.' }}">

    <meta name="theme-color" content="#0F4C81">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles

    @stack('styles')
</head>

<body class="min-h-screen bg-slate-50 text-slate-800 antialiased">

    @include('website.navbar.navbar')

    <main>
        {{ $slot }}
    </main>

    @include('website.footer.footer')

    @livewireScripts

    @stack('scripts')

</body>

</html>
