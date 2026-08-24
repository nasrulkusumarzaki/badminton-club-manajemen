<aside class="bmc-sidebar">
  <div>
    <a href="{{ route('dashboard') }}" class="bmc-logo">
      <img src="{{ asset('images/logo-round.png') }}" alt="BMC">
      <div class="text">
        <div class="bmc-app-name">BMC</div>
        <div class="bmc-app-sub">Badminton Manager Club</div>
      </div>
    </a>

    <div style="margin-top:24px;padding-top:12px;border-top:1px solid rgba(255,255,255,0.1)">
      <div style="font-size:11px;font-weight:700;color:rgba(255,255,255,0.5);text-transform:uppercase;letter-spacing:0.8px;margin-bottom:12px">MENU</div>
      <nav class="bmc-nav">
        <span class="nav-indicator"></span>
        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
          <i class="bi bi-speedometer2"></i><span>Dashboard</span>
        </a>
        <a href="{{ route('atlets.index') }}" class="{{ request()->routeIs('atlets.index') ? 'active' : '' }}">
          <i class="bi bi-people"></i><span>Data Atlet</span>
        </a>
        <a href="{{ route('absensi.index') }}" class="{{ request()->routeIs('absensi.index') ? 'active' : '' }}">
          <i class="bi bi-check2-square"></i><span>Absensi</span>
        </a>
        <a href="{{ route('programs.index') }}" class="{{ request()->routeIs('programs.index') ? 'active' : '' }}">
          <i class="bi bi-journal-text"></i><span>Program Latihan</span>
        </a>
      </nav>
    </div>

    <div style="margin-top:16px;padding-top:12px;border-top:1px solid rgba(255,255,255,0.1)">
      <div style="font-size:11px;font-weight:700;color:rgba(255,255,255,0.5);text-transform:uppercase;letter-spacing:0.8px;margin-bottom:12px">LAINNYA</div>
      <nav class="bmc-nav">
        <span class="nav-indicator"></span>
        <a href="{{ route('absensi.report') }}" class="{{ request()->routeIs('absensi.report') ? 'active' : '' }}">
          <i class="bi bi-file-earmark-text"></i><span>Riwayat Absensi</span>
        </a>
      </nav>
    </div>
  </div>

  <a href="{{ route('profile.edit') }}" class="bmc-user" style="text-decoration:none; color:inherit;">
    @if (auth()->user()->foto ?? null)
      <img src="{{ \Illuminate\Support\Facades\Storage::url(auth()->user()->foto) }}" alt="user"
           style="width:40px;height:40px;border-radius:999px;object-fit:cover">
    @else
      <div style="display:flex;align-items:center;justify-content:center;width:40px;height:40px;border-radius:999px;background:linear-gradient(135deg,#29ABE2,#3b82f6);color:#fff;font-weight:700;font-size:16px">
        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
      </div>
    @endif
    <div class="meta">
      <div class="name" style="color: #ffffff;">{{ auth()->user()->name ?? 'Guest' }}</div>
      <div class="role" style="color: rgba(255,255,255,0.7);">{{ optional(auth()->user())->getRoleNames()->first() ?? '-' }} • {{ optional(auth()->user())->level ?? '-' }}</div>
    </div>
  </a>
</aside>

<script>
document.addEventListener('DOMContentLoaded', function () {
    function initNavIndicators() {
        document.querySelectorAll('.bmc-nav').forEach(nav => {
            let indicator = nav.querySelector('.nav-indicator');
            if (!indicator) return;

            const activeItem = nav.querySelector('a.active');

            function moveTo(item) {
                if (!item) return;
                // Menggunakan getBoundingClientRect agar akurat tanpa terpengaruh margin/padding offset
                const navRect = nav.getBoundingClientRect();
                const itemRect = item.getBoundingClientRect();
                
                const topPos = (itemRect.top - navRect.top) + (itemRect.height / 2) - (32 / 2); // 32px tinggi indikator
                
                indicator.style.top = topPos + 'px';
                indicator.style.opacity = '1';
            }

            if (activeItem) {
                moveTo(activeItem);
            } else {
                indicator.style.opacity = '0';
            }

            nav.querySelectorAll('a').forEach(item => {
                item.addEventListener('mouseenter', () => moveTo(item));
                // Pada klik di perangkat mobile, tutup menu agar konten terlihat
                item.addEventListener('click', () => {
                    if (document.body.querySelector('.bmc-shell').classList.contains('menu-open')) {
                        document.body.querySelector('.bmc-shell').classList.remove('menu-open');
                    }
                });
            });

            nav.addEventListener('mouseleave', () => {
                if (activeItem) {
                    moveTo(activeItem);
                } else {
                    indicator.style.opacity = '0';
                }
            });
        });
    }

    // Jalankan kalkulasi posisi setelah font & layout selesai di-render
    initNavIndicators();
    window.addEventListener('resize', initNavIndicators);

    // Mobile menu toggle
    const mobileBtn = document.getElementById('mobile-menu-btn');
    const shell = document.querySelector('.bmc-shell');

    function closeMenuOnOutsideClick(e) {
        if (!shell) return;
        if (!shell.classList.contains('menu-open')) return;
        const sidebar = shell.querySelector('.bmc-sidebar');
        if (!sidebar) return;
        if (!sidebar.contains(e.target) && !mobileBtn.contains(e.target)) {
            shell.classList.remove('menu-open');
            document.body.style.overflow = '';
            document.removeEventListener('click', closeMenuOnOutsideClick);
        }
    }

    if (mobileBtn && shell) {
        mobileBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            const opening = !shell.classList.contains('menu-open');
            shell.classList.toggle('menu-open');
            if (opening) {
                // pasang listener global untuk menutup ketika klik luar
                document.body.style.overflow = 'hidden';
                setTimeout(() => document.addEventListener('click', closeMenuOnOutsideClick), 50);
            } else {
                document.body.style.overflow = '';
                document.removeEventListener('click', closeMenuOnOutsideClick);
            }
        });

        // Pastikan setiap klik link di nav menutup menu (mobile)
        document.querySelectorAll('.bmc-nav a').forEach(a => {
            a.addEventListener('click', () => {
                if (shell.classList.contains('menu-open')) {
                    shell.classList.remove('menu-open');
                    document.body.style.overflow = '';
                    document.removeEventListener('click', closeMenuOnOutsideClick);
                }
            });
        });
    }

});
</script>