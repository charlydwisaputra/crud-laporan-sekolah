@extends('layouts.app')

@section('title', 'Homepage — Sipras Manusa')

@section('content')
{{-- ===== HERO + FOTO ===== --}}
<div class="grid gap-4 md:grid-cols-2">

    {{-- Hero --}}
    <section class="bento-dark relative flex min-h-[22rem] flex-col justify-between overflow-hidden !p-8 sm:!p-10">
        <div class="pointer-events-none absolute -right-16 -top-16 h-56 w-56 rounded-full bg-sage/40"></div>
        <div class="pointer-events-none absolute -bottom-10 right-10 h-28 w-28 rounded-full bg-sand/30"></div>

        <div class="relative">
            <span class="chip text-sand">Sistem Pelaporan &amp; Perbaikan Fasilitas Sekolah</span>

            <h1 class="mt-5 text-balance text-3xl font-extrabold leading-tight tracking-tight sm:text-4xl lg:text-5xl">
                Laporkan kerusakan sekolah,
                <span class="text-sand">cepat &amp; terpantau.</span>
            </h1>

            <p class="mt-4 max-w-md text-sm leading-relaxed text-cream/80">
                Siswa melaporkan kerusakan sarana kelas, laboratorium, maupun lingkungan sekolah lengkap dengan foto.
                Admin/Guru dan Petugas menanganinya secara transparan hingga tuntas.
            </p>
        </div>

        <div class="relative mt-8 flex flex-wrap items-center gap-3">
            @auth
                @if(auth()->user()->isSiswa())
                    <a href="{{ route('reports.create') }}" class="btn-sand">Buat Laporan Sekarang</a>
                @endif
                <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('reports.index') }}"
                   class="btn border border-cream/30 text-cream hover:bg-cream/10">
                    Buka Panel {{ ucfirst(auth()->user()->role) }}
                </a>
            @else
                <a href="{{ route('login') }}" class="btn-sand">Masuk ke Akun</a>
                <a href="{{ route('register') }}" class="btn border border-cream/30 text-cream hover:bg-cream/10">
                    Daftar Akun Siswa
                </a>
            @endauth
        </div>
    </section>

    {{-- Foto sekolah --}}
    <section class="relative min-h-[16rem] overflow-hidden rounded-[2rem] bg-sage shadow-sm">
        @if(file_exists(public_path('images/banner.jpg')))
            <img src="{{ asset('images/banner.jpg') }}" alt="Gedung sekolah" class="absolute inset-0 h-full w-full object-cover object-center">
            <div class="absolute inset-0 bg-gradient-to-t from-forest/60 via-transparent to-transparent"></div>

            {{-- Ubah atau hapus chip ini bila tidak diperlukan --}}
            <span class="absolute bottom-4 left-4 rounded-full bg-cream/90 px-4 py-1.5 text-xs font-semibold text-forest backdrop-blur">
                SMK Ma&rsquo;arif NU 1 Ajibarang
            </span>
        @else
            <div class="pointer-events-none absolute -right-10 -top-10 h-44 w-44 rounded-full bg-forest/30"></div>
            <div class="pointer-events-none absolute -bottom-8 left-8 h-28 w-28 rounded-full bg-sand/40"></div>
        @endif
    </section>
</div>

