@extends('layouts.app')

@section('title', 'Galeri Perbaikan — Sipras Manusa')

@section('content')
{{-- Header --}}
<div class="bento-dark relative overflow-hidden !p-8 sm:!p-10">
    <div class="pointer-events-none absolute -right-12 -top-12 h-44 w-44 rounded-full bg-sage/40"></div>
    <div class="relative">
        <span class="chip text-sand">Galeri Perbaikan</span>
        <h1 class="mt-3 text-3xl font-extrabold leading-tight sm:text-4xl">
            Bukti nyata, <span class="text-sand">setiap perbaikan.</span>
        </h1>
        <p class="mt-2 max-w-xl text-sm text-cream/75">
            Kumpulan laporan yang sudah dikerjakan petugas dan dikonfirmasi admin, lengkap dengan foto sebelum dan sesudah.
        </p>
        <p class="mt-4 text-sm font-semibold text-sand">{{ $totalSelesai }} perbaikan selesai</p>
    </div>
</div>

{{-- Filter kategori --}}
<div class="mt-4 flex flex-wrap gap-2">
    <a href="{{ route('galeri') }}"
       class="btn !px-4 !py-1.5 !text-xs {{ request('kategori') ? 'border border-forest/20 text-forest hover:bg-forest/5' : 'bg-forest text-cream' }}">
        Semua ({{ $totalSelesai }})
    </a>
    @foreach($categories as $kategori)
        <a href="{{ route('galeri', ['kategori' => $kategori]) }}"
           class="btn !px-4 !py-1.5 !text-xs {{ request('kategori') === $kategori ? 'bg-forest text-cream' : 'border border-forest/20 text-forest hover:bg-forest/5' }}">
            {{ $kategori }} ({{ $counts[$kategori] ?? 0 }})
        </a>
    @endforeach
</div>

{{-- Kartu perbaikan --}}
<div class="mt-4 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
    @forelse($reports as $report)
        <article class="bento bento-hover flex flex-col overflow-hidden !p-0">
            {{-- Sebelum / Sesudah --}}
            <div class="grid grid-cols-2 gap-0.5 bg-forest/10">
                <div class="relative h-40 bg-forest/5">
                    @if($report->foto_kerusakan)
                        <img src="{{ asset('storage/' . $report->foto_kerusakan) }}" alt="Sebelum: {{ $report->judul }}"
                             loading="lazy" class="h-full w-full object-cover">
                    @endif
                    <span class="badge absolute bottom-2 left-2 bg-forest/80 !py-0.5 !text-[10px] text-cream">Sebelum</span>
                </div>
                <div class="relative h-40 bg-forest/5">
                    @if($report->foto_bukti)
                        <img src="{{ asset('storage/' . $report->foto_bukti) }}" alt="Sesudah: {{ $report->judul }}"
                             loading="lazy" class="h-full w-full object-cover">
                    @endif
                    <span class="badge absolute bottom-2 left-2 bg-sage !py-0.5 !text-[10px] text-cream">Sesudah</span>
                </div>
            </div>

            <div class="flex flex-1 flex-col p-5">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="badge bg-sand/40 text-forest">{{ $report->kategori }}</span>
                    <span class="text-xs text-forest/50">
                        {{ optional($report->completed_at)->translatedFormat('d M Y') }}
                    </span>
                </div>

                <h3 class="mt-3 font-bold leading-snug">{{ $report->judul }}</h3>
                <p class="mt-1 text-xs text-forest/60">{{ $report->lokasi }}</p>

                @if($report->keterangan_selesai)
                    <p class="mt-3 text-sm leading-relaxed text-forest/75">
                        {{ \Illuminate\Support\Str::limit($report->keterangan_selesai, 110) }}
                    </p>
                @endif
            </div>
        </article>
    @empty
        <div class="bento py-14 text-center md:col-span-2 lg:col-span-3">
            <p class="text-sm font-semibold">Belum ada perbaikan yang selesai.</p>
            <p class="mt-1 text-xs text-forest/60">Perbaikan yang sudah dikonfirmasi admin akan tampil di sini.</p>
        </div>
    @endforelse
</div>

<div class="mt-6">
    {{ $reports->links() }}
</div>
@endsection