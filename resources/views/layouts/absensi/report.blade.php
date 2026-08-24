@extends('layouts.bmc-app')

@section('page-title','Laporan Absensi')

@section('content')
  <div class="card">
    <div class="report-toolbar">
      <h2 class="report-title">Laporan Absensi</h2>

      <form method="GET" action="{{ route('absensi.report') }}" class="report-filters">
        <div class="date-filter">
          <label class="toolbar-label">Dari</label>
          <input type="date" name="from" value="{{ $from }}">
        </div>
        <div class="date-filter">
          <label class="toolbar-label">Sampai</label>
          <input type="date" name="to" value="{{ $to }}">
        </div>
        <select name="group" class="toolbar-select">
          <option value="">Semua Kelompok</option>
          <option value="pemula" @selected($group === 'pemula')>Pemula</option>
          <option value="beginner" @selected($group === 'beginner')>Beginner</option>
          <option value="senior" @selected($group === 'senior')>Senior</option>
        </select>
        <button type="submit" class="btn btn-navy">Tampilkan</button>
        <a href="{{ route('absensi.export', request()->only(['from','to','group'])) }}" class="btn btn-sky">Ekspor CSV</a>
      </form>
    </div>

    <div class="table-wrap">
      <table class="data-table">
        <thead>
          <tr>
            <th>Tanggal</th>
            <th>Atlet</th>
            <th>Kelompok</th>
            <th>Status</th>
            <th>Dicatat Oleh</th>
          </tr>
        </thead>
        <tbody>
          @forelse($records as $r)
            <tr>
              <td>{{ \Illuminate\Support\Carbon::parse($r->tanggal)->format('Y-m-d') }}</td>
              <td><strong>{{ $r->atlet->nama ?? '-' }}</strong></td>
              <td>
                <span class="chip
                    @if(($r->atlet->level ?? '')==='pemula') chip-pemula
                    @elseif(($r->atlet->level ?? '')==='beginner') chip-beginner
                    @else chip-senior
                    @endif">
                  {{ ucfirst($r->atlet->level ?? '-') }}
                </span>
              </td>
              <td>
                <span class="status-pill status-{{ $r->status }}">{{ ucfirst($r->status) }}</span>
              </td>
              <td>{{ $r->pencatat->name ?? '-' }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="5">
                <div class="empty-state">Tidak ada data absensi di rentang tanggal ini.</div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
@endsection