<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GaleriController extends Controller
{
    public function index(Request $request): View
    {
        $reports = Report::where('status', 'selesai')
            ->when($request->filled('kategori'), fn ($q) => $q->where('kategori', $request->kategori))
            ->latest('completed_at')
            ->paginate(9)
            ->withQueryString();

        // Semua kategori selalu tampil, lengkap dengan jumlah perbaikan selesai
        $categories = ReportController::CATEGORIES;

        $counts = Report::where('status', 'selesai')
            ->selectRaw('kategori, count(*) as total')
            ->groupBy('kategori')
            ->pluck('total', 'kategori');

        $totalSelesai = Report::where('status', 'selesai')->count();

        return view('galeri.index', compact('reports', 'categories', 'counts', 'totalSelesai'));
    }
}