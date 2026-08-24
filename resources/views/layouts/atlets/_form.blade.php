@csrf

<div class="form-group">
    <label>Nama Atlet</label>
    <input type="text" name="nama" value="{{ old('nama', $atlet->nama ?? '') }}"
           placeholder="Contoh: Budi Santoso" required>
    @error('nama') <p class="field-error">{{ $message }}</p> @enderror
</div>

<div class="form-group">
    <label>Umur</label>
    <input type="number" name="umur" value="{{ old('umur', $atlet->umur ?? '') }}"
           min="5" max="80" required>
    @error('umur') <p class="field-error">{{ $message }}</p> @enderror
</div>

<div class="form-group">
    <label>Jenis Kelamin</label>
    <select name="jenis_kelamin" required>
        <option value="">-- Pilih jenis kelamin --</option>
        <option value="L" @selected(old('jenis_kelamin', $atlet->jenis_kelamin ?? '') === 'L')>Laki-laki</option>
        <option value="P" @selected(old('jenis_kelamin', $atlet->jenis_kelamin ?? '') === 'P')>Perempuan</option>
    </select>
    @error('jenis_kelamin') <p class="field-error">{{ $message }}</p> @enderror
</div>

<div class="form-group">
    <label>No. HP (Orang Tua/Wali)</label>
    <input type="text" name="no_hp" value="{{ old('no_hp', $atlet->no_hp ?? '') }}"
           placeholder="08xxxxxxxxxx" required>
    @error('no_hp') <p class="field-error">{{ $message }}</p> @enderror
</div>

<div class="form-group">
    <label>Level / Kelompok</label>
    <select name="level" required>
        <option value="">-- Pilih level --</option>
        <option value="pemula" @selected(old('level', $atlet->level ?? '') === 'pemula')>Pemula</option>
        <option value="beginner" @selected(old('level', $atlet->level ?? '') === 'beginner')>Beginner</option>
        <option value="senior" @selected(old('level', $atlet->level ?? '') === 'senior')>Senior</option>
    </select>
    @error('level') <p class="field-error">{{ $message }}</p> @enderror
</div>

<div class="form-group">
    <label>Foto Atlet</label>

    @if(isset($atlet) && $atlet->fotoUrl())
        <div style="margin-bottom:.8rem;">
            <img src="{{ $atlet->fotoUrl() }}" style="width:64px;height:64px;border-radius:50%;object-fit:cover;display:block;">
            <p class="field-hint" style="margin-top:.4rem;">Foto saat ini. Upload foto baru untuk mengganti.</p>
        </div>
    @endif

    <input type="file" name="foto" accept="image/*" class="file-input">
    <p class="field-hint">Format gambar, maksimal 2MB. Opsional.</p>
    @error('foto') <p class="field-error">{{ $message }}</p> @enderror
</div>

<div class="form-footer">
    <button type="submit" class="btn btn-orange">Simpan</button>
    <a href="{{ route('atlets.index') }}" class="btn btn-outline">Batal</a>
</div>