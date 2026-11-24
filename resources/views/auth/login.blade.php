@extends('layouts.app')

@section('title', 'Masuk — Sipras Manusa')

@section('content')
<div class="mx-auto grid max-w-4xl gap-4 py-4 md:grid-cols-5">

    {{-- Kartu brand --}}
    <section class="bento-dark relative flex min-h-[16rem] flex-col justify-between overflow-hidden !p-8 md:col-span-2">
        <div class="pointer-events-none absolute -right-12 -top-12 h-44 w-44 rounded-full bg-sage/40"></div>
        <div class="pointer-events-none absolute -bottom-8 -left-6 h-24 w-24 rounded-full bg-sand/30"></div>

        <div class="relative">
            <span class="flex h-12 w-12 items-center justify-center rounded-full bg-sand text-lg font-extrabold text-forest">S</span>
            <h1 class="mt-5 text-3xl font-extrabold leading-tight">
                Masuk ke <span class="text-sand">Sipras Manusa</span>
            </h1>
            <p class="mt-2 text-sm text-cream/70">Gunakan akun Siswa, Admin/Guru, atau Petugas.</p>
        </div>

        <div class="relative mt-8 flex flex-wrap gap-2">
            <span class="chip text-sand">Siswa</span>
            <span class="chip text-sand">Admin / Guru</span>
            <span class="chip text-sand">Petugas</span>
        </div>
    </section>

    {{-- Form login --}}
    <section class="bento !p-8 md:col-span-3">
        <h2 class="text-xl font-extrabold">Selamat datang kembali</h2>
        <p class="mt-1 text-xs text-forest/60">Masukkan email dan password untuk melanjutkan.</p>

        <form action="{{ route('login.post') }}" method="POST" class="mt-6 space-y-4">
            @csrf

            <div>
                <label class="label-bento">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                       placeholder="nama@sekolah.sch.id" class="input-bento">
            </div>

            <div>
                <label class="label-bento">Password</label>
                <input type="password" name="password" required placeholder="••••••••" class="input-bento">
            </div>

            <div class="flex items-center justify-between text-xs text-forest/70">
                <label class="inline-flex items-center gap-2">
                    <input type="checkbox" name="remember" class="h-4 w-4 rounded border-forest/30 accent-forest">
                    <span>Ingat saya</span>
                </label>
                <a href="{{ route('register') }}" class="font-semibold text-forest underline-offset-2 hover:underline">
                    Daftar akun siswa
                </a>
            </div>

            <button type="submit" class="btn-primary w-full !py-3">Masuk Sekarang</button>
        </form>
    </section>

    {{-- Akun uji coba: hanya tampil di environment local --}}
    @if(app()->environment('local'))
        <section class="bento-sand md:col-span-5">
            <p class="mb-2 text-sm font-bold">Akun Uji Coba (hanya tampil saat development)</p>
            <div class="grid gap-1 text-xs sm:grid-cols-3">
                <p><span class="font-semibold">Admin:</span> admin@sekolah.sch.id</p>
                <p><span class="font-semibold">Siswa:</span> siswa@sekolah.sch.id</p>
                <p><span class="font-semibold">Petugas:</span> petugas@sekolah.sch.id</p>
            </div>
            <p class="mt-2 text-xs text-forest/70">Password semua akun: <code class="font-semibold">password123</code></p>
        </section>
    @endif
</div>
@endsection