<section>
    <header style="margin-bottom:1.2rem;">
        <h3 style="margin:0 0 .3rem;font-family:'Plus Jakarta Sans',sans-serif;font-size:1.1rem;color:#c0392b;">
            Hapus Akun
        </h3>
        <p class="field-hint" style="margin:0;">
            Setelah akun dihapus, semua data terkait akan hilang permanen. Unduh data penting sebelum lanjut.
        </p>
    </header>

    <button type="button" id="btn-show-delete" class="btn" style="background:#fbe4e4;color:#c0392b;">
        Hapus Akun
    </button>

    <div id="delete-confirm-box" style="display:none;margin-top:1.2rem;padding:1rem;background:#fff5f5;border:1px solid #fbe4e4;border-radius:10px;">
        <form method="post" action="{{ route('profile.destroy') }}">
            @csrf
            @method('delete')

            <div class="form-group">
                <label>Masukkan password untuk konfirmasi</label>
                <input type="password" name="password" placeholder="Password">
                @error('password', 'userDeletion') <p class="field-error">{{ $message }}</p> @enderror
            </div>

            <div class="form-footer">
                <button type="submit" class="btn" style="background:#c0392b;color:#fff;"
                        onclick="return confirm('Yakin mau hapus akun ini? Tindakan ini tidak bisa dibatalkan.');">
                    Ya, Hapus Akun Saya
                </button>
                <button type="button" id="btn-cancel-delete" class="btn btn-outline">Batal</button>
            </div>
        </form>
    </div>
</section>

<script>
    (function(){
        const showBtn = document.getElementById('btn-show-delete');
        const cancelBtn = document.getElementById('btn-cancel-delete');
        const box = document.getElementById('delete-confirm-box');
        if (!showBtn || !box) return;

        showBtn.addEventListener('click', function(){
            box.style.display = 'block';
            showBtn.style.display = 'none';
        });
        if (cancelBtn) {
            cancelBtn.addEventListener('click', function(){
                box.style.display = 'none';
                showBtn.style.display = 'inline-flex';
            });
        }
    })();
</script>