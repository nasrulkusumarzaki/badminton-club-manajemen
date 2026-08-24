<?php

namespace App\Http\Controllers;

use App\Models\HasilLatihan;
use App\Models\Atlet;
use App\Models\Program;
use Illuminate\Http\Request;

class HasilLatihanController extends Controller
{
    public function create(Request $request)
    {
        // allow prefilling atlet or program via query string
        $atlets = Atlet::orderBy('nama')->get();
        $programs = Program::orderBy('nama_program')->get();

        return view('layouts.hasil_latihan.create', compact('atlets', 'programs'));
    }

    public function store(Request $request)
    {
        $rules = [
            'atlet_id' => ['required', 'exists:atlets,id'],
            'program_id' => ['required', 'exists:programs,id'],
            'tanggal' => ['required', 'date'],
        ];

        for ($i = 1; $i <= 11; $i++) {
            $rules['nilai_set_' . $i] = ['nullable', 'numeric'];
        }

        $validated = $request->validate($rules);

        HasilLatihan::create($validated);

        return redirect()->route('atlets.show', $validated['atlet_id'])->with('status', 'Hasil latihan disimpan.');
    }

    // Optional: list recent results
    public function index(Request $request)
    {
        $q = HasilLatihan::with(['atlet', 'program'])->latest();
        $results = $q->paginate(20);
        return view('layouts.hasil_latihan.index', compact('results'));
    }
}
