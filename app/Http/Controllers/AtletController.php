<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Atlet;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class AtletController extends Controller
{
    /**
     * Tampilkan daftar semua atlet.
     */
    public function index(Request $request)
    {
        $query = Atlet::query()->latest();

        // If logged-in user is a coach or assistant, restrict to their assigned level
        $user = auth()->user();
        if ($user && method_exists($user, 'hasAnyRole') && $user->hasAnyRole(['pelatih', 'asisten'])) {
            if ($user->level) {
                $query->where('level', $user->level);
            }
        } else {
            // Filter by ?level if provided for other users
            if ($request->filled('level')) {
                $query->where('level', $request->string('level'));
            }
        }

        // Search simpel berdasarkan nama (opsional, lewat ?search=...)
        if ($request->filled('search')) {
            $query->where('nama', 'like', '%' . $request->string('search') . '%');
        }

        $atlets = $query->paginate(10)->withQueryString();
        return view('layouts.atlets.index', compact('atlets'));
    }
    /**
     * Tampilkan form tambah atlet.
     */
    public function create()
    {
        return view('layouts.atlets.create');
    }
    /**
     * Simpan atlet baru ke database.
     */
    public function store(Request $request)
    {
        $validated = $this->validasi($request);
 
        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('atlets', 'public');
        }
 
        Atlet::create($validated);
 
        return redirect()
            ->route('atlets.index')
            ->with('status', 'Data atlet "' . $validated['nama'] . '" berhasil ditambahkan.');
    }
    /**
     * Tampilkan detail satu atlet.
     */
    public function show(Atlet $atlet)
    {
        // Restrict viewing single atlet to coaches/assistants of same level
        $user = auth()->user();
        if ($user && method_exists($user, 'hasAnyRole') && $user->hasAnyRole(['pelatih', 'asisten'])) {
            if ($user->level && $atlet->level !== $user->level) {
                abort(403, 'Akses ditolak: atlet ini bukan di kelompok Anda.');
            }
        }

        // Load hasil latihan untuk chart & table
        $hasil = \App\Models\HasilLatihan::where('atlet_id', $atlet->id)
                    ->with('program')
                    ->orderBy('tanggal')
                    ->get();

        $programHistory = $hasil->groupBy('program_id')->map(function ($items, $programId) {
            return [
                'id' => (int) $programId,
                'nama' => $items->first()->program?->nama_program ?? 'Program ' . $programId,
                'last_tanggal' => $items->max(fn ($row) => $row->tanggal ? $row->tanggal->toDateString() : null),
            ];
        })->sortByDesc('last_tanggal')->values();

        $defaultProgramId = $programHistory->first()['id'] ?? null;

        // Prepare simplified data for Chart.js (avoid closures in Blade)
        $hasilForJs = $hasil->map(function($r){
            return [
                'program_id' => (int) ($r->program_id ?? 0),
                'program' => $r->program->nama_program ?? null,
                'tanggal' => $r->tanggal ? $r->tanggal->toDateString() : null,
                'values' => [
                    $r->nilai_set_1, $r->nilai_set_2, $r->nilai_set_3, $r->nilai_set_4, $r->nilai_set_5,
                    $r->nilai_set_6, $r->nilai_set_7, $r->nilai_set_8, $r->nilai_set_9, $r->nilai_set_10, $r->nilai_set_11
                ]
            ];
        })->toArray();

        return view('layouts.atlets.show', compact('atlet', 'hasil', 'hasilForJs', 'programHistory', 'defaultProgramId'));
    }
    /**
     * Tampilkan form edit atlet.
     */
    public function edit(Atlet $atlet)
    {
        return view('layouts.atlets.edit', compact('atlet'));
    }
    /**
     * Update data atlet.
     */
    public function update(Request $request, Atlet $atlet)
    {
        $validated = $this->validasi($request, $atlet->id);
 
        if ($request->hasFile('foto')) {
            // Hapus foto lama kalau ada, biar storage gak numpuk
            if ($atlet->foto) {
                Storage::disk('public')->delete($atlet->foto);
            }
            $validated['foto'] = $request->file('foto')->store('atlets', 'public');
        }
 
        $atlet->update($validated);
 
        return redirect()
            ->route('atlets.index')
            ->with('status', 'Data atlet "' . $atlet->nama . '" berhasil diperbarui.');
    }
    /**
     * Hapus data atlet.
     */
    public function destroy(Atlet $atlet)
    {
        if ($atlet->foto) {
            Storage::disk('public')->delete($atlet->foto);
        }
 
        $nama = $atlet->nama;
        $atlet->delete();
 
        return redirect()
            ->route('atlets.index')
            ->with('status', 'Data atlet "' . $nama . '" berhasil dihapus.');
    }
    /**
     * Aturan validasi form atlet (dipakai di store & update).
     */
    private function validasi(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'nama'   => ['required', 'string', 'max:100'],
            'umur'   => ['required', 'integer', 'min:5', 'max:80'],
            'jenis_kelamin'=> ['required', 'in:L,P'],
            'no_hp'  => ['required', 'string', 'max:20'],
            'level'  => ['required', 'in:pemula,beginner,senior'],
            'foto'   => ['nullable', 'image', 'max:2048'], // maks 2MB
        ]);
    }
}
