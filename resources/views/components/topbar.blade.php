<div class="bmc-topbar">
  <div class="bmc-topbar-left">
    <!-- Tombol hamburger untuk layar kecil -->
    <button id="mobile-menu-btn" class="mobile-menu-btn" aria-label="Menu" type="button">&#9776;</button>
    <div class="bmc-topbar-title">
      <h1>@yield('page-title', 'Dashboard')</h1>
      <div class="text-muted small">{{ now()->translatedFormat('l, d F Y') }}</div>
    </div>
  </div>

  <div class="bmc-topbar-right">
    @if (auth()->user()->foto ?? null)
      <a href="{{ route('profile.edit') }}" class="bmc-avatar-link">
        <img src="{{ \Illuminate\Support\Facades\Storage::url(auth()->user()->foto) }}" alt="user" class="bmc-avatar-img">
      </a>
    @else
      <a href="{{ route('profile.edit') }}" class="bmc-avatar-link">
        <div class="bmc-avatar-fallback">
          {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
        </div>
      </a>
    @endif

    <div class="bmc-user-block">
      <a href="{{ route('profile.edit') }}" class="bmc-user-name">{{ auth()->user()->name ?? 'Guest' }}</a>
      <div class="bmc-user-role">{{ optional(auth()->user())->getRoleNames()->first() ?? '-' }}</div>
      <!-- Profile dropdown toggle - di bawah nama user -->
      <button id="profile-toggle" class="profile-toggle" aria-expanded="false" aria-label="Open profile menu">&#9662;</button>
    </div>

    <!-- Profile menu (initially hidden) -->
    <div id="profile-menu" class="profile-menu" style="display:none">
      <a href="{{ route('profile.edit') }}" class="profile-menu-item">Lihat Profil</a>
      <form action="{{ route('logout') }}" method="POST" class="profile-menu-form">
        @csrf
        <button type="submit" class="profile-menu-logout">Keluar</button>
      </form>
    </div>
  </div>
</div>

<style>
  .bmc-topbar {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: nowrap;
  }

  .bmc-topbar-left {
    display: flex;
    align-items: center;
    gap: 8px;
    min-width: 0; /* penting: biar judul bisa truncate, bukan mendorong layout */
    flex: 1 1 auto;
  }

  .mobile-menu-btn {
    background: none;
    border: none;
    font-size: 22px;
    padding: 6px;
    cursor: pointer;
    color: var(--bmc-navy-deep);
    flex: 0 0 auto;
    line-height: 1;
  }

  .bmc-topbar-title {
    display: flex;
    flex-direction: column;
    min-width: 0;
  }

  .bmc-topbar-title h1 {
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-weight: 700;
    margin: 0;
    font-size: 1.25rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .bmc-topbar-right {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    position: relative;
    flex: 0 0 auto;
  }

  .bmc-avatar-link { display: block; flex: 0 0 auto; }

  .bmc-avatar-img,
  .bmc-avatar-fallback {
    width: 40px;
    height: 40px;
    border-radius: 999px;
    object-fit: cover;
  }

  .bmc-avatar-fallback {
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #29ABE2, #3b82f6);
    color: #fff;
    font-weight: 700;
    font-size: 15px;
  }

  .bmc-user-block {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    line-height: 1.3;
    min-width: 0;
  }

  .bmc-user-name {
    font-weight: 600;
    font-size: 14px;
    color: var(--bmc-text);
    text-decoration: none;
    max-width: 140px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .bmc-user-role {
    font-size: 12px;
    color: var(--bmc-muted);
    margin-top: 2px;
  }

  .profile-toggle {
    background: none;
    border: none;
    font-size: 14px;
    cursor: pointer;
    padding: 4px 0;
    margin-top: 4px;
    color: var(--bmc-navy-deep);
  }

  .profile-menu {
    position: absolute;
    top: 100%;
    right: 0;
    margin-top: 6px;
    background: #fff;
    border: 1px solid var(--bmc-border, #e5e7eb);
    border-radius: 10px;
    box-shadow: 0 8px 24px rgba(18, 60, 105, 0.12);
    min-width: 180px;
    z-index: 40;
    overflow: hidden;
  }

  .profile-menu-item {
    display: block;
    padding: 10px 14px;
    color: var(--bmc-text);
    text-decoration: none;
    font-size: 13.5px;
  }

  .profile-menu-form {
    padding: 8px 12px;
    margin: 0;
  }

  .profile-menu-logout {
    width: 100%;
    background: none;
    border: 1px solid var(--bmc-border, #e5e7eb);
    padding: 8px;
    border-radius: 8px;
    color: var(--bmc-text);
    cursor: pointer;
  }

  /* ==== Mobile ==== */
  @media (max-width: 480px) {
    .bmc-topbar {
      flex-wrap: nowrap; /* tetap satu baris, jangan turun ke bawah */
      align-items: center;
    }

    .bmc-topbar-title h1 {
      font-size: .95rem;
      max-width: 38vw; /* cegah judul panjang mendorong avatar keluar layar */
    }

    .bmc-topbar-title .text-muted.small {
      font-size: 10.5px;
    }

    .bmc-topbar-right {
      gap: 6px;
      align-items: center;
    }

    .bmc-avatar-img,
    .bmc-avatar-fallback {
      width: 30px;
      height: 30px;
      font-size: 12px;
    }

    .bmc-user-block {
      min-width: 0;
    }

    .bmc-user-name {
      max-width: 74px;
      font-size: 11.5px;
    }

    .bmc-user-role {
      font-size: 9.5px;
      margin-top: 1px;
    }

    .profile-toggle {
      font-size: 11px;
      margin-top: 2px;
    }

    .profile-menu {
      right: -8px;
      min-width: 160px;
      top: calc(100% + 4px);
    }
  }

  /* Layar sedikit lebih lega (481-600px): nama tetap tampil tapi diperkecil & dipendekkan */
  @media (min-width: 481px) and (max-width: 600px) {
    .bmc-topbar-title h1 { font-size: 1rem; max-width: 42vw; }
    .bmc-user-name { max-width: 80px; font-size: 12.5px; }
    .bmc-user-role { font-size: 10.5px; }
    .bmc-avatar-img, .bmc-avatar-fallback { width: 32px; height: 32px; }
  }
</style>

<script>
  // Profile dropdown toggle
  document.addEventListener('DOMContentLoaded', function () {
    const toggle = document.getElementById('profile-toggle');
    const menu = document.getElementById('profile-menu');

    function closeMenu() {
      if (!menu) return;
      menu.style.display = 'none';
      if (toggle) toggle.setAttribute('aria-expanded', 'false');
      document.removeEventListener('click', onDocClick);
    }

    function onDocClick(e) {
      if (!menu) return;
      if (menu.contains(e.target) || (toggle && toggle.contains(e.target))) return;
      closeMenu();
    }

    if (toggle && menu) {
      toggle.addEventListener('click', function (e) {
        e.stopPropagation();
        const opened = menu.style.display === 'block';
        if (opened) {
          closeMenu();
        } else {
          menu.style.display = 'block';
          toggle.setAttribute('aria-expanded', 'true');
          setTimeout(() => document.addEventListener('click', onDocClick), 50);
        }
      });
    }
  });
</script>