@extends('layouts.bmc-app')

@section('page-title','Dashboard')

@section('content')
  <div class="bmc-grid">
    <div class="card">
      <div class="top-line" style="background:var(--bmc-navy)"></div>
      <div style="display:flex;align-items:center;justify-content:space-between">
        <div>
          <div class="stat-value">{{ \App\Models\Atlet::count() }}</div>
          <div class="stat-label">Total Atlet Aktif</div>
        </div>
        <i class="bi bi-people" style="font-size:48px;color:var(--bmc-navy);opacity:0.15"></i>
      </div>
    </div>
    <div class="card">
      <div class="top-line" style="background:var(--bmc-sky)"></div>
      <div style="display:flex;align-items:center;justify-content:space-between">
        <div>
          <div class="stat-value">{{ \App\Models\AbsensiAtlet::where('tanggal', today())->where('status', 'hadir')->count() }}/{{ \App\Models\Atlet::count() }}</div>
          <div class="stat-label">Hadir Hari Ini</div>
        </div>
        <i class="bi bi-check-circle" style="font-size:48px;color:var(--bmc-sky);opacity:0.15"></i>
      </div>
    </div>
    <div class="card">
      <div class="top-line" style="background:var(--bmc-accent)"></div>
      <div style="display:flex;align-items:center;justify-content:space-between">
        <div>
          <div class="stat-value">{{ \App\Models\Program::count() }}</div>
          <div class="stat-label">Program Aktif</div>
        </div>
        <i class="bi bi-calendar-event" style="font-size:48px;color:var(--bmc-accent);opacity:0.15"></i>
      </div>
    </div>
    <div class="card">
      <div class="top-line" style="background:#8b92a9"></div>
      <div style="display:flex;align-items:center;justify-content:space-between">
        <div>
          <div class="stat-value">{{ \App\Models\User::whereHas('roles')->count() }}</div>
          <div class="stat-label">Staff (Pelatih/Asisten)</div>
        </div>
        <i class="bi bi-person-badge" style="font-size:48px;color:#8b92a9;opacity:0.15"></i>
      </div>
    </div>
  </div>

  <div class="panel-row">
    <div class="card">
      @php
        $user = auth()->user();

        $atletsQuery = \App\Models\Atlet::query();
        if ($user && method_exists($user, 'hasAnyRole') && $user->hasAnyRole(['pelatih', 'asisten'])) {
          if ($user->level) $atletsQuery->where('level', $user->level);
        }

        // Ambil tanggal absensi TERAKHIR yang benar-benar pernah diinput
        // (bukan selalu "hari ini"), dibatasi ke atlet yang relevan dengan user ini.
        $relevantAtletIds = (clone $atletsQuery)->pluck('id');

        $tanggalTerakhirAbsen = \App\Models\AbsensiAtlet::whereIn('atlet_id', $relevantAtletIds)
            ->max('tanggal');

        // Ambil langsung dari record absensi pada tanggal terakhir itu,
        // supaya atlet yang ditampilkan memang atlet yang diabsen hari itu
        // (bukan 5 atlet pertama secara umum yang belum tentu diabsen).
        $attendanceRecords = $tanggalTerakhirAbsen
            ? \App\Models\AbsensiAtlet::where('tanggal', $tanggalTerakhirAbsen)
                ->whereIn('atlet_id', $relevantAtletIds)
                ->with('atlet')
                ->orderBy('atlet_id')
                ->get()
                ->filter(fn ($r) => $r->atlet !== null)
            : collect();

        $atlets = $attendanceRecords->pluck('atlet');
        $attendance = $attendanceRecords->keyBy('atlet_id');

        $absensiTitle = $tanggalTerakhirAbsen
            ? 'Absensi — ' . \Illuminate\Support\Carbon::parse($tanggalTerakhirAbsen)->translatedFormat('d F Y')
            : 'Absensi';
      @endphp

      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
        <h3 style="margin:0">{{ $absensiTitle }}</h3>
        <a href="{{ route('absensi.index', $tanggalTerakhirAbsen ? ['tanggal' => $tanggalTerakhirAbsen] : []) }}" style="color:var(--bmc-sky);font-size:14px;text-decoration:none">Lihat semua →</a>
      </div>

      @if(!$tanggalTerakhirAbsen || $atlets->isEmpty())
        <div class="empty-state">Belum ada absensi yang pernah diinput.</div>
      @else
        <table class="table">
          <thead>
            <tr>
              <th style="color:var(--bmc-muted);font-weight:600;text-transform:uppercase;font-size:12px">Atlet</th>
              <th style="color:var(--bmc-muted);font-weight:600;text-transform:uppercase;font-size:12px">Level</th>
              <th style="color:var(--bmc-muted);font-weight:600;text-transform:uppercase;font-size:12px">Status</th>
            </tr>
          </thead>
          <tbody>
            @foreach($atlets as $a)
              <tr>
                <td><div class="avatar">{{ strtoupper(substr($a->nama,0,1)) }}</div> {{ $a->nama }}</td>
                <td><span class="badge badge-{{ $a->level ?? 'pemula' }}">{{ ucfirst($a->level) }}</span></td>
                <td>
                  @if(isset($attendance[$a->id]))
                    @php
                      $status = $attendance[$a->id]->status;
                      $status_labels = ['hadir' => 'Hadir', 'tidak' => 'Tidak Hadir', 'izin' => 'Izin', 'sakit' => 'Sakit', 'alpha' => 'Alpha'];
                      $badge_classes = ['hadir' => '#10b981', 'tidak' => '#ef4444', 'izin' => '#3b82f6', 'sakit' => '#f59e0b', 'alpha' => '#ef4444'];
                      $status_label = $status_labels[$status] ?? 'Unknown';
                      $badge_color = $badge_classes[$status] ?? '#ef4444';
                    @endphp
                    <span style="display:inline-block;padding:6px 12px;border-radius:6px;background:{{ $badge_color }}15;color:{{ $badge_color }};font-size:13px;font-weight:500">{{ $status_label }}</span>
                  @else
                    <span class="text-muted small">-</span>
                  @endif
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      @endif
    </div>
    <div class="card">
      <h3 style="margin-top:0">Program Berjalan</h3>
      <ul style="list-style:none;padding-left:0;margin:0">
        @foreach(\App\Models\Program::limit(6)->get() as $p)
          <li style="padding:14px 0;border-bottom:1px solid var(--bmc-border);display:flex;justify-content:space-between;align-items:center">
            <div>
              <div style="font-weight:600;font-size:15px">{{ $p->nama_program }}</div>
              <div class="text-muted small">{{ $p->jenis }}</div>
            </div>
            <div style="color:var(--bmc-sky);font-weight:600">{{ $p->atlets->count() }} atlet</div>
          </li>
        @endforeach
      </ul>
    </div>
  </div>

@endsection