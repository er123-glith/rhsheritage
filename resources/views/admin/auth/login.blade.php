<!DOCTYPE html>
<html lang="id" class="h-full bg-canvas-base">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk Panel Owner RHS HERITAGE</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300..900;1,300..900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex items-center justify-center p-4 font-body text-neutral-700 bg-canvas-base antialiased">
    <div class="w-full max-w-md">
        <!-- Logo & Header -->
        <div class="text-center mb-8" data-aos="zoom-in" data-aos-duration="600">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-primary-700 text-gold-400 font-display text-2xl font-bold shadow-2 mb-4 border-2 border-gold-500">
                RHS
            </div>
            <h1 class="font-display text-2xl sm:text-3xl text-primary-700 font-bold">RHS HERITAGE</h1>
            <p class="font-body text-xs uppercase tracking-widest text-gold-700 font-semibold mt-1">Panel Masuk Owner</p>
        </div>

        <!-- Login Card -->
        <div class="bg-white rounded-xl shadow-2 p-6 sm:p-8 border border-neutral-200" data-aos="fade-up" data-aos-delay="150" data-aos-duration="650">
            @if (session('status'))
                <script>
                    document.addEventListener('DOMContentLoaded', () => {
                        if (window.Swal) {
                            window.Swal.fire({
                                icon: 'info',
                                title: 'Informasi',
                                text: @json(session('status')),
                                confirmButtonColor: '#0F3D2E',
                                customClass: { popup: 'rhs-swal-popup', title: 'rhs-swal-title', confirmButton: 'rhs-swal-confirm' }
                            });
                        }
                    });
                </script>
            @endif

            @if ($errors->any())
                <script>
                    document.addEventListener('DOMContentLoaded', () => {
                        if (window.Swal) {
                            window.Swal.fire({
                                icon: 'error',
                                title: 'Gagal Masuk',
                                text: @json($errors->first()),
                                confirmButtonColor: '#C0392B',
                                customClass: { popup: 'rhs-swal-popup', title: 'rhs-swal-title', confirmButton: 'rhs-swal-confirm' }
                            });
                        }
                    });
                </script>
            @endif

            <form method="POST" action="{{ route('admin.login') }}" class="space-y-5">
                @csrf

                <x-form.input
                    name="email"
                    type="email"
                    label="Email Owner"
                    :value="old('email')"
                    required
                    placeholder="nama@rhsheritage.com"
                    autocomplete="email"
                    autofocus
                />

                <x-form.input
                    name="password"
                    type="password"
                    label="Kata Sandi"
                    required
                    placeholder="***********"
                    autocomplete="current-password"
                />

                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center gap-2.5 cursor-pointer">
                        <input
                            type="checkbox"
                            name="remember"
                            class="w-4 h-4 rounded border-neutral-300 text-primary-700 focus:ring-primary-500 cursor-pointer"
                        />
                        <span class="font-body text-xs text-neutral-600">Ingat Saya</span>
                    </label>
                </div>

                <x-button type="submit" variant="primary" size="lg" class="w-full mt-2">
                    <span>Masuk ke Dashboard</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </x-button>
            </form>
        </div>

        <!-- Footer Notice -->
        <p class="text-center font-body text-xs text-neutral-500 mt-6" data-aos="fade-up" data-aos-delay="250">
            Sistem Informasi E-Commerce RHS HERITAGE &copy; {{ date('Y') }}
        </p>
    </div>
</body>
</html>
