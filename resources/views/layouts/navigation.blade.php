@php
    $link   = 'rounded-full px-4 py-2 text-sm font-medium transition';
    $active = 'bg-forest text-cream';
    $idle   = 'text-forest/70 hover:bg-forest/10 hover:text-forest';
@endphp

<header class="sticky top-0 z-50 px-4 pt-4">
    <nav class="mx-auto max-w-6xl rounded-[2rem] border border-forest/10 bg-cream/80 p-2 shadow-sm backdrop-blur-md">
        <div class="flex flex-wrap items-center justify-between gap-2">

            {{-- Logo --}}
            <a href="{{ route('home') }}" class="flex items-center gap-2 rounded-full py-1 pl-1 pr-4">
                <span class="flex h-10 w-10 items-center justify-center rounded-full bg-forest text-sm font-extrabold text-cream">S</span>
                <span class="text-base font-extrabold tracking-tight text-forest">
                    Sipras <span class="text-sage">Manusa</span>
                </span>
            </a>

            {{-- Tombol menu (mobile) --}}
            <button type="button" id="nav-toggle"
                    class="rounded-full border border-forest/15 px-4 py-2 text-sm font-semibold text-forest md:hidden">
                Menu
            </button>

            {{-- Isi menu --}}
            <div id="nav-menu"
                 class="hidden w-full flex-col gap-2 pt-2 md:flex md:w-auto md:flex-1 md:flex-row md:items-center md:justify-between md:pt-0">

                {{-- Link utama --}}
                <div class="flex flex-col gap-1 md:flex-row md:items-center">
                    <a href="{{ route('home') }}"
                       class="{{ $link }} {{ request()->routeIs('home') ? $active : $idle }}">
                        Homepage
                    </a>

                    <a href="{{ route('galeri') }}"
                       class="{{ $link }} {{ request()->routeIs('galeri') ? $active : $idle }}">
                        Galeri Perbaikan
                    </a>

                    @auth
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}"
                               class="{{ $link }} {{ request()->routeIs('admin.dashboard') ? $active : $idle }}">
                                Dashboard Admin
                            </a>
                        @endif

                        <a href="{{ route('reports.index') }}"
                           class="{{ $link }} {{ request()->routeIs('reports.index') || request()->routeIs('reports.show') || request()->routeIs('reports.edit') ? $active : $idle }}">
                            {{ auth()->user()->isPetugas() ? 'Tugas Saya' : 'Daftar Laporan' }}
                        </a>
                    @endauth
                </div>

                {{-- Aksi kanan --}}
                <div class="flex flex-col gap-2 md:flex-row md:items-center">
                    @auth
                        @if(auth()->user()->isSiswa())
                            <a href="{{ route('reports.create') }}" class="btn-sand">
                                + Lapor Kerusakan
                            </a>
                        @endif

                        <span class="flex items-center gap-2 rounded-full border border-forest/10 bg-white/60 py-1.5 pl-1.5 pr-4 text-xs text-forest">
                            <span class="flex h-7 w-7 items-center justify-center rounded-full bg-sage text-xs font-bold uppercase text-cream">
                                {{ mb_substr(auth()->user()->name, 0, 1) }}
                            </span>
                            <span class="font-semibold">{{ auth()->user()->name }}</span>
                            <span class="text-forest/50">· {{ auth()->user()->role }}</span>
                        </span>

                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="btn-danger w-full !px-4 !py-2 !text-xs md:w-auto">
                                Keluar
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="btn-ghost">Masuk</a>
                        <a href="{{ route('register') }}" class="btn-primary">Daftar Siswa</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>
</header>

<script>
    document.getElementById('nav-toggle')?.addEventListener('click', function () {
        document.getElementById('nav-menu').classList.toggle('hidden');
    });
</script>