<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Masuk - BMC</title>
  <link rel="stylesheet" href="{{ asset('css/bmc.css') }}">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>
<body class="login-hero">
  <div class="login-card" role="main" aria-labelledby="login-title">
    <div style="display:flex;align-items:center;gap:14px;margin-bottom:20px">
      <img src="{{ asset('images/logo-round.png') }}" alt="BMC" style="width:56px;height:56px;border-radius:12px">
      <div>
        <div style="font-family:'Plus Jakarta Sans',sans-serif;font-weight:700;color:var(--bmc-navy)">BMC</div>
        <div class="text-muted small">BADMINTON CLUB MANAGER</div>
      </div>
    </div>

    <h2 id="login-title" style="margin:0 0 8px 0;font-family:'Plus Jakarta Sans',sans-serif;font-size:28px">Masuk ke akun kamu</h2>
    <p class="text-muted small" style="margin-bottom:18px">Khusus pelatih & asisten pelatih PB Sumber Maju.</p>

    @if ($errors->any())
      <div style="background:#fff6f6;border:1px solid #ffd6d6;padding:10px;border-radius:8px;margin-bottom:12px;color:#9b1c1c">
        <strong>Terdapat kesalahan:</strong>
        <ul style="margin:6px 0 0 16px;padding:0">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ route('login') }}" novalidate>
      @csrf

      <div style="margin-bottom:14px">
        <label class="small" for="email">Email</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
               style="width:100%;padding:14px;border-radius:12px;border:1px solid var(--bmc-border);margin-top:6px;font-size:15px">
      </div>

      <div style="margin-bottom:18px">
        <label class="small" for="password">Password</label>
        <div class="input-with-icon" style="margin-top:6px">
          <input id="password" name="password" type="password" required
                 style="width:100%;padding:14px;border-radius:12px;border:1px solid var(--bmc-border);font-size:15px">
          <button type="button" id="toggle-password" aria-label="Tampilkan password" title="Tampilkan password" class="input-icon-btn">
            <i class="bi bi-eye" id="toggle-icon" style="font-size:18px"></i>
          </button>
        </div>
      </div>

      <div style="display:flex;justify-content:center;margin-top:6px">
        <button type="submit" class="btn btn-primary" style="width:100%;padding:16px;border-radius:12px;font-size:18px;background:var(--bmc-accent);box-shadow:0 10px 30px rgba(247,148,29,0.18);color:#fff;border:none">Masuk</button>
      </div>

      <div style="text-align:center;margin-top:14px">
        @if (Route::has('password.request'))
          <a href="{{ route('password.request') }}" class="text-muted small">Lupa password? Hubungi admin club.</a>
        @endif
      </div>
    </form>
  </div>

  <script>
    (function(){
      const pwd = document.getElementById('password');
      const btn = document.getElementById('toggle-password');
      const icon = document.getElementById('toggle-icon');
      if(!pwd || !btn || !icon) return;
      btn.addEventListener('click', function(){
        if(pwd.type === 'password'){
          pwd.type = 'text';
          icon.classList.remove('bi-eye');
          icon.classList.add('bi-eye-slash');
          btn.setAttribute('aria-label', 'Sembunyikan password');
          btn.setAttribute('title', 'Sembunyikan password');
        } else {
          pwd.type = 'password';
          icon.classList.remove('bi-eye-slash');
          icon.classList.add('bi-eye');
          btn.setAttribute('aria-label', 'Tampilkan password');
          btn.setAttribute('title', 'Tampilkan password');
        }
      });
    })();
  </script>
</body>
</html>