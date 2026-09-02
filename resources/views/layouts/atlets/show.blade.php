@extends('layouts.bmc-app')

@section('page-title','Detail Atlet')

@section('content')
  <div class="card">
    <a href="{{ route('atlets.index') }}" class="back-link">&larr; Kembali</a>

    <div class="profile-head">
      @if ($atlet->fotoUrl())
        <img src="{{ $atlet->fotoUrl() }}" class="avatar-lg" style="object-fit:cover;">
      @else
        <div class="avatar-lg">{{ strtoupper(substr($atlet->nama, 0, 1)) }}</div>
      @endif
      <div>
        <h3>{{ $atlet->nama }}</h3>
        <span class="chip
            @if($atlet->level==='pemula') chip-pemula
            @elseif($atlet->level==='beginner') chip-beginner
            @else chip-senior
            @endif">
          {{ $atlet->levelLabel() }}
        </span>
      </div>
    </div>

    <div class="info-grid">
      <div class="info-item">
        <dt>Umur</dt>
        <dd>{{ $atlet->umur }} tahun</dd>
      </div>
      <div class="info-item">
        <dt>Jenis Kelamin</dt>
        <dd>{{ $atlet->jenis_kelamin === 'L' ? 'Laki-laki' : ($atlet->jenis_kelamin === 'P' ? 'Perempuan' : '-') }}</dd>
      </div>
      <div class="info-item">
        <dt>No. HP</dt>
        <dd>{{ $atlet->no_hp ?? '-' }}</dd>
      </div>
      <div class="info-item">
        <dt>Terdaftar sejak</dt>
        <dd>{{ $atlet->created_at->translatedFormat('d F Y') }}</dd>
      </div>
    </div>

    <div class="form-footer">
      <a href="{{ route('atlets.edit', $atlet) }}" class="btn btn-outline">Edit</a>
      <form action="{{ route('atlets.destroy', $atlet) }}" method="POST"
            onsubmit="return confirm('Yakin mau hapus data {{ $atlet->nama }}?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn" style="background:#fbe4e4;color:#c0392b;">Hapus</button>
      </form>
      <a href="{{ route('hasil_latihan.create') }}" class="btn btn-navy">Input Hasil</a>
    </div>
  </div>

  {{-- Chart & hasil latihan --}}
  <div class="card" style="margin-top:1.2rem;">
    <div class="panel-head" style="padding:0 0 1rem;border:none;">
      <h3 style="margin:0;font-family:'Plus Jakarta Sans',sans-serif;font-size:1.05rem;">Grafik Hasil Latihan</h3>
      <div style="display:flex;align-items:center;gap:.8rem;flex-wrap:wrap;">
        <label class="toolbar-label">Program</label>
        <select id="select-program" class="toolbar-select" title="Semua Program akan menggabungkan hasil dari beberapa program yang berbeda, sehingga konteks latihan bisa tercampur.">
          <option value="all">Semua Program</option>
          @foreach(($programHistory ?? []) as $programOption)
            <option value="{{ $programOption['id'] }}" @selected(($defaultProgramId ?? null) !== null && (int) $programOption['id'] === (int) $defaultProgramId)>
              {{ $programOption['nama'] }}
            </option>
          @endforeach
        </select>

        <label class="toolbar-label">Set</label>
        <select id="select-set" class="toolbar-select">
          <option value="all">Semua Set</option>
          @for($i=1;$i<=11;$i++)
            <option value="{{ $i }}">Set {{ $i }}</option>
          @endfor
        </select>

        <label class="toolbar-label">Periode</label>
        <select id="select-period" class="toolbar-select">
          <option value="week">Mingguan</option>
          <option value="month">Bulanan</option>
        </select>
      </div>
    </div>

    <div class="chart-scroll-x">
      <div class="chart-wrapper">
        <canvas id="hasilChart"></canvas>
      </div>
    </div>

    {{-- Custom legend: scrollable horizontal, hanya muncul saat mode "Semua Set" --}}
    <div id="chartLegend" class="chart-legend-scroll" style="display:none;"></div>

    <div style="margin-top:1.6rem;">
      <h4 style="font-size:.92rem;font-weight:700;color:var(--ink);margin-bottom:.8rem;">Daftar Hasil Terakhir</h4>
      <div class="table-wrap">
        <table class="data-table">
          <thead>
            <tr>
              <th>Tanggal</th>
              <th>Program</th>
              <th>Nilai (Set1..11)</th>
            </tr>
          </thead>
          <tbody>
            @if(!empty($hasil) && $hasil->count())
              @foreach($hasil as $h)
                <tr>
                  <td>{{ optional($h->tanggal)->format('d F Y') }}</td>
                  <td>{{ $h->program->nama_program ?? '-' }}</td>
                  <td>{{ collect(range(1,11))->map(fn($i)=> $h['nilai_set_'.$i] ?? '-')->implode(', ') }}</td>
                </tr>
              @endforeach
            @else
              <tr>
                <td colspan="3">
                  <div class="empty-state">Belum ada hasil latihan.</div>
                </td>
              </tr>
            @endif
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <style>
    /* Container agar chart bisa digeser horizontal di layar sempit */
    .chart-scroll-x {
        width: 100%;
        overflow-x: auto;
        overflow-y: hidden;
        -webkit-overflow-scrolling: touch;
        padding-bottom: 4px;
    }
    .chart-scroll-x::-webkit-scrollbar {
        height: 6px;
    }
    .chart-scroll-x::-webkit-scrollbar-thumb {
        background: #D6DEE8;
        border-radius: 999px;
    }
    .chart-wrapper {
        position: relative;
        height: 280px;
        min-width: 560px; /* biar chart tidak dipaksa menyempit di layar kecil */
    }
    @media (min-width: 640px) {
        .chart-wrapper { min-width: 100%; }
    }

    /* Legend custom: satu baris, bisa digeser kanan-kiri, tidak wrap ke bawah */
    .chart-legend-scroll {
        display: flex;
        flex-wrap: nowrap;
        gap: 14px;
        overflow-x: auto;
        overflow-y: hidden;
        -webkit-overflow-scrolling: touch;
        padding: 10px 4px 6px;
        margin-top: 4px;
        white-space: nowrap;
        border-top: 1px solid #EEF2F7;
        -webkit-mask-image: linear-gradient(to right, black 92%, transparent 100%);
        mask-image: linear-gradient(to right, black 92%, transparent 100%);
    }
    .chart-legend-scroll::-webkit-scrollbar {
        height: 6px;
    }
    .chart-legend-scroll::-webkit-scrollbar-thumb {
        background: #D6DEE8;
        border-radius: 999px;
    }
    .chart-legend-item {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        flex: 0 0 auto;
        font-size: 12.5px;
        color: var(--ink, #123C69);
        cursor: pointer;
        user-select: none;
    }
    .chart-legend-item.is-hidden {
        opacity: 0.35;
    }
    .chart-legend-swatch {
        width: 12px;
        height: 12px;
        border-radius: 3px;
        flex: 0 0 auto;
    }
  </style>

  {{-- Chart.js sudah dimuat di layouts.bmc-app, jadi di sini cukup script logic-nya saja --}}
  <script>
    (function(){
        const raw = @json($hasilForJs ?? []);
        const NUM_SETS = 11;
        const programSelect = document.getElementById('select-program');
        const selectedProgramDefault = programSelect ? programSelect.value : 'all';

        function getWeekKey(dateStr){
            const d = new Date(dateStr + 'T00:00:00');
            const target = new Date(d.valueOf());
            const dayNr = (d.getUTCDay() + 6) % 7;
            target.setUTCDate(target.getUTCDate() - dayNr + 3);
            const firstThursday = new Date(Date.UTC(target.getUTCFullYear(),0,4));
            const diff = target - firstThursday;
            const week = 1 + Math.round(diff /  (7 * 24 * 3600 * 1000));
            return target.getUTCFullYear() + '-W' + String(week).padStart(2,'0');
        }

        function getMonthKey(dateStr){
            const d = new Date(dateStr + 'T00:00:00');
            return d.getUTCFullYear() + '-' + String(d.getUTCMonth()+1).padStart(2,'0');
        }

        function getVisibleRows(){
            const selectedProgram = programSelect ? String(programSelect.value) : 'all';
            if (selectedProgram === 'all') return raw;
            return raw.filter(row => String(row.program_id) === selectedProgram);
        }

        // aggregate for a single set (existing behavior)
        function aggregateSingle(setIndex, period){
            const groups = {};
            for(const row of getVisibleRows()){
                if(!row.tanggal) continue;
                const key = period === 'month' ? getMonthKey(row.tanggal) : getWeekKey(row.tanggal);
                const val = row.values[setIndex-1];
                if(val === null || val === undefined || val === '') continue;
                if(!groups[key]) groups[key] = {sum:0, count:0};
                groups[key].sum += Number(val);
                groups[key].count += 1;
            }
            const keys = Object.keys(groups).sort();
            const labels = keys;
            const data = keys.map(k => Math.round((groups[k].sum / groups[k].count) * 100)/100);
            return {labels, data};
        }

        // aggregate for all sets: returns labels and array of data arrays (per set)
        function aggregateAll(period){
            const groups = {}; // key -> {sums:[], counts:[]}
            for(const row of getVisibleRows()){
                if(!row.tanggal) continue;
                const key = period === 'month' ? getMonthKey(row.tanggal) : getWeekKey(row.tanggal);
                if(!groups[key]) {
                    groups[key] = {sums: Array(NUM_SETS).fill(0), counts: Array(NUM_SETS).fill(0)};
                }
                for(let i=0;i<NUM_SETS;i++){
                    const v = row.values[i];
                    if(v === null || v === undefined || v === '') continue;
                    groups[key].sums[i] += Number(v);
                    groups[key].counts[i] += 1;
                }
            }
            const keys = Object.keys(groups).sort();
            const labels = keys;
            const datasetsData = [];
            for(let i=0;i<NUM_SETS;i++){
                const arr = keys.map(k => {
                    const cnt = groups[k].counts[i];
                    return cnt ? Math.round((groups[k].sums[i] / cnt) * 100)/100 : null;
                });
                datasetsData.push(arr);
            }
            return {labels, datasetsData};
        }

        function initChart(){
            const canvasEl = document.getElementById('hasilChart');
            const legendEl = document.getElementById('chartLegend');
            if(!canvasEl) {
                console.error('hasilChart canvas tidak ditemukan');
                return;
            }

            if(typeof Chart === 'undefined') {
                console.error('Chart.js belum dimuat');
                return;
            }

            const ctx = canvasEl.getContext('2d');
            let chart = null;

            // simple color palette for up to 11 sets
            const colors = ['#F7941D','#1F77B4','#2CA02C','#D62728','#9467BD','#8C564B','#E377C2','#7F7F7F','#BCBD22','#17BECF','#A65628'];

            function buildCustomLegend(){
                if(!legendEl || !chart) return;
                legendEl.innerHTML = '';
                chart.data.datasets.forEach((ds, i) => {
                    const item = document.createElement('span');
                    item.className = 'chart-legend-item';
                    item.innerHTML = '<span class="chart-legend-swatch" style="background:' + ds.borderColor + '"></span>' + ds.label;
                    item.addEventListener('click', function(){
                        const meta = chart.getDatasetMeta(i);
                        meta.hidden = meta.hidden === null ? !chart.data.datasets[i].hidden : !meta.hidden;
                        item.classList.toggle('is-hidden', !!meta.hidden);
                        chart.update();
                    });
                    legendEl.appendChild(item);
                });
                legendEl.style.display = 'flex';
            }

            function renderChart(setIndex, period){
                let cfg;

                if(setIndex === 'all'){
                    const ga = aggregateAll(period);
                    const datasets = ga.datasetsData.map((dataArr, idx) => ({
                        label: 'Set ' + (idx+1),
                        data: dataArr,
                        borderColor: colors[idx % colors.length],
                        backgroundColor: 'rgba(0,0,0,0)',
                        tension: 0.25,
                        fill: false,
                        spanGaps: true,
                        pointRadius: 3
                    }));

                    const isNarrow = window.innerWidth <= 400;
                    cfg = {
                        type: 'line',
                        data: { labels: ga.labels, datasets },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { display: false } },
                            layout: { padding: { bottom: 12 } },
                            scales: {
                                y: { beginAtZero: true, grid: { color: '#E3EAF3' }, ticks: { font: { size: isNarrow ? 10 : 12 }, padding: isNarrow ? 6 : 4 } },
                                x: { grid: { display: false }, ticks: { font: { size: isNarrow ? 10 : 12 } } }
                            }
                        }
                    };

                    if(chart) chart.destroy();
                    chart = new Chart(ctx, cfg);
                    buildCustomLegend();
                } else {
                    const idx = Number(setIndex);
                    const ga = aggregateSingle(idx, period);
                    const isNarrow = window.innerWidth <= 400;
                    cfg = {
                        type: 'line',
                        data: {
                            labels: ga.labels,
                            datasets: [{
                                label: 'Rata-rata Set ' + idx,
                                data: ga.data,
                                borderColor: '#F7941D',
                                backgroundColor: 'rgba(247,148,29,0.12)',
                                tension: 0.3,
                                fill: true,
                                pointBackgroundColor: '#123C69',
                                pointRadius: isNarrow ? 3 : 4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { display: false } },
                            layout: { padding: { bottom: isNarrow ? 28 : 12 } },
                            scales: {
                                y: { beginAtZero: true, grid: { color: '#E3EAF3' }, ticks: { font: { size: isNarrow ? 10 : 12 }, padding: isNarrow ? 6 : 4 } },
                                x: { grid: { display: false }, ticks: { font: { size: isNarrow ? 10 : 12 } } }
                            }
                        }
                    };

                    if(chart) chart.destroy();
                    chart = new Chart(ctx, cfg);

                    if(legendEl){
                        legendEl.style.display = 'none';
                        legendEl.innerHTML = '';
                    }
                }
            }

            const setSelect = document.getElementById('select-set');
            const periodSelect = document.getElementById('select-period');

            if(setSelect && periodSelect && programSelect){
                if (selectedProgramDefault && selectedProgramDefault !== 'all') {
                    programSelect.value = selectedProgramDefault;
                }

                renderChart(String(setSelect.value), periodSelect.value);

                setSelect.addEventListener('change', function(){
                    renderChart(String(this.value), periodSelect.value);
                });
                periodSelect.addEventListener('change', function(){
                    renderChart(String(setSelect.value), this.value);
                });
                programSelect.addEventListener('change', function(){
                    renderChart(String(setSelect.value), periodSelect.value);
                });
            }
        }

        if(document.readyState === 'loading'){
            document.addEventListener('DOMContentLoaded', initChart);
        } else {
            initChart();
        }
    })();
  </script>
@endsection