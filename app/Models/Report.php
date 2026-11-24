<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'petugas_id',
        'judul',
        'kategori',
        'lokasi',
        'deskripsi',
        'prioritas',
        'tanggal_laporan',
        'foto_kerusakan',
        'status',
        'catatan_penolakan',
        'foto_bukti',
        'keterangan_selesai',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_laporan' => 'date',
            'completed_at' => 'datetime',
        ];
    }

    public function pelapor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(ReportLog::class)->latest();
    }

    public function canBeEditedOrDeletedBySiswa(): bool
    {
        return $this->status === 'menunggu_verifikasi';
    }

    /**
     * Label status yang bisa dipakai di mana saja (termasuk riwayat log).
     */
    public static function labelFor(?string $status): string
    {
        return match ($status) {
            'menunggu_verifikasi' => 'Menunggu Verifikasi',
            'ditolak' => 'Ditolak',
            'diterima' => 'Diterima & Ditugaskan',
            'sedang_dikerjakan' => 'Sedang Dikerjakan',
            'menunggu_konfirmasi' => 'Menunggu Konfirmasi Admin',
            'selesai' => 'Selesai',
            default => ucfirst(str_replace('_', ' ', (string) $status)),
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return self::labelFor($this->status);
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'menunggu_verifikasi' => 'bg-sand/30 text-forest border-sand',
            'ditolak' => 'bg-clay/10 text-clay border-clay/40',
            'diterima' => 'bg-sage/20 text-forest border-sage/50',
            'sedang_dikerjakan' => 'bg-sage text-cream border-sage',
            'menunggu_konfirmasi' => 'bg-forest/10 text-forest border-forest/40',
            'selesai' => 'bg-forest text-cream border-forest',
            default => 'bg-forest/5 text-forest/70 border-forest/20',
        };
    }

    public function getPrioritasBadgeClassAttribute(): string
    {
        return match ($this->prioritas) {
            'tinggi' => 'bg-clay text-cream border-clay',
            'sedang' => 'bg-sand/40 text-forest border-sand',
            'rendah' => 'bg-sage/20 text-forest border-sage/50',
            default => 'bg-forest/5 text-forest/70 border-forest/20',
        };
    }
}