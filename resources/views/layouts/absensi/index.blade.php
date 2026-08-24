@extends('layouts.bmc-app')

@section('page-title','Absensi Atlet')

@section('content')
  <div class="card">
    <div class="page-header">
      <div>
        <h3 style="margin:0;font-family:'Plus Jakarta Sans',sans-serif;">Absensi Atlet</h3>
        <div class="subtitle">Pilihan tanggal dan kelompok untuk mencatat kehadiran atlet.</div>
      </div>
      <form method="GET" action="{{ route('absensi.index') }}" class="date-filter">
        <input type="date" name="tanggal" value="{{ $tanggal }}">
        <button type="submit" class="btn-navy">Tampilkan</button>
      </form>
    </div>

    @if(session('status'))
      <div class="alert-success">{{ session('status') }}</div>
    @endif

    <div class="level-tabs">
      @foreach($groups as $key => $label)
        <a href="{{ route('absensi.index', ['tanggal' => $tanggal, 'group' => $key]) }}"
           class="{{ $group === $key ? 'active' : '' }}">
          {{ $label }}
        </a>
      @endforeach
    </div>

    <form method="POST" action="{{ route('absensi.store') }}">
      @csrf
      <input type="hidden" name="tanggal" value="{{ $tanggal }}">
      <input type="hidden" name="group" value="{{ $group }}">

      <div class="table-wrap">
        <table class="data-table">
          <thead>
            <tr>
              <th>Nama Atlet</th>
              <th>Umur</th>
              <th>No. HP</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            @forelse($atlets as $atlet)
              @php
                $statusSekarang = old('status.'.$atlet->id, $attendance[$atlet->id] ?? '');
              @endphp
              <tr>
                <td>{{ $atlet->nama }}</td>
                <td>{{ $atlet->umur }} th</td>
                <td>{{ $atlet->no_hp }}</td>
                <td>
                  <div class="status-toggle">
                    <label>
                      <input type="radio" name="status[{{ $atlet->id }}]" value="hadir" @checked($statusSekarang === 'hadir')>
                      <span class="pill pill-hadir">Hadir</span>
                    </label>
                    <label>
                      <input type="radio" name="status[{{ $atlet->id }}]" value="izin" @checked($statusSekarang === 'izin')>
                      <span class="pill pill-izin">Izin</span>
                    </label>
                    <label>
                      <input type="radio" name="status[{{ $atlet->id }}]" value="sakit" @checked($statusSekarang === 'sakit')>
                      <span class="pill pill-sakit">Sakit</span>
                    </label>
                    <label>
                      <input type="radio" name="status[{{ $atlet->id }}]" value="alpha" @checked($statusSekarang === 'alpha')>
                      <span class="pill pill-alpha">Alpha</span>
                    </label>
                  </div>
                  @error('status.'.$atlet->id)
                    <p style="color:#c0392b;font-size:.75rem;margin-top:.3rem;">{{ $message }}</p>
                  @enderror
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="4">
                  <div class="empty-state">Tidak ada atlet di kelompok {{ $groups[$group] }}.</div>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="form-footer">
        <button type="submit" class="btn-orange">Simpan Absensi</button>
        <p class="info-note">Tanggal: {{ $tanggal }}</p>
      </div>
    </form>
  </div>
@endsection