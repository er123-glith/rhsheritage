<!DOCTYPE html>
<html lang="id" class="h-full bg-canvas-base">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ukuran File Terlalu Besar — RHS HERITAGE</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex items-center justify-center p-4 font-body text-neutral-700 bg-canvas-base antialiased">
    <div class="max-w-md w-full bg-white rounded-2xl border border-neutral-200 shadow-2 p-8 text-center space-y-6">
        <div class="w-16 h-16 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center mx-auto">
            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
        </div>

        <div class="space-y-2">
            <span class="inline-block px-3 py-1 rounded-sm bg-red-100 text-red-800 font-body text-xs font-bold uppercase tracking-wider">
                Error 413 — Muatan Terlalu Besar
            </span>
            <h1 class="font-display text-xl sm:text-2xl font-bold text-primary-700">
                Ukuran Berkas Melebihi Batas Server
            </h1>
            <p class="font-body text-sm text-neutral-600 leading-relaxed">
                {{ $message ?? 'Ukuran berkas yang diunggah melebihi batas maksimal server (100 MB). Silakan kompres video atau pilih berkas yang lebih kecil, kemudian coba kembali.' }}
            </p>
        </div>

        <div class="pt-4 border-t border-neutral-100 flex flex-col sm:flex-row gap-3 justify-center">
            <button 
                onclick="window.history.back()" 
                type="button"
                class="w-full sm:w-auto px-6 py-2.5 rounded-lg bg-primary-700 hover:bg-primary-800 text-white font-body text-xs font-bold transition shadow-xs cursor-pointer"
            >
                ← Kembali ke Formulir
            </button>
            <a 
                href="{{ route('customer.dashboard') }}" 
                class="w-full sm:w-auto px-5 py-2.5 rounded-lg border border-neutral-300 text-xs font-semibold text-neutral-700 hover:bg-neutral-50 transition inline-flex items-center justify-center"
            >
                Dashboard Pelanggan
            </a>
        </div>
    </div>
</body>
</html>

