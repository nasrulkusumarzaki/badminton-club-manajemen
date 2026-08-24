@extends('layouts.bmc-app')

@section('page-title','Daftar Hasil Latihan')

@section('content')
  <div class="card">
    <div class="table-wrap">
      <table class="data-table">
        <thead>
          <tr>
            <th>Tanggal</th>
            <th>Atlet</th>
            <th>Program</th>
            <th>Rangkuman (Set1..Set11)</th>
            <th style="text-align:right;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($results as $r)
            <tr>
              <td>{{ $r->tanggal->format('d F Y') }}</td>
              <td><strong>{{ $r->atlet->nama ?? '-' }}</strong></td>
              <td>{{ $r->program->nama_program ?? '-' }}</td>
              <td>{{ collect(range(1,11))->map(fn($i)=> $r['nilai_set_'.$i] ?? '-')->implode(', ') }}</td>
              <td style="text-align:right;">-</td>
            </tr>
          @empty
            <tr>
              <td colspan="5">
                <div class="empty-state">Belum ada hasil latihan.</div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div style="margin-top:1rem;">
      {{ $results->links() }}
    </div>
  </div>
@endsection