{{-- ===== 4 KARTU RINGKASAN ===== --}}
<div class="mt-4 grid grid-cols-2 gap-4 lg:grid-cols-4">

    {{-- Kartu 1: Progres penyelesaian --}}
    <div class="bento-sage bento-hover flex min-h-[13rem] flex-col justify-between">
        <p class="text-xs font-semibold uppercase tracking-wider text-cream/80">Progres Penyelesaian</p>

        <div>
            <p class="text-4xl font-extrabold">
                {{ $stats['selesai'] }}<span class="text-lg font-semibold text-cream/70"> / {{ $stats['total'] }}</span>
            </p>
            <p class="mt-1 text-xs text-cream/80">laporan selesai diperbaiki</p>

            <div class="mt-3 h-2.5 w-full overflow-hidden rounded-full bg-forest/30">
                <div class="h-full rounded-full bg-cream" style="width: {{ $stats['persen'] }}%"></div>
            </div>
            <p class="mt-2 text-xs text-cream/80">
                {{ $stats['persen'] }}% tuntas &bull; {{ $stats['aktif'] }} sedang ditangani
            </p>
        </div>
    </div>

    {{-- Kartu 2: Rata-rata waktu perbaikan --}}
    <div class="bento-sand bento-hover flex min-h-[13rem] flex-col justify-between">
        <p class="text-xs font-semibold uppercase tracking-wider text-forest/70">Rata-rata Waktu Perbaikan</p>

        @if($waktuRata)
            <div>
                <p class="text-4xl font-extrabold">
                    {{ rtrim(rtrim(number_format($waktuRata['angka'], 1, ',', ''), '0'), ',') }}
                    <span class="text-lg font-semibold text-forest/70">{{ $waktuRata['satuan'] }}</span>
                </p>
                <p class="mt-1 text-xs text-forest/75">dari laporan masuk sampai dikonfirmasi selesai</p>
            </div>
        @else
            <div>
                <p class="text-2xl font-extrabold">Belum ada data</p>
                <p class="mt-1 text-xs text-forest/75">Muncul setelah ada laporan yang selesai.</p>
            </div>
        @endif
    </div>

    {{-- Kartu 3: Cara melapor --}}
    <div class="bento bento-hover flex min-h-[13rem] flex-col justify-between">
        <p class="text-xs font-semibold uppercase tracking-wider text-forest/60">Cara Melapor</p>

        <ol class="space-y-2.5 text-sm font-medium">
            <li class="flex items-center gap-3">
                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-forest text-xs font-bold text-cream">1</span>
                Foto kerusakannya
            </li>
            <li class="flex items-center gap-3">
                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-sage text-xs font-bold text-cream">2</span>
                Isi form laporan
            </li>
            <li class="flex items-center gap-3">
                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-sand text-xs font-bold text-forest">3</span>
                Pantau statusnya
            </li>
        </ol>
    </div>

    {{-- Kartu 4: Sesuai peran --}}
    <div class="bento-dark bento-hover flex min-h-[13rem] flex-col justify-between">
        @if($roleCard === null)
            {{-- Tamu --}}
            <p class="text-xs font-semibold uppercase tracking-wider text-sand">Galeri Perbaikan</p>
            <p class="text-sm leading-relaxed text-cream/80">Lihat foto sebelum dan sesudah dari perbaikan yang sudah selesai.</p>
            <a href="{{ route('galeri') }}" class="btn-sand self-start !px-4 !py-1.5 !text-xs">Buka Galeri</a>

        @elseif($roleCard['type'] === 'admin')
            <p class="text-xs font-semibold uppercase tracking-wider text-sand">Perlu Tindakanmu</p>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <p class="text-3xl font-extrabold">{{ $roleCard['verifikasi'] }}</p>
                    <p class="text-[11px] text-cream/70">menunggu verifikasi</p>
                </div>
                <div>
                    <p class="text-3xl font-extrabold">{{ $roleCard['konfirmasi'] }}</p>
                    <p class="text-[11px] text-cream/70">cek bukti</p>
                </div>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="btn-sand self-start !px-4 !py-1.5 !text-xs">Buka Dashboard</a>

        @elseif($roleCard['type'] === 'petugas')
            <p class="text-xs font-semibold uppercase tracking-wider text-sand">Tugas Untukmu</p>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <p class="text-3xl font-extrabold">{{ $roleCard['baru'] }}</p>
                    <p class="text-[11px] text-cream/70">tugas baru</p>
                </div>
                <div>
                    <p class="text-3xl font-extrabold">{{ $roleCard['dikerjakan'] }}</p>
                    <p class="text-[11px] text-cream/70">sedang dikerjakan</p>
                </div>
            </div>
            <a href="{{ route('reports.index') }}" class="btn-sand self-start !px-4 !py-1.5 !text-xs">Lihat Tugas</a>

        @else
            {{-- Siswa --}}
            <p class="text-xs font-semibold uppercase tracking-wider text-sand">Laporan Terakhirmu</p>
            @if($roleCard['latest'])
                <div>
                    <p class="line-clamp-2 text-sm font-bold leading-snug">{{ $roleCard['latest']->judul }}</p>
                    <span class="chip mt-2 text-sand">{{ $roleCard['latest']->status_label }}</span>
                </div>
                <a href="{{ route('reports.show', $roleCard['latest']) }}" class="btn-sand self-start !px-4 !py-1.5 !text-xs">Lihat Laporan</a>
            @else
                <p class="text-sm text-cream/80">Kamu belum membuat laporan.</p>
                <a href="{{ route('reports.create') }}" class="btn-sand self-start !px-4 !py-1.5 !text-xs">Buat Laporan</a>
            @endif
        @endif
    </div>
