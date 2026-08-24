@extends('layouts.bmc-app')

@section('page-title','Program Latihan')

@section('content')
  <div class="card">
    <div class="page-header">
      <h2 style="margin:0;font-family:'Plus Jakarta Sans',sans-serif;">Program Latihan</h2>
      @role('pelatih')
        <a href="{{ route('programs.create') }}" class="btn-orange" style="text-decoration: none;">+ Tambah Program</a>
      @endrole
    </div>

    @if(session('status'))
      <div class="alert-success">{{ session('status') }}</div>
    @endif

    <div class="table-wrap">
      <table class="data-table">
        <thead>
          <tr>
            <th>Nama Program</th>
            <th>Jenis</th>
            <th>Level</th>
            <th>Deskripsi</th>
            <th>Atlet</th>
            <th>Tanggal</th>
            <th style="text-align:right;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($programs as $program)
            @php
              $namaAtletFull = $program->atlets->pluck('nama');
              $tampil = $namaAtletFull->take(3);
              $sisa = $namaAtletFull->count() - $tampil->count();
              $deskripsi = trim((string) $program->deskripsi);
              $deskripsiPendek = $deskripsi !== '' ? \Illuminate\Support\Str::limit($deskripsi, 60) : null;

              $detailPayload = [
                  'nama_program' => $program->nama_program,
                  'jenis' => $program->jenis,
                  'level' => $program->level,
                  'deskripsi' => $deskripsi !== '' ? $deskripsi : null,
                  'tanggal' => optional($program->tanggal)->translatedFormat('d F Y'),
                  'atlet' => $namaAtletFull->values(),
              ];
            @endphp
            <tr class="program-row" data-program="{{ json_encode($detailPayload) }}" style="cursor:pointer;">
              <td><strong>{{ $program->nama_program }}</strong></td>
              <td><span class="tag-jenis">{{ $program->jenis }}</span></td>
              <td>
                <span class="chip
                    @if($program->level==='pemula') chip-pemula
                    @elseif($program->level==='beginner') chip-beginner
                    @else chip-senior
                    @endif">
                  {{ ucfirst($program->level) }}
                </span>
              </td>
              <td style="max-width:220px;">
                @if($deskripsiPendek)
                  {{ $deskripsiPendek }}
                @else
                  <span class="empty-state" style="padding:0;">-</span>
                @endif
              </td>
              <td>
                <div class="atlet-tags">
                  @forelse($tampil as $nama)
                    <span>{{ $nama }}</span>
                  @empty
                    <span class="empty-state" style="padding:0;">Belum ada atlet</span>
                  @endforelse
                  @if($sisa > 0)
                    <span class="more">+{{ $sisa }} lainnya</span>
                  @endif
                </div>
              </td>
              <td>{{ optional($program->tanggal)->format('d F Y') }}</td>
              <td>
                <div class="action-links" style="justify-content:flex-end;" onclick="event.stopPropagation();">
                  @role('pelatih')
                    <a href="{{ route('programs.edit', $program) }}" class="edit-link">Edit</a>
                    <form action="{{ route('programs.destroy', $program) }}" method="POST"
                          onsubmit="return confirm('Yakin mau hapus program ini?');">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="delete-btn">Hapus</button>
                    </form>
                  @endrole
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7">
                <div class="empty-state">Belum ada program latihan.</div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div style="margin-top:1rem;">
      {{ $programs->links() }}
    </div>
  </div>

  {{-- Modal detail program --}}
  <div id="programDetailOverlay" class="program-modal-overlay" style="display:none;">
    <div class="program-modal" role="dialog" aria-modal="true">
      <button type="button" class="program-modal-close" id="programDetailClose" aria-label="Tutup">&times;</button>

      <h3 id="pdNamaProgram" class="program-modal-title"></h3>

      <div class="program-modal-row">
        <span class="program-modal-label">Jenis</span>
        <span id="pdJenis" class="program-modal-value"></span>
      </div>
      <div class="program-modal-row">
        <span class="program-modal-label">Level</span>
        <span id="pdLevel" class="program-modal-value"></span>
      </div>
      <div class="program-modal-row">
        <span class="program-modal-label">Tanggal</span>
        <span id="pdTanggal" class="program-modal-value"></span>
      </div>

      <div class="program-modal-block">
        <span class="program-modal-label">Deskripsi</span>
        <p id="pdDeskripsi" class="program-modal-desc"></p>
      </div>

      <div class="program-modal-block">
        <span class="program-modal-label">Atlet (<span id="pdAtletCount">0</span>)</span>
        <ul id="pdAtletList" class="program-modal-atlet-list"></ul>
      </div>
    </div>
  </div>

  <style>
    .program-modal-overlay {
      position: fixed;
      inset: 0;
      background: rgba(18, 60, 105, 0.45);
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 16px;
      z-index: 100;
    }

    .program-modal {
      background: #fff;
      border-radius: 14px;
      padding: 22px;
      width: 100%;
      max-width: 460px;
      max-height: 85vh;
      overflow-y: auto;
      position: relative;
      box-shadow: 0 16px 40px rgba(18, 60, 105, 0.25);
    }

    .program-modal-close {
      position: absolute;
      top: 12px;
      right: 14px;
      background: none;
      border: none;
      font-size: 22px;
      line-height: 1;
      cursor: pointer;
      color: var(--bmc-muted, #6b7280);
    }

    .program-modal-title {
      margin: 0 28px 14px 0;
      font-family: 'Plus Jakarta Sans', sans-serif;
      font-size: 1.15rem;
    }

    .program-modal-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 8px 0;
      border-bottom: 1px solid #EEF2F7;
      font-size: 13.5px;
    }

    .program-modal-label {
      color: var(--bmc-muted, #6b7280);
      font-weight: 600;
      font-size: 12.5px;
    }

    .program-modal-value {
      color: var(--bmc-text, #123C69);
      font-weight: 600;
    }

    .program-modal-block {
      margin-top: 14px;
    }

    .program-modal-desc {
      margin: 6px 0 0;
      font-size: 13.5px;
      color: var(--bmc-text, #123C69);
      line-height: 1.5;
      white-space: pre-line;
    }

    .program-modal-atlet-list {
      list-style: none;
      margin: 8px 0 0;
      padding: 0;
      display: flex;
      flex-direction: column;
      gap: 6px;
      max-height: 180px;
      overflow-y: auto;
    }

    .program-modal-atlet-list li {
      background: #F3F7FC;
      border-radius: 8px;
      padding: 7px 10px;
      font-size: 13px;
      color: var(--bmc-text, #123C69);
    }
  </style>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const overlay = document.getElementById('programDetailOverlay');
      const closeBtn = document.getElementById('programDetailClose');
      const rows = document.querySelectorAll('.program-row');

      function openModal(data) {
        document.getElementById('pdNamaProgram').textContent = data.nama_program || '-';
        document.getElementById('pdJenis').textContent = data.jenis || '-';
        document.getElementById('pdLevel').textContent = data.level ? data.level.charAt(0).toUpperCase() + data.level.slice(1) : '-';
        document.getElementById('pdTanggal').textContent = data.tanggal || '-';
        document.getElementById('pdDeskripsi').textContent = data.deskripsi || 'Belum ada deskripsi.';

        const atletList = document.getElementById('pdAtletList');
        atletList.innerHTML = '';
        const atlet = data.atlet || [];
        document.getElementById('pdAtletCount').textContent = atlet.length;

        if (atlet.length === 0) {
          const li = document.createElement('li');
          li.textContent = 'Belum ada atlet di program ini.';
          atletList.appendChild(li);
        } else {
          atlet.forEach(function (nama) {
            const li = document.createElement('li');
            li.textContent = nama;
            atletList.appendChild(li);
          });
        }

        overlay.style.display = 'flex';
        document.body.style.overflow = 'hidden';
      }

      function closeModal() {
        overlay.style.display = 'none';
        document.body.style.overflow = '';
      }

      rows.forEach(function (row) {
        row.addEventListener('click', function () {
          try {
            const data = JSON.parse(row.dataset.program);
            openModal(data);
          } catch (e) {
            console.error('Gagal membaca data program', e);
          }
        });
      });

      closeBtn.addEventListener('click', closeModal);
      overlay.addEventListener('click', function (e) {
        if (e.target === overlay) closeModal();
      });
      document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeModal();
      });
    });
  </script>
@endsection