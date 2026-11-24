@extends('layouts.app')

@section('title', 'Detail Laporan: ' . $report->judul)

@section('content')
<div class="space-y-4">

    {{-- Header --}}
    <div class="bento-dark !p-8">
        <a href="{{ route('reports.index') }}" class="text-xs font-semibold text-sand hover:underline">&larr; Kembali ke Daftar Laporan</a>
        <h1 class="mt-3 text-2xl font-extrabold sm:text-3xl">{{ $report->judul }}</h1>
        <div class="mt-4 flex flex-wrap items-center gap-2">
            <span class="badge border {{ $report->status_badge_class }}">{{ $report->status_label }}</span>
            <span class="badge border uppercase {{ $report->prioritas_badge_class }}">Prioritas: {{ $report->prioritas }}</span>
            <span class="badge border border-cream/30 text-cream/80">{{ $report->tanggal_laporan->format('d M Y') }}</span>
        </div>
    </div>

    <div class="grid gap-4 lg:grid-cols-3">
        {{-- Informasi kerusakan --}}
        <div class="bento space-y-4 lg:col-span-2">
            <h2 class="text-base font-bold">Informasi Kerusakan</h2>

            <div class="grid gap-3 sm:grid-cols-2">
                <div class="rounded-2xl bg-cream p-4">
                    <span class="block text-[11px] font-semibold uppercase tracking-wider text-forest/50">Pelapor (Siswa)</span>
                    <span class="mt-1 block text-sm font-semibold">
                        {{ $report->pelapor->name ?? '-' }}
                        <span class="font-normal text-forest/60">({{ $report->pelapor->kelas_atau_unit ?? '-' }})</span>
                    </span>
                </div>
                <div class="rounded-2xl bg-cream p-4">
                    <span class="block text-[11px] font-semibold uppercase tracking-wider text-forest/50">Petugas Ditugaskan</span>
                    <span class="mt-1 block text-sm font-semibold">{{ $report->petugas->name ?? 'Belum ada petugas' }}</span>
                </div>
                <div class="rounded-2xl bg-cream p-4">
                    <span class="block text-[11px] font-semibold uppercase tracking-wider text-forest/50">Kategori</span>
                    <span class="mt-1 block text-sm font-semibold">{{ $report->kategori }}</span>
                </div>
                <div class="rounded-2xl bg-cream p-4">
                    <span class="block text-[11px] font-semibold uppercase tracking-wider text-forest/50">Lokasi</span>
                    <span class="mt-1 block text-sm font-semibold">{{ $report->lokasi }}</span>
                </div>
            </div>

            <div class="rounded-2xl bg-cream p-4">
                <span class="block text-[11px] font-semibold uppercase tracking-wider text-forest/50">Deskripsi Kerusakan</span>
                <p class="mt-2 whitespace-pre-line text-sm leading-relaxed">{{ $report->deskripsi }}</p>
            </div>

            @if($report->catatan_penolakan)
                <div class="rounded-2xl border border-clay/30 bg-clay/10 p-4 text-sm text-clay">
                    <strong class="font-semibold">Catatan Penolakan / Pengembalian:</strong>
                    <p class="mt-1">{{ $report->catatan_penolakan }}</p>
                </div>
            @endif

            <div>
                <h3 class="mb-2 text-[11px] font-semibold uppercase tracking-wider text-forest/50">Foto Kerusakan (dari Siswa)</h3>
                @if($report->foto_kerusakan)
                    <img src="{{ asset('storage/' . $report->foto_kerusakan) }}" alt="Foto Kerusakan"
                         class="max-h-80 w-full rounded-[1.5rem] object-cover">
                @else
                    <p class="text-xs text-forest/60">Tidak ada foto kerusakan.</p>
                @endif
            </div>
        </div>

        {{-- Kolom kanan --}}
        <div class="space-y-4">
            @if($report->foto_bukti || $report->keterangan_selesai)
                <div class="bento-sage space-y-3">
                    <h2 class="text-base font-bold">Bukti Perbaikan</h2>
                    <p class="text-xs"><strong>Keterangan:</strong> {{ $report->keterangan_selesai ?? '-' }}</p>
                    @if($report->completed_at)
                        <p class="text-xs text-cream/80"><strong>Selesai Admin:</strong> {{ $report->completed_at->format('d M Y H:i') }}</p>
                    @endif
                    @if($report->foto_bukti)
                        <img src="{{ asset('storage/' . $report->foto_bukti) }}" alt="Foto Bukti Perbaikan"
                             class="max-h-60 w-full rounded-[1.5rem] object-cover">
                    @endif
                </div>
            @endif

            <div class="bento">
                <h2 class="mb-4 text-base font-bold">Riwayat Aktivitas</h2>
                <ul class="space-y-4 border-l-2 border-forest/10 pl-4">
                    @forelse($report->logs as $log)
                        <li class="relative text-xs">
                            <span class="absolute -left-[1.4rem] top-1 h-3 w-3 rounded-full border-2 border-cream bg-sage"></span>
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <span class="text-forest/50">{{ $log->created_at->format('d M Y H:i') }}</span>
                                <span class="badge bg-forest text-cream !py-0.5 !text-[10px]">
                                    {{ \App\Models\Report::labelFor($log->status_baru) }}
                                </span>
                            </div>
                            <p class="mt-1 font-semibold">Oleh {{ $log->aktor->name ?? 'Sistem' }}</p>
                            @if($log->catatan)
                                <p class="mt-0.5 text-forest/60">{{ $log->catatan }}</p>
                            @endif
                        </li>
                    @empty
                        <li class="text-xs text-forest/60">Belum ada riwayat aktivitas.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    {{-- ================= AKSI SISWA ================= --}}
    @if(auth()->user()->isSiswa() && $report->user_id === auth()->id())
        <div class="bento">
            <h2 class="text-base font-bold">Aksi Siswa</h2>
            @if($report->canBeEditedOrDeletedBySiswa())
                <div class="mt-4 flex flex-wrap items-center gap-3">
                    <a href="{{ route('reports.edit', $report) }}" class="btn-primary">Edit Laporan</a>
                    <form action="{{ route('reports.destroy', $report) }}" method="POST"
                          onsubmit="return confirm('Yakin hapus laporan ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-danger">Hapus Laporan</button>
                    </form>
                </div>
            @else
                <p class="mt-2 text-xs text-forest/60">Laporan tidak dapat diedit atau dihapus karena sudah diproses/diterima admin.</p>
            @endif
        </div>
    @endif

    {{-- ================= AKSI ADMIN / GURU ================= --}}
    @if(auth()->user()->isAdmin())
        <div class="bento space-y-5">
            <div>
                <h2 class="text-lg font-bold">Panel Aksi Admin / Guru</h2>
                <p class="text-xs text-forest/60">Kelola verifikasi laporan, penugasan petugas, dan konfirmasi bukti perbaikan.</p>
            </div>

            @if(in_array($report->status, ['menunggu_verifikasi', 'ditolak'], true))
                <div class="grid gap-4 {{ $report->status === 'menunggu_verifikasi' ? 'lg:grid-cols-2' : '' }}">
                    <form action="{{ route('reports.assign', $report) }}" method="POST" class="bento-dark space-y-4">
                        @csrf
                        <h3 class="text-sm font-bold text-sand">
                            {{ $report->status === 'ditolak' ? 'Terima Ulang & Tugaskan Petugas' : '1. Terima Laporan & Tugaskan Petugas' }}
                        </h3>

                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-cream/80">Pilih Petugas</label>
                            <select name="petugas_id" required class="input-bento !bg-cream">
                                <option value="">-- Pilih Petugas --</option>
                                @foreach($petugasList as $petugas)
                                    <option value="{{ $petugas->id }}" @selected((int) $report->petugas_id === (int) $petugas->id)>
                                        {{ $petugas->name }} ({{ $petugas->kelas_atau_unit ?? 'Petugas' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-cream/80">Catatan Penugasan (opsional)</label>
                            <input type="text" name="catatan_penugasan" placeholder="Catatan untuk petugas" class="input-bento !bg-cream">
                        </div>

                        <button type="submit" class="btn-sand">Terima &amp; Tugaskan Petugas</button>
                    </form>

                    @if($report->status === 'menunggu_verifikasi')
                        <form action="{{ route('reports.reject', $report) }}" method="POST" class="bento space-y-4">
                            @csrf
                            <h3 class="text-sm font-bold text-clay">2. Tolak Laporan Siswa</h3>

                            <div>
                                <label class="label-bento">Alasan Penolakan (wajib)</label>
                                <textarea name="catatan_penolakan" rows="3" required
                                          placeholder="Contoh: Foto kurang jelas atau duplikat laporan"
                                          class="input-bento"></textarea>
                            </div>

                            <button type="submit" class="btn-danger">Tolak Laporan</button>
                        </form>
                    @endif
                </div>
            @endif

            @if($report->status === 'menunggu_konfirmasi')
                <form action="{{ route('reports.confirm-completion', $report) }}" method="POST" class="bento-sage max-w-xl space-y-4">
                    @csrf
                    <h3 class="text-sm font-bold">Konfirmasi Penyelesaian Tugas Petugas</h3>
                    <p class="text-xs text-cream/80">Petugas sudah mengunggah foto bukti &amp; keterangan. Periksa sebelum mengirim tanda selesai ke siswa.</p>

                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-cream/90">Catatan Admin (opsional)</label>
                        <input type="text" name="catatan_admin" placeholder="Catatan verifikasi admin" class="input-bento !bg-cream">
                    </div>

                    <button type="submit" class="btn bg-forest text-cream hover:bg-forest/90">Setujui &amp; Kirim Tanda Selesai ke Siswa</button>
                </form>
            @endif

            @if(! in_array($report->status, ['menunggu_verifikasi', 'ditolak', 'menunggu_konfirmasi'], true))
                <p class="text-xs text-forest/60">Tidak ada aksi admin yang tersedia untuk status laporan saat ini.</p>
            @endif

            {{-- Zona hapus --}}
            <div class="flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-clay/30 bg-clay/5 p-4">
                <div>
                    <p class="text-sm font-bold text-clay">Hapus Laporan</p>
                    <p class="text-xs text-forest/60">Laporan, foto, dan riwayatnya dihapus permanen. Laporan juga hilang dari akun siswa.</p>
                </div>

                <form action="{{ route('admin.reports.destroy', $report) }}" method="POST"
                      onsubmit="return confirm('Hapus laporan ini secara permanen? Laporan juga akan hilang dari akun siswa dan tidak bisa dikembalikan.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-danger">Hapus Laporan</button>
                </form>
            </div>
        </div>
    @endif

    {{-- ================= AKSI PETUGAS ================= --}}
    @if(auth()->user()->isPetugas() && $report->petugas_id === auth()->id())
        <div class="bento space-y-5">
            <div>
                <h2 class="text-lg font-bold">Panel Aksi Petugas</h2>
                <p class="text-xs text-forest/60">Mulai kerjakan atau kembalikan tugas, lalu unggah bukti perbaikan setelah selesai.</p>
            </div>

            @if($report->status === 'diterima')
                <div class="grid gap-4 md:grid-cols-2">
                    {{-- Opsi 1 --}}
                    <div class="bento-sage space-y-4">
                        <h3 class="text-sm font-bold">Opsi 1: Kerjakan Tugas</h3>
                        <p class="text-xs text-cream/80">Terima tugas ini dan mulai perbaikan. Form bukti selesai akan muncul setelah kamu menekan tombol ini.</p>
                        <form action="{{ route('reports.start', $report) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn bg-forest text-cream hover:bg-forest/90">Kerjakan</button>
                        </form>
                    </div>

                    {{-- Opsi 2 --}}
                    <form action="{{ route('reports.respond', $report) }}" method="POST" class="bento space-y-4">
                        @csrf
                        <input type="hidden" name="keputusan" value="tolak">
                        <h3 class="text-sm font-bold text-clay">Opsi 2: Kembalikan Tugas ke Admin</h3>
                        <input type="text" name="alasan_tolak" required
                               placeholder="Contoh: Butuh sparepart khusus / di luar unit"
                               class="input-bento">
                        <button type="submit" class="btn-danger">Kembalikan Tugas</button>
                    </form>
                </div>

            @elseif($report->status === 'sedang_dikerjakan')
                <form action="{{ route('reports.complete', $report) }}" method="POST" enctype="multipart/form-data"
                      class="bento-sand max-w-xl space-y-4">
                    @csrf
                    <h3 class="text-sm font-bold">Tandai Tugas Selesai (Wajib Foto Bukti)</h3>

                    <div>
                        <label class="mb-1.5 block text-xs font-semibold">Foto Bukti Kerusakan Sudah Diperbaiki (wajib)</label>
                        <input type="file" name="foto_bukti" accept="image/jpg,image/jpeg,image/png,image/webp" required
                               class="input-bento !bg-cream file:mr-3 file:rounded-full file:border-0 file:bg-forest file:px-4 file:py-1.5 file:text-xs file:font-semibold file:text-cream">
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-semibold">Keterangan Perbaikan (wajib)</label>
                        <textarea name="keterangan_selesai" rows="3" required
                                  placeholder="Jelaskan perbaikan yang sudah dilakukan"
                                  class="input-bento !bg-cream"></textarea>
                    </div>

                    <button type="submit" class="btn-primary">Selesai</button>
                </form>

            @else
                <p class="text-xs text-forest/60">Tidak ada aksi lanjutan yang tersedia untuk status laporan saat ini.</p>
            @endif
        </div>
    @endif
</div>
@endsection