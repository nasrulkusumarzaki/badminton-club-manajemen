<?php

namespace App\Http\Controllers;

use App\Models\AbsensiAtlet;
use App\Models\Atlet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AbsensiController extends Controller
{
    public function index(Request $request)
    {
        $groups = [
            'pemula' => 'Pemula',
            'beginner' => 'Beginner',
            'senior' => 'Senior',
        ];

        $tanggal = $request->filled('tanggal')
            ? $request->input('tanggal')
            : now()->toDateString();

        $group = $request->filled('group') && array_key_exists($request->input('group'), $groups)
            ? $request->input('group')
            : 'pemula';

        $atlets = Atlet::where('level', $group)
            ->orderBy('nama')
            ->get();

        $attendance = AbsensiAtlet::where('tanggal', $tanggal)
            ->whereIn('atlet_id', $atlets->pluck('id'))
            ->pluck('status', 'atlet_id')
            ->toArray();

        return view('layouts.absensi.index', compact('groups', 'group', 'tanggal', 'atlets', 'attendance'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => ['required', 'date'],
            'group' => ['required', 'in:pemula,beginner,senior'],
            'status' => ['required', 'array'],
            'status.*' => ['required', 'in:hadir,izin,sakit,alpha'],
        ]);

        $atletIds = Atlet::where('level', $validated['group'])->pluck('id');

        foreach ($atletIds as $atletId) {
            if (! isset($validated['status'][$atletId])) {
                continue;
            }

            AbsensiAtlet::updateOrCreate(
                ['tanggal' => $validated['tanggal'], 'atlet_id' => $atletId],
                ['status' => $validated['status'][$atletId], 'dicatat_oleh' => Auth::id()]
            );
        }

        return redirect()
            ->route('absensi.index', ['tanggal' => $validated['tanggal'], 'group' => $validated['group']])
            ->with('status', 'Absensi berhasil disimpan.');
    }

    public function report(Request $request)
    {
        // filter by range and group
        $from = $request->input('from') ?: now()->subWeek()->toDateString();
        $to = $request->input('to') ?: now()->toDateString();
        $group = $request->input('group') ?: null;

        $query = AbsensiAtlet::whereBetween('tanggal', [$from, $to])->with(['atlet', 'pencatat']);
        if ($group) {
            $query->whereHas('atlet', function($q) use ($group) { $q->where('level', $group); });
        }

        $records = $query->orderBy('tanggal', 'desc')->get();

        return view('layouts.absensi.report', compact('records', 'from', 'to', 'group'));
    }

    public function export(Request $request)
    {
        $from = $request->input('from') ?: now()->subWeek()->toDateString();
        $to = $request->input('to') ?: now()->toDateString();
        $group = $request->input('group') ?: null;

        $query = AbsensiAtlet::whereBetween('tanggal', [$from, $to])->with(['atlet', 'pencatat']);
        if ($group) {
            $query->whereHas('atlet', function($q) use ($group) { $q->where('level', $group); });
        }
        $records = $query->orderBy('tanggal', 'desc')->get();

        $filename = "absensi_{$from}_to_{$to}.csv";
        $handle = fopen('php://memory', 'r+');
        fputcsv($handle, ['tanggal','atlet_id','nama','level','status','dicatat_oleh']);
        foreach ($records as $r) {
            fputcsv($handle, [$r->tanggal, $r->atlet_id, $r->atlet->nama, $r->atlet->level ?? '', $r->status, $r->pencatat->name ?? '']);
        }
        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }
}