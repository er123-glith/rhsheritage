<!DOCTYPE html>
<html lang="id" class="h-full bg-canvas-base">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Autentikasi' }} — RHS HERITAGE</title>

    <!-- Favicon RHS Heritage Resmi -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Plus+Jakarta+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full flex flex-col justify-center py-10 sm:px-6 lg:px-8 font-body text-neutral-700 bg-canvas-base antialiased">
    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
        <!-- Logo RHS -->
        <a href="{{ route('home') }}" class="inline-flex flex-col items-center gap-2 group">
            <div class="w-16 h-16 rounded-full bg-primary-700 text-gold-400 font-display font-bold text-2xl flex items-center justify-center border-2 border-gold-500 shadow-2 group-hover:scale-105 transition-transform">
                RHS
            </div>
            <div>
                <span class="font-display text-2xl font-bold tracking-wide text-primary-700 block leading-tight">
                    RHS HERITAGE
                </span>
                <span class="font-body text-[10px] uppercase tracking-widest text-gold-700 font-semibold block">
                    Kriya Tradisi Keluarga Sejak 2004
                </span>
            </div>
        </a>
    </div>

    <div class="mt-6 sm:mx-auto sm:w-full sm:max-w-md px-4 sm:px-0">
        <div class="bg-white rounded-2xl shadow-2 p-6 sm:p-8 border border-neutral-200">
            {{ $slot }}
        </div>

        <div class="mt-6 text-center text-xs text-neutral-500">
            <a href="{{ route('home') }}" class="hover:text-primary-700 transition flex items-center justify-center gap-1.5 font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                <span>Kembali ke Beranda Website</span>
            </a>
        </div>
    </div>
</body>
</html>
