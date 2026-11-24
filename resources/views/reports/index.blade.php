@extends('layouts.app')

@section('title', 'Daftar Laporan — Sipras Manusa')

@section('content')
{{-- Header --}}
<div class="bento-dark flex flex-wrap items-center justify-between gap-4 !p-8">
    <div>
        <span class="chip text-sand">
            @if(auth()->user()->isSiswa()) Siswa
            @elseif(auth()->user()->isPetugas()) Petugas
            @else Admin / Guru
            @endif
        </span>
        <h1 class="mt-3 text-2xl font-extrabold sm:text-3xl">
            @if(auth()->user()->isSiswa())
                Laporan Saya
            @elseif(auth()->user()->isPetugas())
                Tugas Perbaikan Saya
            @else
                Semua Laporan Kerusakan
            @endif
        </h1>
        <p class="mt-1 text-xs text-cream/70 sm:text-sm">{{ $reports->total() }} laporan ditemukan</p>
    </div>

    @if(auth()->user()->isSiswa())
        <a href="{{ route('reports.create') }}" class="btn-sand">+ Buat Laporan Baru</a>
    @endif
</div>

{{-- Filter & Pencarian --}}
<form method="GET" action="{{ route('reports.index') }}" class="bento mt-4 !p-5">
    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Cari judul / lokasi / deskripsi" class="input-bento sm:col-span-2 lg:col-span-4">

        <select name="status" class="input-bento">
            <option value="">Semua Status</option>
            <option value="menunggu_verifikasi" @selected(request('status') === 'menunggu_verifikasi')>Menunggu Verifikasi</option>
            <option value="ditolak" @selected(request('status') === 'ditolak')>Ditolak</option>
            <option value="diterima" @selected(request('status') === 'diterima')>Diterima &amp; Ditugaskan</option>
            <option value="sedang_dikerjakan" @selected(request('status') === 'sedang_dikerjakan')>Sedang Dikerjakan</option>
            <option value="menunggu_konfirmasi" @selected(request('status') === 'menunggu_konfirmasi')>Menunggu Konfirmasi Admin</option>
            <option value="selesai" @selected(request('status') === 'selesai')>Selesai</option>
        </select>

        <select name="prioritas" class="input-bento">
            <option value="">Semua Prioritas</option>
            <option value="rendah" @selected(request('prioritas') === 'rendah')>Rendah</option>
            <option value="sedang" @selected(request('prioritas') === 'sedang')>Sedang</option>
            <option value="tinggi" @selected(request('prioritas') === 'tinggi')>Tinggi</option>
        </select>

        <input type="text" name="kategori" value="{{ request('kategori') }}" placeholder="Kategori" class="input-bento">

        <div class="flex gap-2">
            <button type="submit" class="btn-primary flex-1">Filter</button>
            <a href="{{ route('reports.index') }}" class="btn-ghost">Reset</a>
        </div>
    </div>
</form>

{{-- Daftar laporan --}}
<div class="mt-4 grid gap-4 lg:grid-cols-2">
    @forelse($reports as $index => $report)
        <article class="bento bento-hover flex flex-col gap-4 !p-4 sm:flex-row">
            {{-- Foto --}}
            <div class="h-40 w-full shrink-0 overflow-hidden rounded-[1.5rem] bg-forest/5 sm:h-auto sm:w-40">
                @if($report->foto_kerusakan)
                    <img src="{{ asset('storage/' . $report->foto_kerusakan) }}" alt="Foto kerusakan"
                         class="h-full w-full object-cover">
                @else
                    <div class="flex h-full w-full items-center justify-center text-xs text-forest/40">Tanpa foto</div>
                @endif
            </div>

            {{-- Isi --}}
            <div class="flex min-w-0 flex-1 flex-col">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-xs font-bold text-forest/40">#{{ $reports->firstItem() + $index }}</span>
                    <span class="badge border {{ $report->status_badge_class }}">{{ $report->status_label }}</span>
                    <span class="badge border uppercase {{ $report->prioritas_badge_class }}">{{ $report->prioritas }}</span>
                </div>

                <h3 class="mt-2 text-base font-bold leading-snug">{{ $report->judul }}</h3>
                <p class="text-xs text-forest/60">
                    {{ $report->kategori }} &bull; {{ $report->lokasi }} &bull; {{ $report->tanggal_laporan->format('d M Y') }}
                </p>

                <dl class="mt-3 grid grid-cols-2 gap-2 text-xs">
                    <div>
                        <dt class="text-forest/50">Pelapor</dt>
                        <dd class="font-semibold">{{ $report->pelapor->name ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-forest/50">Petugas</dt>
                        <dd class="font-semibold">{{ $report->petugas->name ?? 'Belum ditugaskan' }}</dd>
                    </div>
                </dl>

                @if($report->catatan_penolakan)
                    <p class="mt-3 rounded-2xl bg-clay/10 px-3 py-2 text-xs text-clay">
                        <strong>Catatan:</strong> {{ $report->catatan_penolakan }}
                    </p>
                @endif

                {{-- Aksi --}}
                <div class="mt-4 flex flex-wrap items-center gap-2 pt-1 sm:mt-auto">
                    <a href="{{ route('reports.show', $report) }}" class="btn-primary !px-4 !py-1.5 !text-xs">Detail</a>

                    @if(auth()->user()->isSiswa() && $report->user_id === auth()->id() && $report->canBeEditedOrDeletedBySiswa())
                        <a href="{{ route('reports.edit', $report) }}" class="btn-ghost !px-4 !py-1.5 !text-xs">Edit</a>

                        <form action="{{ route('reports.destroy', $report) }}" method="POST"
                              onsubmit="return confirm('Yakin hapus laporan ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-danger !px-4 !py-1.5 !text-xs">Hapus</button>
                        </form>
                    @endif

                    @if(auth()->user()->isAdmin())
                        <form action="{{ route('admin.reports.destroy', $report) }}" method="POST"
                              onsubmit="return confirm('Hapus laporan ini secara permanen? Laporan juga akan hilang dari akun siswa.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-danger !px-4 !py-1.5 !text-xs">Hapus</button>
                        </form>
                    @endif
                </div>
            </div>
        </article>
    @empty
        <div class="bento py-12 text-center lg:col-span-2">
            <p class="text-sm font-semibold">Belum ada data laporan.</p>
            <p class="mt-1 text-xs text-forest/60">Coba ubah filter atau kata kunci pencarian.</p>
        </div>
    @endforelse
</div>

<div class="mt-6">
    {{ $reports->links() }}
</div>
@endsection