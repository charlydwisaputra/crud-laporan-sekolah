<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sipras Manusa — Sistem Laporan Kerusakan Sekolah')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        forest: '#1E3B2F',
                        sage: '#7F8C6A',
                        sand: '#C8B48A',
                        cream: '#F5F0E6',
                        clay: '#B5523B', // khusus untuk error / hapus
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    },
                },
            },
        }
    </script>

    <style type="text/tailwindcss">
        @layer base {
            body {
                background-color: #F5F0E6;
                background-image: radial-gradient(rgba(30, 59, 47, 0.12) 1px, transparent 1px);
                background-size: 22px 22px;
            }
        }

        @layer components {
            /* ===== KARTU BENTO ===== */
            .bento       { @apply rounded-[2rem] border border-forest/10 bg-white/60 p-6 shadow-sm; }
            .bento-dark  { @apply rounded-[2rem] bg-forest p-6 text-cream shadow-sm; }
            .bento-sage  { @apply rounded-[2rem] bg-sage p-6 text-cream shadow-sm; }
            .bento-sand  { @apply rounded-[2rem] bg-sand p-6 text-forest shadow-sm; }
            .bento-hover { @apply transition duration-200 hover:-translate-y-0.5 hover:shadow-md; }

            /* ===== TOMBOL ===== */
            .btn         { @apply inline-flex items-center justify-center gap-2 rounded-full px-5 py-2.5 text-sm font-semibold transition; }
            .btn-primary { @apply btn bg-forest text-cream hover:bg-forest/90; }
            .btn-sand    { @apply btn bg-sand text-forest hover:bg-sand/80; }
            .btn-ghost   { @apply btn border border-forest/20 text-forest hover:bg-forest/5; }
            .btn-danger  { @apply btn bg-clay/10 text-clay border border-clay/30 hover:bg-clay/20; }

            /* ===== FORM ===== */
            .label-bento { @apply mb-1.5 block text-sm font-semibold text-forest; }
            .input-bento { @apply w-full rounded-2xl border border-forest/15 bg-white/70 px-4 py-3 text-sm text-forest placeholder:text-forest/40 focus:border-forest focus:outline-none focus:ring-2 focus:ring-sage/40; }

            /* ===== LABEL KECIL ===== */
            .chip        { @apply inline-flex items-center rounded-full border border-current px-3 py-0.5 text-[11px] font-semibold uppercase tracking-wider; }
            .badge       { @apply inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold; }
        }
    </style>

    @stack('head')
</head>
<body class="min-h-screen font-sans text-forest antialiased">
    @include('layouts.navigation')

    <div class="mx-auto max-w-6xl px-4 pt-6">
        @if(session('success'))
            <div class="mb-4 flex items-start gap-3 rounded-[1.5rem] bg-sage px-5 py-4 text-sm font-medium text-cream shadow-sm">
                <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-cream text-xs font-bold text-forest">✓</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 flex items-start gap-3 rounded-[1.5rem] border border-clay/30 bg-clay/10 px-5 py-4 text-sm font-medium text-clay shadow-sm">
                <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-clay text-xs font-bold text-cream">!</span>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 rounded-[1.5rem] border border-clay/30 bg-clay/10 px-5 py-4 text-sm text-clay shadow-sm">
                <p class="font-semibold">Periksa kembali input:</p>
                <ul class="mt-1 list-disc pl-5 text-xs">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <main class="mx-auto max-w-6xl px-4 py-6">
        @yield('content')
    </main>

    <footer class="mx-auto max-w-6xl px-4 pb-8">
        <div class="bento-dark flex flex-wrap items-center justify-between gap-4 !rounded-[1.75rem] !px-8 !py-6">
            <div>
                <p class="text-base font-extrabold">Sipras <span class="text-sand">Manusa</span></p>
                <p class="mt-0.5 text-xs text-cream/60">Sistem pelaporan sarana &amp; prasarana sekolah.</p>
            </div>
            <div class="flex flex-wrap items-center gap-4 text-xs text-cream/70">
                <a href="{{ route('home') }}" class="hover:text-cream">Homepage</a>
                <a href="{{ route('galeri') }}" class="hover:text-cream">Galeri Perbaikan</a>
                <span>&copy; {{ date('Y') }} Sipras Manusa</span>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>