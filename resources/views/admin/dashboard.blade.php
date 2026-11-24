@extends('layouts.app')

@section('title', 'Dashboard Admin / Guru — Sipras Manusa')

@section('content')
{{-- Header --}}
<div class="bento-dark flex flex-wrap items-center justify-between gap-4 !p-8">
    <div>
        <span class="chip text-sand">Panel Kendali Admin / Guru</span>
        <h1 class="mt-3 text-2xl font-extrabold sm:text-3xl">Dashboard Sarana &amp; Prasarana</h1>
        <p class="mt-1 max-w-xl text-xs text-cream/70 sm:text-sm">
            Kelola laporan masuk, tugaskan petugas, dan verifikasi bukti perbaikan sebelum selesai.
        </p>
    </div>
    <a href="{{ route('reports.index') }}" class="btn-sand">Lihat Semua Laporan</a>
</div>

{{-- Kartu hitungan --}}
<div class="mt-4 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
    <div class="bento-dark bento-hover !p-5">
        <p class="text-[11px] font-semibold uppercase tracking-wider text-cream/70">Total</p>
        <p class="mt-2 text-3xl font-extrabold">{{ $counts['total'] }}</p>
    </div>
    <div class="bento-sand bento-hover !p-5">
        <p class="text-[11px] font-semibold uppercase tracking-wider text-forest/70">Menunggu Cek</p>
        <p class="mt-2 text-3xl font-extrabold">{{ $counts['menunggu_verifikasi'] }}</p>
    </div>
    <div class="bento-sage bento-hover !p-5">
        <p class="text-[11px] font-semibold uppercase tracking-wider text-cream/80">Diproses</p>
        <p class="mt-2 text-3xl font-extrabold">{{ $counts['diproses'] }}</p>
    </div>
    <div class="bento bento-hover !p-5">
        <p class="text-[11px] font-semibold uppercase tracking-wider text-forest/60">Cek Bukti</p>
        <p class="mt-2 text-3xl font-extrabold">{{ $counts['menunggu_konfirmasi'] }}</p>
    </div>
    <div class="bento bento-hover !border-sage/40 !p-5">
        <p class="text-[11px] font-semibold uppercase tracking-wider text-sage">Selesai</p>
        <p class="mt-2 text-3xl font-extrabold text-sage">{{ $counts['selesai'] }}</p>
    </div>
    <div class="bento bento-hover !border-clay/30 !p-5">
        <p class="text-[11px] font-semibold uppercase tracking-wider text-clay">Ditolak</p>
        <p class="mt-2 text-3xl font-extrabold text-clay">{{ $counts['ditolak'] }}</p>
    </div>
</div>

{{-- Grafik --}}
<div class="mt-4 grid gap-4 lg:grid-cols-2">
    <div class="bento">
        <h2 class="text-base font-bold">Statistik Laporan per Status</h2>
        <p class="mb-4 text-xs text-forest/60">Sebaran status laporan kerusakan di sekolah</p>
        <div class="h-64">
            <canvas id="statusChart" data-values="{{ json_encode($byStatus) }}"></canvas>
        </div>
    </div>

    <div class="bento">
        <h2 class="text-base font-bold">Statistik Tingkat Prioritas</h2>
        <p class="mb-4 text-xs text-forest/60">Perbandingan prioritas tinggi, sedang, dan rendah</p>
        <div class="h-64">
            <canvas id="prioritasChart" data-values="{{ json_encode($byPrioritas) }}"></canvas>
        </div>
    </div>
</div>

{{-- Tabel tindakan --}}
<div class="bento mt-4">
    <div class="mb-4">
        <h2 class="text-lg font-bold">Butuh Tindakan Admin Segera</h2>
        <p class="text-xs text-forest/60">Laporan baru dari siswa atau bukti perbaikan dari petugas yang menunggu pemeriksaanmu.</p>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead>
                <tr class="border-b border-forest/10 text-xs uppercase tracking-wider text-forest/50">
                    <th class="py-3 pr-4">Foto</th>
                    <th class="py-3 pr-4">Judul &amp; Lokasi</th>
                    <th class="py-3 pr-4">Pelapor</th>
                    <th class="py-3 pr-4">Prioritas</th>
                    <th class="py-3 pr-4">Status</th>
                    <th class="py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-forest/10">
                @forelse($needsAction as $report)
                    <tr class="transition hover:bg-forest/5">
                        <td class="py-3 pr-4">
                            <img src="{{ asset('storage/' . $report->foto_kerusakan) }}" alt="{{ $report->judul }}"
                                 class="h-12 w-16 rounded-xl border border-forest/10 object-cover">
                        </td>
                        <td class="py-3 pr-4">
                            <div class="font-semibold">{{ $report->judul }}</div>
                            <div class="text-xs text-forest/60">{{ $report->lokasi }} &bull; {{ $report->tanggal_laporan->format('d M Y') }}</div>
                        </td>
                        <td class="py-3 pr-4 text-xs">
                            {{ $report->pelapor->name }}<br>
                            <span class="text-forest/50">{{ $report->pelapor->kelas_atau_unit }}</span>
                        </td>
                        <td class="py-3 pr-4">
                            <span class="inline-block rounded-full border px-2.5 py-0.5 text-xs font-semibold uppercase {{ $report->prioritas_badge_class }}">
                                {{ $report->prioritas }}
                            </span>
                        </td>
                        <td class="py-3 pr-4">
                            <span class="inline-block rounded-full border px-2.5 py-0.5 text-xs font-semibold {{ $report->status_badge_class }}">
                                {{ $report->status_label }}
                            </span>
                        </td>
                        <td class="py-3 text-right">
                            <a href="{{ route('reports.show', $report) }}" class="btn-primary !px-4 !py-1.5 !text-xs">
                                Proses / Cek
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-sm text-forest/60">
                            Tidak ada laporan yang menunggu tindakan admin saat ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    Chart.defaults.font.family = '"Plus Jakarta Sans", sans-serif';
    Chart.defaults.color = '#5b6b5f';

    const statusEl = document.getElementById('statusChart');
    new Chart(statusEl.getContext('2d'), {
        type: 'bar',
        data: {
            labels: ['Menunggu Cek', 'Diterima', 'Dikerjakan', 'Cek Bukti', 'Selesai', 'Ditolak'],
            datasets: [{
                label: 'Jumlah Laporan',
                data: JSON.parse(statusEl.dataset.values),
                backgroundColor: ['#C8B48A', '#A3AE8F', '#7F8C6A', '#4F6B58', '#1E3B2F', '#B5523B'],
                borderRadius: 12,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false } },
                y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: 'rgba(30,59,47,0.08)' } }
            }
        }
    });

    const prioritasEl = document.getElementById('prioritasChart');
    new Chart(prioritasEl.getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: ['Tinggi', 'Sedang', 'Rendah'],
            datasets: [{
                data: JSON.parse(prioritasEl.dataset.values),
                backgroundColor: ['#B5523B', '#C8B48A', '#7F8C6A'],
                borderColor: '#F5F0E6',
                borderWidth: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: { legend: { position: 'bottom' } }
        }
    });
</script>
@endpush