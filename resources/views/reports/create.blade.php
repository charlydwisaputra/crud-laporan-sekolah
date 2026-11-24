@extends('layouts.app')

@section('title', 'Buat Laporan Kerusakan — Sipras Manusa')

@section('content')
<div class="mx-auto max-w-3xl space-y-4">
    <div class="bento-dark !p-8">
        <a href="{{ route('reports.index') }}" class="text-xs font-semibold text-sand hover:underline">&larr; Kembali ke Daftar Laporan</a>
        <h1 class="mt-3 text-2xl font-extrabold sm:text-3xl">Form Pelaporan Kerusakan</h1>
        <p class="mt-1 text-xs text-cream/70 sm:text-sm">Lengkapi detail kerusakan fasilitas sekolah. Tanggal laporan terisi otomatis hari ini.</p>
    </div>

    <form action="{{ route('reports.store') }}" method="POST" enctype="multipart/form-data" class="bento space-y-5 !p-6 sm:!p-8">
        @csrf

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label class="label-bento">Tanggal Laporan (Otomatis)</label>
                <input type="text" value="{{ now()->translatedFormat('d F Y') }}" readonly
                       class="input-bento cursor-not-allowed !bg-sand/20">
            </div>

            <div>
                <label class="label-bento">Tingkat Prioritas</label>
                <select name="prioritas" required class="input-bento">
                    <option value="rendah" @selected(old('prioritas') === 'rendah')>Rendah</option>
                    <option value="sedang" @selected(old('prioritas', 'sedang') === 'sedang')>Sedang</option>
                    <option value="tinggi" @selected(old('prioritas') === 'tinggi')>Tinggi</option>
                </select>
            </div>
        </div>

        <div>
            <label class="label-bento">Judul Kerusakan</label>
            <input type="text" name="judul" value="{{ old('judul') }}" required
                   placeholder="Contoh: Kipas angin kelas XI RPL 1 mati" class="input-bento">
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label class="label-bento">Kategori Kerusakan</label>
                <select name="kategori" required class="input-bento">
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($categories as $category)
                        <option value="{{ $category }}" @selected(old('kategori') === $category)>{{ $category }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="label-bento">Lokasi / Ruangan</label>
                <input type="text" name="lokasi" value="{{ old('lokasi') }}" required
                       placeholder="Contoh: Ruang Kelas XI RPL 1" class="input-bento">
            </div>
        </div>

        <div>
            <label class="label-bento">Deskripsi Kerusakan</label>
            <textarea name="deskripsi" rows="4" required
                      placeholder="Jelaskan kondisi kerusakan secara jelas agar mudah ditangani..."
                      class="input-bento">{{ old('deskripsi') }}</textarea>
        </div>

        <div>
            <label class="label-bento">Foto Kerusakan (Wajib)</label>
            <input type="file" name="foto_kerusakan" id="foto_kerusakan"
                   accept="image/jpg,image/jpeg,image/png,image/webp" required
                   class="input-bento file:mr-3 file:rounded-full file:border-0 file:bg-forest file:px-4 file:py-1.5 file:text-xs file:font-semibold file:text-cream">
            <p class="mt-1.5 text-xs text-forest/60">Format JPG, JPEG, PNG, atau WEBP. Maksimal 4 MB.</p>
            <img id="foto_preview" alt="Pratinjau foto" class="mt-3 hidden max-h-48 rounded-[1.5rem] object-cover">
        </div>

        <div class="flex items-center justify-end gap-3 border-t border-forest/10 pt-5">
            <a href="{{ route('reports.index') }}" class="btn-ghost">Batal</a>
            <button type="submit" class="btn-primary">Kirim Laporan</button>
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