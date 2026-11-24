@extends('layouts.app')

@section('title', 'Edit Laporan — Sipras Manusa')

@section('content')
<div class="mx-auto max-w-3xl space-y-4">
    <div class="bento-dark !p-8">
        <a href="{{ route('reports.show', $report) }}" class="text-xs font-semibold text-sand hover:underline">&larr; Kembali ke Detail Laporan</a>
        <h1 class="mt-3 text-2xl font-extrabold sm:text-3xl">Edit Laporan Kerusakan</h1>
        <p class="mt-1 text-xs text-cream/70 sm:text-sm">Hanya dapat diubah selama laporan belum diterima oleh admin/guru.</p>
    </div>

    <form action="{{ route('reports.update', $report) }}" method="POST" enctype="multipart/form-data" class="bento space-y-5 !p-6 sm:!p-8">
        @csrf
        @method('PUT')

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label class="label-bento">Tanggal Laporan</label>
                <input type="text" value="{{ $report->tanggal_laporan->format('d F Y') }}" readonly
                       class="input-bento cursor-not-allowed !bg-sand/20">
            </div>

            <div>
                <label class="label-bento">Tingkat Prioritas</label>
                <select name="prioritas" required class="input-bento">
                    <option value="rendah" @selected(old('prioritas', $report->prioritas) === 'rendah')>Rendah</option>
                    <option value="sedang" @selected(old('prioritas', $report->prioritas) === 'sedang')>Sedang</option>
                    <option value="tinggi" @selected(old('prioritas', $report->prioritas) === 'tinggi')>Tinggi</option>
                </select>
            </div>
        </div>

        <div>
            <label class="label-bento">Judul Kerusakan</label>
            <input type="text" name="judul" value="{{ old('judul', $report->judul) }}" required class="input-bento">
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label class="label-bento">Kategori Kerusakan</label>
                <select name="kategori" required class="input-bento">
                    @foreach($categories as $category)
                        <option value="{{ $category }}" @selected(old('kategori', $report->kategori) === $category)>{{ $category }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="label-bento">Lokasi / Ruangan</label>
                <input type="text" name="lokasi" value="{{ old('lokasi', $report->lokasi) }}" required class="input-bento">
            </div>
        </div>

        <div>
            <label class="label-bento">Deskripsi Kerusakan</label>
            <textarea name="deskripsi" rows="4" required class="input-bento">{{ old('deskripsi', $report->deskripsi) }}</textarea>
        </div>

        <div>
            <label class="label-bento">Ganti Foto Kerusakan (Opsional)</label>

            @if($report->foto_kerusakan)
                <div class="mb-3">
                    <p class="mb-1 text-xs text-forest/60">Foto saat ini:</p>
                    <img src="{{ asset('storage/' . $report->foto_kerusakan) }}" alt="Foto saat ini"
                         class="h-32 rounded-[1.5rem] object-cover">
                </div>
            @endif

            <input type="file" name="foto_kerusakan" id="foto_kerusakan"
                   accept="image/jpg,image/jpeg,image/png,image/webp"
                   class="input-bento file:mr-3 file:rounded-full file:border-0 file:bg-forest file:px-4 file:py-1.5 file:text-xs file:font-semibold file:text-cream">
            <p class="mt-1.5 text-xs text-forest/60">Kosongkan jika tidak ingin mengganti foto.</p>
            <img id="foto_preview" alt="Pratinjau foto baru" class="mt-3 hidden max-h-48 rounded-[1.5rem] object-cover">
        </div>

        <div class="flex items-center justify-end gap-3 border-t border-forest/10 pt-5">
            <a href="{{ route('reports.show', $report) }}" class="btn-ghost">Batal</a>
            <button type="submit" class="btn-primary">Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('foto_kerusakan')?.addEventListener('change', function (e) {
        const file = e.target.files[0];
        const img = document.getElementById('foto_preview');
        if (!file) { img.classList.add('hidden'); return; }
        img.src = URL.createObjectURL(file);
        img.classList.remove('hidden');
    });
</script>
@endpush