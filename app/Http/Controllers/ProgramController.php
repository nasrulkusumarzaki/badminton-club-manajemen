<?php

namespace App\Http\Controllers;

use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class ProgramController extends Controller
{
    public function index()
    {
        $programs = Program::orderBy('tanggal', 'desc')->paginate(10);
        return view('layouts.programs.index', compact('programs'));
    }

    public function create()
    {
        $atlets = \App\Models\Atlet::orderBy('nama')->get();
        return view('layouts.programs.create', compact('atlets'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_program' => ['required', 'string', 'max:191'],
            'deskripsi' => ['nullable', 'string'],
            'jenis' => ['nullable', 'string', 'max:100'],
            'tanggal' => ['nullable', 'date'],
            'level' => ['nullable', 'in:pemula,beginner,senior'],
            'atlets' => ['nullable', 'array'],
            'atlets.*' => ['integer', 'exists:atlets,id'],
        ]);

        $program = Program::create(Arr::except($validated, ['atlets']));

        // Sync assigned athletes if provided
        if (!empty($validated['atlets'])) {
            $program->atlets()->sync($validated['atlets']);
        }

        return redirect()->route('programs.index')->with('status', 'Program latihan berhasil dibuat.');
    }

    public function show(Program $program)
    {
        return view('layouts.programs.show', compact('program'));
    }

    public function edit(Program $program)
    {
        $atlets = \App\Models\Atlet::orderBy('nama')->get();
        return view('layouts.programs.edit', compact('program', 'atlets'));
    }

    public function update(Request $request, Program $program)
    {
        $validated = $request->validate([
            'nama_program' => ['required', 'string', 'max:191'],
            'deskripsi' => ['nullable', 'string'],
            'jenis' => ['nullable', 'string', 'max:100'],
            'tanggal' => ['nullable', 'date'],
            'level' => ['nullable', 'in:pemula,beginner,senior'],
            'atlets' => ['nullable', 'array'],
            'atlets.*' => ['integer', 'exists:atlets,id'],
        ]);

        $program->update(Arr::except($validated, ['atlets']));

        // Sync assigned athletes
        $program->atlets()->sync($validated['atlets'] ?? []);

        return redirect()->route('programs.index')->with('status', 'Program latihan berhasil diperbarui.');
    }

    public function destroy(Program $program)
    {
        $program->delete();
        return redirect()->route('programs.index')->with('status', 'Program latihan dihapus.');
    }
}
