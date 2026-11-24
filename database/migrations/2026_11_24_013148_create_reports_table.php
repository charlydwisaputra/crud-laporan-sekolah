<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('petugas_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('judul');
            $table->string('kategori');
            $table->string('lokasi');
            $table->text('deskripsi');
            $table->enum('prioritas', ['rendah', 'sedang', 'tinggi'])->default('sedang');
            $table->date('tanggal_laporan');
            $table->string('foto_kerusakan');

            $table->enum('status', [
                'menunggu_verifikasi',
                'ditolak',
                'diterima',
                'sedang_dikerjakan',
                'menunggu_konfirmasi',
                'selesai',
            ])->default('menunggu_verifikasi');

            $table->text('catatan_penolakan')->nullable();
            $table->string('foto_bukti')->nullable();
            $table->text('keterangan_selesai')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        Schema::table('report_logs', function (Blueprint $table) {
            $table->foreign('report_id')
                ->references('id')
                ->on('reports')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('report_logs', function (Blueprint $table) {
            $table->dropForeign(['report_id']);
        });

        Schema::dropIfExists('reports');
    }
};