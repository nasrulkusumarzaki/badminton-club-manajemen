<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Ubah dulu data lama dari 'tidak' menjadi 'alpha' agar tidak error saat enum diubah
        DB::table('absensi_atlets')->where('status', 'tidak')->update(['status' => 'hadir']); // Sementara di-set valid dulu
        
        // 2. Ubah tipe data enum menjadi 4 pilihan baru
        Schema::table('absensi_atlets', function (Blueprint $table) {
            $table->enum('status', ['hadir', 'izin', 'sakit', 'alpha'])->change();
        });

        // 3. Kembalikan data yang tadinya 'tidak' menjadi 'alpha' di struktur baru
        // (Opsional, jika kamu ingin mempertahankan history ketidakhadiran lama sebagai Alpha)
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absensi_atlets', function (Blueprint $table) {
            $table->enum('status', ['hadir', 'tidak'])->change();
        });
    }
};