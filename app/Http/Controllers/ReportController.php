<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\ReportLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ReportController extends Controller
{
    public const CATEGORIES = [
        'Listrik & Pencahayaan',
        'Furniture Kelas',
        'Bangunan & Pintu/Jendela',
        'IT, Komputer & Proyektor',
        'Sanitasi & Air',
        'Lainnya',
    ];

    public function adminDashboard(): View
    {
        $counts = [
            'total' => Report::count(),
            'menunggu_verifikasi' => Report::where('status', 'menunggu_verifikasi')->count(),
            'diproses' => Report::whereIn('status', ['diterima', 'sedang_dikerjakan'])->count(),
            'menunggu_konfirmasi' => Report::where('status', 'menunggu_konfirmasi')->count(),
            'selesai' => Report::where('status', 'selesai')->count(),
            'ditolak' => Report::where('status', 'ditolak')->count(),
        ];

        // Urutan harus sama dengan label grafik di dashboard.blade.php
        $byStatus = [
            Report::where('status', 'menunggu_verifikasi')->count(),
            Report::where('status', 'diterima')->count(),
            Report::where('status', 'sedang_dikerjakan')->count(),
            Report::where('status', 'menunggu_konfirmasi')->count(),
            Report::where('status', 'selesai')->count(),
            Report::where('status', 'ditolak')->count(),
        ];

        $byPrioritas = [
            Report::where('prioritas', 'tinggi')->count(),
            Report::where('prioritas', 'sedang')->count(),
            Report::where('prioritas', 'rendah')->count(),
        ];

        $needsAction = Report::with(['pelapor', 'petugas'])
            ->whereIn('status', ['menunggu_verifikasi', 'menunggu_konfirmasi'])
            ->latest()
            ->take(8)
            ->get();

        return view('admin.dashboard', compact('counts', 'byStatus', 'byPrioritas', 'needsAction'));
    }

    public function index(Request $request): View
    {
        $user = $request->user();
        $query = Report::with(['pelapor', 'petugas'])->latest();

        if ($user->isSiswa()) {
            $query->where('user_id', $user->id);
        } elseif ($user->isPetugas()) {
            $query->where('petugas_id', $user->id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('prioritas')) {
            $query->where('prioritas', $request->prioritas);
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', 'like', '%' . $request->kategori . '%');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                    ->orWhere('lokasi', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        $reports = $query->paginate(10)->withQueryString();

        return view('reports.index', compact('reports'));
    }

    public function create(): View
    {
        $categories = self::CATEGORIES;

        return view('reports.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:160'],
            'kategori' => ['required', 'string', 'max:100'],
            'lokasi' => ['required', 'string', 'max:140'],
            'deskripsi' => ['required', 'string', 'min:10'],
            'prioritas' => ['required', 'in:rendah,sedang,tinggi'],
            'foto_kerusakan' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        $path = $request->file('foto_kerusakan')->store('reports/damage', 'public');

        $report = Report::create([
            'user_id' => $request->user()->id,
            'judul' => $validated['judul'],
            'kategori' => $validated['kategori'],
            'lokasi' => $validated['lokasi'],
            'deskripsi' => $validated['deskripsi'],
            'prioritas' => $validated['prioritas'],
            'tanggal_laporan' => now()->toDateString(),
            'foto_kerusakan' => $path,
            'status' => 'menunggu_verifikasi',
        ]);

        $this->recordLog(
            $report->id,
            $request->user()->id,
            'menunggu_verifikasi',
            'Laporan kerusakan dibuat oleh siswa dan menunggu verifikasi admin/guru.'
        );

        return redirect()->route('reports.show', $report)
            ->with('success', 'Laporan kerusakan berhasil dikirim dengan tanggal otomatis hari ini.');
    }

    public function show(Request $request, Report $report): View
    {
        $this->authorizeView($request->user(), $report);
        $report->load(['pelapor', 'petugas', 'logs.aktor']);

        $petugasList = $request->user()->isAdmin()
            ? User::where('role', 'petugas')->orderBy('name')->get()
            : collect();

        return view('reports.show', compact('report', 'petugasList'));
    }

    public function edit(Request $request, Report $report): View
    {
        $this->abortUnlessOwnerSiswa($request->user(), $report);

        if (! $report->canBeEditedOrDeletedBySiswa()) {
            abort(403, 'Laporan tidak dapat diedit karena sudah diproses atau diterima admin.');
        }

        $categories = self::CATEGORIES;

        return view('reports.edit', compact('report', 'categories'));
    }

    public function update(Request $request, Report $report): RedirectResponse
    {
        $user = $request->user();
        $this->abortUnlessOwnerSiswa($user, $report);

        if (! $report->canBeEditedOrDeletedBySiswa()) {
            return back()->with('error', 'Laporan sudah diterima/diproses admin sehingga tidak bisa diedit.');
        }

        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:160'],
            'kategori' => ['required', 'string', 'max:100'],
            'lokasi' => ['required', 'string', 'max:140'],
            'deskripsi' => ['required', 'string', 'min:10'],
            'prioritas' => ['required', 'in:rendah,sedang,tinggi'],
            'foto_kerusakan' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        if ($request->hasFile('foto_kerusakan')) {
            $this->deleteFile($report->foto_kerusakan);
            $validated['foto_kerusakan'] = $request->file('foto_kerusakan')->store('reports/damage', 'public');
        } else {
            unset($validated['foto_kerusakan']);
        }

        $report->update($validated);

        $this->recordLog(
            $report->id,
            $user->id,
            'menunggu_verifikasi',
            'Siswa memperbarui detail laporan sebelum diverifikasi admin.'
        );

        return redirect()->route('reports.show', $report)
            ->with('success', 'Laporan berhasil diperbarui.');
    }

    public function destroy(Request $request, Report $report): RedirectResponse
    {
        $this->abortUnlessOwnerSiswa($request->user(), $report);

        if (! $report->canBeEditedOrDeletedBySiswa()) {
            return back()->with('error', 'Laporan tidak bisa dihapus karena sudah diterima/diproses admin.');
        }

        $this->deleteFile($report->foto_kerusakan);
        $this->deleteFile($report->foto_bukti);

        $report->delete();

        return redirect()->route('reports.index')
            ->with('success', 'Laporan berhasil dihapus.');
    }

    // --- AKSI ADMIN / GURU ---

    public function acceptAndAssign(Request $request, Report $report): RedirectResponse
    {
        if (! in_array($report->status, ['menunggu_verifikasi', 'ditolak'], true)) {
            return back()->with('error', 'Laporan ini sudah diproses, penugasan tidak bisa diubah.');
        }

        $validated = $request->validate([
            'petugas_id' => ['required', 'exists:users,id'],
            'catatan_penugasan' => ['nullable', 'string', 'max:500'],
        ]);

        $petugas = User::where('id', $validated['petugas_id'])->where('role', 'petugas')->firstOrFail();

        $report->update([
            'status' => 'diterima',
            'petugas_id' => $petugas->id,
            'catatan_penolakan' => null,
        ]);

        $catatan = 'Laporan diterima admin dan ditugaskan kepada petugas: ' . $petugas->name;
        if (! empty($validated['catatan_penugasan'])) {
            $catatan .= ' — Catatan: ' . $validated['catatan_penugasan'];
        }

        $this->recordLog($report->id, $request->user()->id, 'diterima', $catatan);

        return back()->with('success', 'Laporan diterima dan ditugaskan ke ' . $petugas->name . '.');
    }

    public function rejectReport(Request $request, Report $report): RedirectResponse
    {
        if ($report->status !== 'menunggu_verifikasi') {
            return back()->with('error', 'Hanya laporan yang menunggu verifikasi yang bisa ditolak.');
        }

        $validated = $request->validate([
            'catatan_penolakan' => ['required', 'string', 'min:5', 'max:500'],
        ]);

        $report->update([
            'status' => 'ditolak',
            'catatan_penolakan' => $validated['catatan_penolakan'],
        ]);

        $this->recordLog(
            $report->id,
            $request->user()->id,
            'ditolak',
            'Laporan ditolak admin/guru. Alasan: ' . $validated['catatan_penolakan']
        );

        return back()->with('success', 'Laporan telah ditolak dengan catatan alasan.');
    }

    public function confirmCompletion(Request $request, Report $report): RedirectResponse
    {
        if ($report->status !== 'menunggu_konfirmasi') {
            return back()->with('error', 'Laporan belum berada pada tahap menunggu konfirmasi selesai.');
        }

        $validated = $request->validate([
            'catatan_admin' => ['nullable', 'string', 'max:500'],
        ]);

        $report->update([
            'status' => 'selesai',
            'completed_at' => now(),
        ]);

        $catatan = 'Admin/guru telah memeriksa foto bukti perbaikan dan menandai laporan SELESAI untuk siswa.';
        if (! empty($validated['catatan_admin'])) {
            $catatan .= ' Catatan: ' . $validated['catatan_admin'];
        }

        $this->recordLog($report->id, $request->user()->id, 'selesai', $catatan);

        return back()->with('success', 'Bukti perbaikan disetujui! Status SELESAI telah dikirim ke siswa.');
    }

    /**
     * Admin menghapus laporan apa pun secara permanen.
     * Karena akun siswa membaca tabel yang sama, laporan otomatis hilang dari akun siswa.
     */
    public function adminDestroy(Request $request, Report $report): RedirectResponse
    {
        $fotoKerusakan = $report->foto_kerusakan;
        $fotoBukti = $report->foto_bukti;

        // Riwayat dihapus dulu supaya tidak terhalang foreign key
        DB::transaction(function () use ($report) {
            ReportLog::where('report_id', $report->id)->delete();
            $report->delete();
        });

        // File foto dihapus setelah data berhasil terhapus
        $this->deleteFile($fotoKerusakan);
        $this->deleteFile($fotoBukti);

        return redirect()->route('reports.index')
            ->with('success', 'Laporan berhasil dihapus permanen.');
    }

    // --- AKSI PETUGAS ---

    /**
     * Petugas mengembalikan tugas ke admin (hanya saat status "diterima").
     * Menerima tugas = menekan "Kerjakan" (startWork).
     */
    public function respondAssignment(Request $request, Report $report): RedirectResponse
    {
        $user = $request->user();
        $this->abortUnlessAssignedPetugas($user, $report);

        if ($report->status !== 'diterima') {
            return back()->with('error', 'Tugas hanya bisa dikembalikan sebelum mulai dikerjakan.');
        }

        $validated = $request->validate([
            'keputusan' => ['required', 'in:tolak'],
            'alasan_tolak' => ['required', 'string', 'min:5', 'max:500'],
        ]);

        $report->update([
            'status' => 'menunggu_verifikasi',
            'petugas_id' => null,
            'catatan_penolakan' => 'Dikembalikan oleh petugas: ' . $validated['alasan_tolak'],
        ]);

        $this->recordLog(
            $report->id,
            $user->id,
            'menunggu_verifikasi',
            'Petugas mengembalikan tugas dengan alasan: ' . $validated['alasan_tolak']
        );

        return redirect()->route('reports.index')
            ->with('success', 'Tugas dikembalikan ke admin beserta alasannya.');
    }

    public function startWork(Request $request, Report $report): RedirectResponse
    {
        $user = $request->user();
        $this->abortUnlessAssignedPetugas($user, $report);

        if ($report->status !== 'diterima') {
            return back()->with('error', 'Tombol Kerjakan hanya tersedia setelah tugas diterima.');
        }

        $report->update(['status' => 'sedang_dikerjakan']);

        $this->recordLog(
            $report->id,
            $user->id,
            'sedang_dikerjakan',
            'Petugas menerima tugas dan mulai melakukan perbaikan.'
        );

        return back()->with('success', 'Status diubah menjadi Sedang Dikerjakan.');
    }

    public function submitCompletion(Request $request, Report $report): RedirectResponse
    {
        $user = $request->user();
        $this->abortUnlessAssignedPetugas($user, $report);

        if ($report->status !== 'sedang_dikerjakan') {
            return back()->with('error', 'Bukti perbaikan hanya bisa dikirim saat status Sedang Dikerjakan.');
        }

        $validated = $request->validate([
            'foto_bukti' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'keterangan_selesai' => ['required', 'string', 'min:5', 'max:1000'],
        ]);

        $this->deleteFile($report->foto_bukti);

        $buktiPath = $request->file('foto_bukti')->store('reports/proofs', 'public');

        $report->update([
            'status' => 'menunggu_konfirmasi',
            'foto_bukti' => $buktiPath,
            'keterangan_selesai' => $validated['keterangan_selesai'],
        ]);

        $this->recordLog(
            $report->id,
            $user->id,
            'menunggu_konfirmasi',
            'Petugas mengklik "Selesai" dan mengunggah foto bukti perbaikan untuk diperiksa admin.'
        );

        return back()->with('success', 'Bukti perbaikan berhasil dikirim! Menunggu pemeriksaan & konfirmasi dari admin.');
    }

    // --- HELPER ---

    private function authorizeView($user, Report $report): void
    {
        if ($user->isAdmin()) {
            return;
        }

        if ($user->isSiswa() && (int) $report->user_id === (int) $user->id) {
            return;
        }

        if ($user->isPetugas() && (int) $report->petugas_id === (int) $user->id) {
            return;
        }

        abort(403, 'Kamu tidak berhak melihat laporan ini.');
    }

    private function abortUnlessOwnerSiswa($user, Report $report): void
    {
        abort_unless($user->isSiswa() && (int) $report->user_id === (int) $user->id, 403);
    }

    private function abortUnlessAssignedPetugas($user, Report $report): void
    {
        abort_unless($user->isPetugas() && (int) $report->petugas_id === (int) $user->id, 403);
    }

    private function deleteFile(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    private function recordLog(int $reportId, ?int $userId, string $statusBaru, ?string $catatan = null): void
    {
        ReportLog::create([
            'report_id' => $reportId,
            'user_id' => $userId,
            'status_baru' => $statusBaru,
            'catatan' => $catatan,
        ]);
    }
}