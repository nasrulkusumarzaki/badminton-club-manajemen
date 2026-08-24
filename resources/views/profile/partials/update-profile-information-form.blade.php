<section>
    <header style="margin-bottom:1.2rem;">
        <h3 style="margin:0 0 .3rem;font-family:'Plus Jakarta Sans',sans-serif;font-size:1.1rem;">
            Informasi Profile
        </h3>
        <p class="field-hint" style="margin:0;">
            Perbarui nama, email, foto profil, dan password kamu.
        </p>
    </header>

    @if (session('status') === 'profile-updated')
        <div class="alert-success">Profil berhasil diperbarui.</div>
    @endif

    @if (session('status') === 'profile-update-failed')
        <div class="alert-danger">Gagal menyimpan profil. Coba lagi nanti.</div>
    @endif

    @if ($errors->any())
        <div class="alert-danger">
            <ul style="margin:0;padding-left:1rem;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <div class="form-group">
            <label>Foto Profil</label>
            <div style="display:flex;align-items:center;gap:1rem;margin-bottom:.6rem;">
                @if ($user->foto ?? null)
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($user->foto) }}"
                         class="avatar-lg" style="object-fit:cover;">
                @else
                    <div class="avatar-lg">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                @endif
                <div>
                    <input type="file" name="foto" accept="image/*" class="file-input">
                    <p class="field-hint" style="margin-top:.4rem;">Format gambar, maksimal 2MB. Opsional.</p>
                </div>
            </div>
            @error('foto') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        <div class="form-group">
            <label>Nama</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" required autocomplete="name">
            @error('name') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" required autocomplete="username">
            @error('email') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <p class="field-hint" style="margin-top:.6rem;">
                Email kamu belum diverifikasi.
                <button form="send-verification" class="link-inline">Kirim ulang email verifikasi.</button>
            </p>

            @if (session('status') === 'verification-link-sent')
                <p class="field-hint" style="color:#1c8a4a;margin-top:.4rem;">
                    Link verifikasi baru sudah dikirim ke email kamu.
                </p>
            @endif
        @endif

        <hr style="margin:1.4rem 0;border:none;border-top:1px solid #e5e7eb;">

        <h4 style="margin:0 0 .3rem;font-family:'Plus Jakarta Sans',sans-serif;font-size:1rem;">
            Ubah Password
        </h4>
        <p class="field-hint" style="margin:0 0 .8rem;">
            Kosongkan jika kamu tidak ingin mengubah password. Gunakan password yang panjang dan acak biar tetap aman.
        </p>

        <div class="form-group">
            <label>Password Baru</label>
            <div class="input-with-icon">
                <input type="password" name="password" autocomplete="new-password" data-password-field>
                <button type="button" class="input-icon-btn" data-toggle-password aria-label="Lihat password">
                    <i class="bi bi-eye"></i>
                </button>
            </div>
            @error('password') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        <div class="form-group">
            <label>Konfirmasi Password Baru</label>
            <div class="input-with-icon">
                <input type="password" name="password_confirmation" autocomplete="new-password" data-password-field>
                <button type="button" class="input-icon-btn" data-toggle-password aria-label="Lihat password">
                    <i class="bi bi-eye"></i>
                </button>
            </div>
        </div>

        <div class="form-group">
            <label>Password Saat Ini</label>
            <div class="input-with-icon">
                <input type="password" name="current_password" autocomplete="current-password" data-password-field placeholder="Masukkan password lama jika ingin mengubah email atau password">
                <button type="button" class="input-icon-btn" data-toggle-password aria-label="Lihat password">
                    <i class="bi bi-eye"></i>
                </button>
            </div>
            <p class="field-hint" style="margin-top:.4rem;">Diperlukan saat mengubah email atau password.</p>
            @error('current_password') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        <div class="form-footer">
            <button type="submit" class="btn btn-orange">Simpan</button>
        </div>
    </form>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>
</section>

<script>
    document.querySelectorAll('[data-toggle-password]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const field = btn.parentElement.querySelector('[data-password-field]');
            const icon = btn.querySelector('i');
            if (!field) return;

            const isHidden = field.type === 'password';
            field.type = isHidden ? 'text' : 'password';
            icon.classList.toggle('bi-eye', !isHidden);
            icon.classList.toggle('bi-eye-slash', isHidden);
        });
    });
</script>