</div>

{{-- ===== ALUR KERJA 3 PERAN ===== --}}
<section class="mt-12">
    <div class="mb-5">
        <h2 class="text-xl font-extrabold sm:text-2xl">Alur Kerja 3 Peran</h2>
        <p class="mt-1 text-sm text-forest/60">Setiap peran memiliki tanggung jawab jelas dari pelaporan hingga konfirmasi akhir.</p>
    </div>

    <div class="grid gap-4 md:grid-cols-3">
        <div class="bento bento-hover">
            <span class="flex h-11 w-11 items-center justify-center rounded-full bg-forest text-sm font-bold text-cream">01</span>
            <h3 class="mt-4 text-lg font-bold">Siswa</h3>
            <ul class="mt-3 space-y-2 text-sm text-forest/75">
                <li>&bull; Membuat laporan dengan foto, kategori, lokasi, judul, deskripsi, prioritas, dan tanggal otomatis.</li>
                <li>&bull; Mengedit atau menghapus laporan selama belum diterima admin.</li>
                <li>&bull; Hanya melihat laporan miliknya sendiri dan memantau statusnya.</li>
            </ul>
        </div>

        <div class="bento-dark bento-hover">
            <span class="flex h-11 w-11 items-center justify-center rounded-full bg-sand text-sm font-bold text-forest">02</span>
            <h3 class="mt-4 text-lg font-bold">Admin / Guru</h3>
            <ul class="mt-3 space-y-2 text-sm text-cream/75">
                <li>&bull; Memegang hak penuh atas data laporan dan alur penanganan.</li>
                <li>&bull; Menerima atau menolak laporan siswa serta menugaskan petugas.</li>
                <li>&bull; Memeriksa foto bukti selesai dari petugas sebelum mengirim tanda selesai ke siswa.</li>
            </ul>
        </div>

        <div class="bento-sand bento-hover">
            <span class="flex h-11 w-11 items-center justify-center rounded-full bg-forest text-sm font-bold text-cream">03</span>
            <h3 class="mt-4 text-lg font-bold">Petugas</h3>
            <ul class="mt-3 space-y-2 text-sm text-forest/80">
                <li>&bull; Melihat tugas yang diberikan admin beserta foto kerusakan.</li>
                <li>&bull; Menekan <strong>Kerjakan</strong> untuk menerima dan memulai tugas, atau mengembalikannya ke admin beserta alasan.</li>
                <li>&bull; Klik <strong>Selesai</strong> dan wajib melampirkan foto bukti perbaikan.</li>
            </ul>
        </div>
    </div>
</section>

{{-- ===== PERBAIKAN TERBARU ===== --}}
@if($recentCompleted->isNotEmpty())
<section class="mt-12">
    <div class="mb-5 flex flex-wrap items-end justify-between gap-2">
        <h2 class="text-xl font-extrabold sm:text-2xl">Perbaikan Terbaru Selesai</h2>
        <a href="{{ route('galeri') }}" class="text-sm font-semibold underline-offset-2 hover:underline">
            Lihat semua perbaikan &rarr;
        </a>
    </div>

    <div class="grid gap-4 md:grid-cols-3">
        @foreach($recentCompleted as $item)
            <a href="{{ route('galeri') }}" class="bento bento-hover block overflow-hidden !p-0">
                @if($item->foto_bukti)
                    <img src="{{ asset('storage/' . $item->foto_bukti) }}" alt="{{ $item->judul }}" class="h-44 w-full object-cover">
                @endif
                <div class="p-5">
                    <span class="badge bg-sage text-cream">Selesai</span>
                    <h3 class="mt-3 font-bold">{{ $item->judul }}</h3>
                    <p class="mt-1 text-xs text-forest/60">{{ $item->lokasi }} &bull; {{ $item->kategori }}</p>
                </div>
            </a>
        @endforeach
    </div>
</section>
@endif
@endsection