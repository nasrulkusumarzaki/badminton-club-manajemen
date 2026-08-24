@extends('layouts.bmc-app')

@section('page-title','Input Hasil Latihan')

@section('content')
  <div class="card" style="max-width:640px;">
    <form method="POST" action="{{ route('hasil_latihan.store') }}">
      @csrf

      <div class="form-group">
        <label>Atlet</label>
        <select name="atlet_id" required>
          <option value="">-- Pilih atlet --</option>
          @foreach($atlets as $a)
            <option value="{{ $a->id }}" @selected(old('atlet_id') == $a->id)>{{ $a->nama }} ({{ $a->level }})</option>
          @endforeach
        </select>
        @error('atlet_id') <p class="field-error">{{ $message }}</p> @enderror
      </div>

      <div class="form-group">
        <label>Program</label>
        <select name="program_id" required>
          <option value="">-- Pilih program --</option>
          @foreach($programs as $p)
            <option value="{{ $p->id }}" @selected(old('program_id') == $p->id)>{{ $p->nama_program }} @if($p->level) ({{ $p->level }}) @endif</option>
          @endforeach
        </select>
        @error('program_id') <p class="field-error">{{ $message }}</p> @enderror
      </div>

      <div class="form-group">
        <label>Tanggal</label>
        <input type="date" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" style="max-width:220px;" required>
        @error('tanggal') <p class="field-error">{{ $message }}</p> @enderror
      </div>

      <div class="form-group">
        <label>Nilai per Set</label>
        <div class="field-grid">
          @for($i=1;$i<=11;$i++)
            <div class="mini-field">
              <label>Set {{ $i }}</label>
              <input type="number" step="0.01" name="nilai_set_{{ $i }}" value="{{ old('nilai_set_'.$i) }}">
              @error('nilai_set_'.$i) <p class="field-error">{{ $message }}</p> @enderror
            </div>
          @endfor
        </div>
      </div>

      <div class="form-footer">
        <button type="submit" class="btn btn-orange">Simpan Hasil</button>
        <a href="{{ route('atlets.index') }}" class="btn btn-outline">Batal</a>
      </div>
    </form>
  </div>
@endsection