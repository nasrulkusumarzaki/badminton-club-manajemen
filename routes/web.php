<?php

use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProgramController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AtletController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Absensi: pelatih dan asisten dapat melihat & mencatat
    Route::get('absensi', [AbsensiController::class, 'index'])->name('absensi.index')->middleware('role:pelatih,asisten');
    Route::post('absensi', [AbsensiController::class, 'store'])->name('absensi.store')->middleware('role:pelatih,asisten');
    Route::get('absensi/report', [AbsensiController::class, 'report'])->name('absensi.report')->middleware('role:pelatih,asisten');
    Route::get('absensi/export', [AbsensiController::class, 'export'])->name('absensi.export')->middleware('role:pelatih,asisten');

    // Atlets: restrict create/store to pelatih first (prevent resource {atlet} catching 'create')
    Route::get('atlets/create', [AtletController::class, 'create'])->name('atlets.create')->middleware('role:pelatih');
    Route::post('atlets', [AtletController::class, 'store'])->name('atlets.store')->middleware('role:pelatih');
    // Then register other resource routes (exclude create & store)
    Route::resource('atlets', AtletController::class)->except(['create', 'store']);

    // ================= Khusus Route Programs =================
    // 1) Khusus Pelatih: create & store didaftarkan DULUAN
    Route::middleware('role:pelatih')->group(function () {
        Route::get('programs/create', [ProgramController::class, 'create'])->name('programs.create');
        Route::post('programs', [ProgramController::class, 'store'])->name('programs.store');
    });

    // 2) Bisa diakses Pelatih & Asisten Pelatih (hanya melihat)
    Route::get('programs', [ProgramController::class, 'index'])->name('programs.index')->middleware('role:pelatih,asisten');
    Route::get('programs/{program}', [ProgramController::class, 'show'])->name('programs.show')->middleware('role:pelatih,asisten');

    // 3) Khusus Pelatih: edit, update, destroy (aman ditaruh setelah show,
    //    karena pakai {program}/edit -> jumlah segmennya beda, tidak akan bentrok)
    Route::middleware('role:pelatih')->group(function () {
        Route::get('programs/{program}/edit', [ProgramController::class, 'edit'])->name('programs.edit');
        Route::put('programs/{program}', [ProgramController::class, 'update'])->name('programs.update');
        Route::delete('programs/{program}', [ProgramController::class, 'destroy'])->name('programs.destroy');
    });
    // =========================================================

    // Hasil latihan: pelatih dan asisten dapat input dan melihat daftar
    Route::get('hasil-latihan/create', [\App\Http\Controllers\HasilLatihanController::class, 'create'])->name('hasil_latihan.create')->middleware('role:pelatih,asisten');
    Route::post('hasil-latihan', [\App\Http\Controllers\HasilLatihanController::class, 'store'])->name('hasil_latihan.store')->middleware('role:pelatih,asisten');
    Route::get('hasil-latihan', [\App\Http\Controllers\HasilLatihanController::class, 'index'])->name('hasil_latihan.index')->middleware('role:pelatih,asisten');

    // Debug route to show current user's roles (temporary) — visit /_whoami while logged in
    Route::get('_whoami', function () {
        $user = auth()->user();
        if (! $user) return response('Not authenticated', 401);
        return response()->json([
            'id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
            'level' => $user->level ?? null,
            'roles' => method_exists($user, 'getRoleNames') ? $user->getRoleNames() : []
        ]);
    })->middleware('auth');
});

require __DIR__.'/auth.php';