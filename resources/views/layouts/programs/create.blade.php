@extends('layouts.bmc-app')

@section('page-title','Buat Program Latihan')

@section('content')
  <div class="card" style="max-width:640px;">
    <h2 style="margin:0 0 1.2rem;font-family:'Plus Jakarta Sans',sans-serif;">Buat Program Latihan</h2>

    <form method="POST" action="{{ route('programs.store') }}">
      @csrf

      <div class="form-group">
        <label>Nama Program</label>
        <input type="text" name="nama_program" value="{{ old('nama_program') }}" required>
        @error('nama_program') <p class="field-error">{{ $message }}</p> @enderror
      </div>

      <div class="form-group">
        <label>Jenis</label>
        <input type="text" name="jenis" value="{{ old('jenis') }}" placeholder="Contoh: Footwork, Smash, Servis">
        @error('jenis') <p class="field-error">{{ $message }}</p> @enderror
      </div>

      <div class="form-group">
        <label>Level</label>
        <select name="level">
          <option value="">-- Tidak spesifik --</option>
          <option value="pemula" @selected(old('level')==='pemula')>Pemula</option>
          <option value="beginner" @selected(old('level')==='beginner')>Beginner</option>
          <option value="senior" @selected(old('level')==='senior')>Senior</option>
        </select>
        @error('level') <p class="field-error">{{ $message }}</p> @enderror
      </div>

      <div class="form-group">
        <label>Tanggal</label>
        <input type="date" name="tanggal" value="{{ old('tanggal') }}" style="max-width:220px;">
        @error('tanggal') <p class="field-error">{{ $message }}</p> @enderror
      </div>

      <div class="form-group">
        <label>Pilih Atlet (assign ke program)</label>
        <p class="field-hint">Pilih level di atas untuk otomatis menandai semua atlet di level itu.</p>
        <select name="atlets[]" multiple size="6" class="multi-select">
          @foreach($atlets as $a)
            <option value="{{ $a->id }}" data-level="{{ $a->level }}" @selected(collect(old('atlets'))->contains($a->id))>{{ $a->nama }} ({{ $a->level }})</option>
          @endforeach
        </select>
        @error('atlets') <p class="field-error">{{ $message }}</p> @enderror
        @error('atlets.*') <p class="field-error">{{ $message }}</p> @enderror
      </div>

      <div class="form-group">
        <label>Deskripsi</label>
        <textarea name="deskripsi" rows="4">{{ old('deskripsi') }}</textarea>
        @error('deskripsi') <p class="field-error">{{ $message }}</p> @enderror
      </div>

      <div class="form-footer">
        <button type="submit" class="btn-orange">Simpan Program</button>
        <a href="{{ route('programs.index') }}" class="btn btn-outline">Batal</a>
      </div>
    </form>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
        const levelSelect = document.querySelector('select[name="level"]');
        const atletsSelect = document.querySelector('select[name="atlets[]"]');

        if (!levelSelect || !atletsSelect) return;

        function applyGroupSelection(value) {
            if (!value) {
                for (const o of atletsSelect.options) o.selected = false;
                return;
            }
            for (const o of atletsSelect.options) {
                o.selected = (o.dataset.level === value);
            }
        }

        if (levelSelect.value) {
            applyGroupSelection(levelSelect.value);
        }

        levelSelect.addEventListener('change', function () {
            applyGroupSelection(this.value);
        });
    });
  </script>
@endsection