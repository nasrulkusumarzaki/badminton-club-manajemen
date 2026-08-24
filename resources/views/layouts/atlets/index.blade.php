@extends('layouts.bmc-app')

@section('page-title','Data Atlet')

@section('content')
  <div class="card">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
      <h2 style="margin:0;font-family:'Plus Jakarta Sans',sans-serif;">Data Atlet</h2>
      @if(auth()->check() && method_exists(auth()->user(), 'hasAnyRole') && auth()->user()->hasAnyRole(['pelatih']))
        <a href="{{ route('atlets.create') }}" class="btn btn-primary">+ Tambah Atlet</a>
      @endif
    </div>

    @if (session('status'))
      <div class="alert-success">{{ session('status') }}</div>
    @endif

    <form method="GET" class="filter-form">
      <div class="filter-field">
        <label>Cari nama</label>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari atlet...">
      </div>
      <div class="filter-field">
        <label>Level</label>
        <select name="level">
          <option value="">Semua level</option>
          <option value="pemula" @selected(request('level')==='pemula')>Pemula</option>
          <option value="beginner" @selected(request('level')==='beginner')>Beginner</option>
          <option value="senior" @selected(request('level')==='senior')>Senior</option>
        </select>
      </div>
      <button type="submit" class="btn btn-outline">Filter</button>
      @if(request('search') || request('level'))
        <a href="{{ route('atlets.index') }}" class="btn btn-outline">Reset</a>
      @endif
    </form>

    <div class="table-wrap">
      <table class="data-table">
        <thead>
          <tr>
            <th>Foto</th>
            <th>Nama</th>
            <th>Umur</th>
            <th>Jenis Kelamin</th>
            <th>No. HP</th>
            <th>Level</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($atlets as $atlet)
            <tr>
              <td>
                <span class="avatar-sm">
                  @if ($atlet->fotoUrl())
                    <img src="{{ $atlet->fotoUrl() }}" alt="{{ $atlet->nama }}">
                  @else
                    {{ strtoupper(substr($atlet->nama, 0, 1)) }}
                  @endif
                </span>
              </td>
              <td>
                <a href="{{ route('atlets.show', $atlet) }}" class="row-link">{{ $atlet->nama }}</a>
              </td>
              <td>{{ $atlet->umur }} th</td>
              <td>{{ $atlet->jenis_kelamin === 'L' ? 'Laki-laki' : ($atlet->jenis_kelamin === 'P' ? 'Perempuan' : '-') }}</td>
              <td>{{ $atlet->no_hp }}</td>
              <td>
                <span class="chip
                    @if($atlet->level==='pemula') chip-pemula
                    @elseif($atlet->level==='beginner') chip-beginner
                    @else chip-senior
                    @endif">
                  {{ $atlet->levelLabel() }}
                </span>
              </td>
              <td>
                <div class="action-links">
                  <a href="{{ route('atlets.edit', $atlet) }}" class="edit-link">Edit</a>
                  <form action="{{ route('atlets.destroy', $atlet) }}" method="POST"
                        onsubmit="return confirm('Yakin mau hapus data {{ $atlet->nama }}?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="delete-btn">Hapus</button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7">
                <div class="empty-state">Belum ada data atlet.</div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div style="margin-top:1rem;">
      {{ $atlets->links() }}
    </div>
  </div>
@endsection