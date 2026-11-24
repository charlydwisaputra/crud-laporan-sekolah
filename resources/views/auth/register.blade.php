@extends('layouts.app')

@section('title', 'Daftar Siswa — ')
Sipras Manusa
@section('content')
<div class="mx-auto grid max-w-4xl gap-4 py-4 md:grid-cols-5">

    {{-- Kartu brand --}}
    <section class="bento-sage relative flex min-h-[16rem] flex-col justify-between overflow-hidden !p-8 md:col-span-2">
        <div class="pointer-events-none absolute -right-12 -top-12 h-44 w-44 rounded-full bg-forest/30"></div>
        <div class="pointer-events-none absolute -bottom-8 -left-6 h-24 w-24 rounded-full bg-sand/40"></div>

        <div class="relative">
            <span class="flex h-12 w-12 items-center justify-center rounded-full bg-forest text-lg font-extrabold text-cream">S</span>
            <h1 class="mt-5 text-3xl font-extrabold leading-tight">Daftar Akun Siswa</h1>
            <p class="mt-2 text-sm text-cream/85">Buat akun untuk melaporkan kerusakan fasilitas sekolah dan memantau penanganannya.</p>
        </div>

        <ul class="relative mt-8 space-y-1.5 text-xs text-cream/90">
            <li>&bull; Lapor lengkap dengan foto</li>
            <li>&bull; Pantau status secara langsung</li>
            <li>&bull; Riwayat aktivitas transparan</li>
        </ul>
    </section>

    {{-- Form daftar --}}
    <section class="bento !p-8 md:col-span-3">
        <form action="{{ route('register.post') }}" method="POST" class="space-y-4">
            @csrf

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="label-bento">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="input-bento">
                </div>

                <div>
                    <label class="label-bento">Kelas</label>
                    <input type="text" name="kelas_atau_unit" value="{{ old('kelas_atau_unit') }}"
                           placeholder="Contoh: XI RPL 1" required class="input-bento">
                </div>
            </div>

            <div>
                <label class="label-bento">Email Sekolah</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                       placeholder="nama@sekolah.sch.id" class="input-bento">
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="label-bento">Password</label>
                    <input type="password" name="password" required class="input-bento">
                </div>

                <div>
                    <label class="label-bento">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" required class="input-bento">
                </div>
            </div>

            <button type="submit" class="btn-primary w-full !py-3">Daftar Akun Siswa</button>
        </form>

        <p class="mt-5 text-center text-xs text-forest/70">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="font-semibold text-forest underline-offset-2 hover:underline">Masuk di sini</a>
        </p>
    </section>
</div>
@endsection