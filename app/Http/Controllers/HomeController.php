<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $total = Report::count();
        $selesai = Report::where('status', 'selesai')->count();

        // Kartu 1: progres penyelesaian
        $stats = [
            'total' => $total,
            'selesai' => $selesai,
            'aktif' => Report::whereIn('status', [
                'menunggu_verifikasi', 'diterima', 'sedang_dikerjakan', 'menunggu_konfirmasi',
            ])->count(),
            'persen' => $total > 0 ? (int) round($selesai / $total * 100) : 0,
        ];

        // Kartu 2: rata-rata waktu dari laporan dibuat sampai dikonfirmasi selesai
        $rataJam = Report::where('status', 'selesai')
            ->whereNotNull('completed_at')
            ->get(['created_at', 'completed_at'])
            ->avg(fn ($r) => abs($r->created_at->diffInMinutes($r->completed_at)) / 60);

        $waktuRata = null;
        if ($rataJam !== null) {
            $waktuRata = $rataJam < 24
                ? ['angka' => round($rataJam, 1), 'satuan' => 'jam']
                : ['angka' => round($rataJam / 24, 1), 'satuan' => 'hari'];
        }

        // Kartu 4: berubah sesuai peran pengguna (null = belum login)
        $roleCard = null;
        $user = auth()->user();

        if ($user) {
            if ($user->isAdmin()) {
                $roleCard = [
                    'type' => 'admin',
                    'verifikasi' => Report::where('status', 'menunggu_verifikasi')->count(),
                    'konfirmasi' => Report::where('status', 'menunggu_konfirmasi')->count(),
                ];
            } elseif ($user->isPetugas()) {
                $roleCard = [
                    'type' => 'petugas',
                    'baru' => Report::where('petugas_id', $user->id)->where('status', 'diterima')->count(),
                    'dikerjakan' => Report::where('petugas_id', $user->id)->where('status', 'sedang_dikerjakan')->count(),
                ];
            } else {
                $roleCard = [
                    'type' => 'siswa',
                    'latest' => Report::where('user_id', $user->id)->latest()->first(),
                ];
            }
        }

        $recentCompleted = Report::where('status', 'selesai')
            ->latest('completed_at')
            ->take(3)
            ->get();

        return view('home', compact('stats', 'waktuRata', 'roleCard', 'recentCompleted'));
    }
}