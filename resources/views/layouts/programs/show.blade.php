@extends('layouts.bmc-app')

@section('page-title','Detail Program')

@section('content')
  <div class="card">
    <a href="{{ route('programs.index') }}" class="back-link">&larr; Kembali</a>

    <div class="page-header" style="align-items:flex-start;">
      <div>
        <h2 style="margin:0 0 .35rem;font-family:'Plus Jakarta Sans',sans-serif;">{{ $program->nama_program }}</h2>
        <div style="display:flex;flex-wrap:wrap;gap:.5rem;align-items:center;">
          <span class="tag-jenis">{{ $program->jenis ?? 'Umum' }}</span>
          <span class="chip
              @if($program->level==='pemula') chip-pemula
              @elseif($program->level==='beginner') chip-beginner
              @else chip-senior
              @endif">
            {{ ucfirst($program->level ?? 'umum') }}
          </span>
        </div>
      </div>
      @role('pelatih')
        <a href="{{ route('programs.edit', $program) }}" class="btn btn-outline">Edit</a>
      @endrole
    </div>

    <div class="info-grid" style="margin-top:1.2rem;">
      <div class="info-item">
        <dt>Tanggal</dt>
        <dd>{{ optional($program->tanggal)->translatedFormat('d F Y') ?? '-' }}</dd>
      </div>
      <div class="info-item">
        <dt>Jumlah Atlet</dt>
        <dd>{{ $program->atlets->count() }}</dd>
      </div>
      <div class="info-item" style="grid-column:1 / -1;">
        <dt>Deskripsi</dt>
        <dd>{{ $program->deskripsi ?: 'Belum ada deskripsi.' }}</dd>
      </div>
    </div>
  </div>

  <div class="card" style="margin-top:1.2rem;">
    <div class="panel-head" style="padding:0 0 1rem;border:none;display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap;">
      <h3 style="margin:0;font-family:'Plus Jakarta Sans',sans-serif;font-size:1.05rem;">Grafik Perkembangan Program</h3>

      <div style="display:flex;align-items:center;gap:.8rem;flex-wrap:wrap;">
        <label class="toolbar-label">Set</label>
        <select id="select-program-set" class="toolbar-select">
          <option value="all">Semua Set</option>
          @for($i=1;$i<=11;$i++)
            <option value="{{ $i }}">Set {{ $i }}</option>
          @endfor
        </select>

        <label class="toolbar-label">Periode</label>
        <select id="select-program-period" class="toolbar-select">
          <option value="week">Mingguan</option>
          <option value="month">Bulanan</option>
        </select>

        <label class="toolbar-label" style="display:inline-flex;align-items:center;gap:.5rem;">
          <input type="checkbox" id="toggle-per-atlet" style="accent-color:#123C69;">
          Tampilkan per Atlet
        </label>
      </div>
    </div>

    <div class="chart-scroll-x">
      <div class="chart-wrapper">
        <canvas id="programChart"></canvas>
      </div>
    </div>

    <div id="programChartLegend" class="chart-legend-scroll" style="display:none;"></div>
  </div>

  <style>
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
        height: 300px;
        min-width: 560px;
    }
    @media (min-width: 640px) {
        .chart-wrapper { min-width: 100%; }
    }
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

  @push('scripts')
    <script>
      (function () {
        const raw = @json($chartResults ?? []);
        const numSets = 11;
        const programSetSelect = document.getElementById('select-program-set');
        const programPeriodSelect = document.getElementById('select-program-period');
        const togglePerAtlet = document.getElementById('toggle-per-atlet');
        const legendEl = document.getElementById('programChartLegend');

        function getWeekKey(dateStr) {
          const d = new Date(dateStr + 'T00:00:00');
          const target = new Date(d.valueOf());
          const dayNr = (d.getUTCDay() + 6) % 7;
          target.setUTCDate(target.getUTCDate() - dayNr + 3);
          const firstThursday = new Date(Date.UTC(target.getUTCFullYear(), 0, 4));
          const diff = target - firstThursday;
          const week = 1 + Math.round(diff / (7 * 24 * 3600 * 1000));
          return target.getUTCFullYear() + '-W' + String(week).padStart(2, '0');
        }

        function getMonthKey(dateStr) {
          const d = new Date(dateStr + 'T00:00:00');
          return d.getUTCFullYear() + '-' + String(d.getUTCMonth() + 1).padStart(2, '0');
        }

        function getVisibleRows() {
          return raw.filter(row => row && row.tanggal);
        }

        function aggregateForSet(setIndex, period) {
          const groups = {};
          const rows = getVisibleRows();

          rows.forEach(function (row) {
            const key = period === 'month' ? getMonthKey(row.tanggal) : getWeekKey(row.tanggal);
            const val = row.values?.[setIndex - 1];
            if (val === null || val === undefined || val === '') return;
            if (!groups[key]) groups[key] = { sum: 0, count: 0 };
            groups[key].sum += Number(val);
            groups[key].count += 1;
          });

          const labels = Object.keys(groups).sort();
          return {
            labels,
            data: labels.map(function (label) {
              const item = groups[label];
              return item.count ? Math.round((item.sum / item.count) * 100) / 100 : null;
            })
          };
        }

        function aggregateForAllSets(period) {
          const groups = {};
          const rows = getVisibleRows();

          rows.forEach(function (row) {
            const key = period === 'month' ? getMonthKey(row.tanggal) : getWeekKey(row.tanggal);
            if (!groups[key]) {
              groups[key] = { sums: Array(numSets).fill(0), counts: Array(numSets).fill(0) };
            }

            for (let i = 0; i < numSets; i++) {
              const val = row.values?.[i];
              if (val === null || val === undefined || val === '') continue;
              groups[key].sums[i] += Number(val);
              groups[key].counts[i] += 1;
            }
          });

          const labels = Object.keys(groups).sort();
          const datasetData = [];

          for (let i = 0; i < numSets; i++) {
            datasetData.push(labels.map(function (label) {
              const item = groups[label];
              const count = item.counts[i];
              return count ? Math.round((item.sums[i] / count) * 100) / 100 : null;
            }));
          }

          return { labels, datasetData };
        }

        function buildLegend(chart) {
          if (!legendEl || !chart) return;
          legendEl.innerHTML = '';

          chart.data.datasets.forEach(function (ds, index) {
            const item = document.createElement('span');
            item.className = 'chart-legend-item';
            item.innerHTML = '<span class="chart-legend-swatch" style="background:' + ds.borderColor + '"></span>' + ds.label;
            item.addEventListener('click', function () {
              const meta = chart.getDatasetMeta(index);
              meta.hidden = meta.hidden === null ? !chart.data.datasets[index].hidden : !meta.hidden;
              item.classList.toggle('is-hidden', !!meta.hidden);
              chart.update();
            });
            legendEl.appendChild(item);
          });
          legendEl.style.display = 'flex';
        }

        function buildAverageChart(setIndex, period) {
          const agg = setIndex === 'all' ? aggregateForAllSets(period) : aggregateForSet(Number(setIndex), period);
          const chartColors = ['#F7941D', '#1F77B4', '#2CA02C', '#D62728', '#9467BD', '#8C564B', '#E377C2', '#7F7F7F', '#BCBD22', '#17BECF', '#A65628'];

          if (setIndex === 'all') {
            const datasets = agg.datasetData.map(function (dataArr, idx) {
              return {
                label: 'Set ' + (idx + 1),
                data: dataArr,
                borderColor: chartColors[idx % chartColors.length],
                backgroundColor: 'rgba(0,0,0,0)',
                tension: 0.25,
                fill: false,
                spanGaps: true,
                pointRadius: 3
              };
            });

            return {
              type: 'line',
              data: { labels: agg.labels, datasets: datasets },
              options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                layout: { padding: { bottom: 12 } },
                scales: {
                  y: { beginAtZero: true, grid: { color: '#E3EAF3' }, ticks: { font: { size: 12 } } },
                  x: { grid: { display: false }, ticks: { font: { size: 11 } } }
                }
              }
            };
          }

          return {
            type: 'line',
            data: {
              labels: agg.labels,
              datasets: [{
                label: 'Rata-rata Set ' + setIndex,
                data: agg.data,
                borderColor: '#F7941D',
                backgroundColor: 'rgba(247,148,29,0.12)',
                tension: 0.3,
                fill: true,
                pointBackgroundColor: '#123C69',
                pointRadius: 4
              }]
            },
            options: {
              responsive: true,
              maintainAspectRatio: false,
              plugins: { legend: { display: false } },
              layout: { padding: { bottom: 12 } },
              scales: {
                y: { beginAtZero: true, grid: { color: '#E3EAF3' }, ticks: { font: { size: 12 } } },
                x: { grid: { display: false }, ticks: { font: { size: 11 } } }
              }
            }
          };
        }

        function buildPerAtletChart(setIndex, period) {
          const rows = getVisibleRows();
          const athletes = {};

          rows.forEach(function (row) {
            if (!row.atlet_id) return;
            const key = row.atlet_id;
            if (!athletes[key]) {
              athletes[key] = {
                label: row.atlet_nama || 'Atlet ' + row.atlet_id,
                color: null,
                periods: {}
              };
            }

            const dateGroup = period === 'month' ? getMonthKey(row.tanggal) : getWeekKey(row.tanggal);
            if (!athletes[key].periods[dateGroup]) {
              athletes[key].periods[dateGroup] = { sum: 0, count: 0 };
            }

            const value = row.values?.[setIndex === 'all' ? 0 : (Number(setIndex) - 1)];
            if (value === null || value === undefined || value === '') return;
            athletes[key].periods[dateGroup].sum += Number(value);
            athletes[key].periods[dateGroup].count += 1;
          });

          const labels = Array.from(new Set(rows.map(row => period === 'month' ? getMonthKey(row.tanggal) : getWeekKey(row.tanggal)))).sort();
          const colorPalette = ['#F7941D', '#1F77B4', '#2CA02C', '#D62728', '#9467BD', '#8C564B', '#E377C2', '#7F7F7F', '#BCBD22', '#17BECF', '#A65628'];
          const datasets = [];
          const athleteIds = Object.keys(athletes);

          athleteIds.forEach(function (athleteId, index) {
            const athlete = athletes[athleteId];
            athlete.color = colorPalette[index % colorPalette.length];
            datasets.push({
              label: athlete.label,
              data: labels.map(function (label) {
                const item = athlete.periods[label];
                return item && item.count ? Math.round((item.sum / item.count) * 100) / 100 : null;
              }),
              borderColor: athlete.color,
              backgroundColor: 'rgba(0,0,0,0)',
              tension: 0.25,
              fill: false,
              spanGaps: true,
              pointRadius: 3
            });
          });

          return {
            type: 'line',
            data: { labels: labels, datasets: datasets },
            options: {
              responsive: true,
              maintainAspectRatio: false,
              plugins: { legend: { display: false } },
              layout: { padding: { bottom: 12 } },
              scales: {
                y: { beginAtZero: true, grid: { color: '#E3EAF3' }, ticks: { font: { size: 12 } } },
                x: { grid: { display: false }, ticks: { font: { size: 11 } } }
              }
            }
          };
        }

        function renderChart() {
          if (!window.Chart) return;
          const canvas = document.getElementById('programChart');
          if (!canvas) return;

          const setIndex = programSetSelect ? programSetSelect.value : 'all';
          const period = programPeriodSelect ? programPeriodSelect.value : 'week';
          const perAtlet = togglePerAtlet ? togglePerAtlet.checked : false;
          const config = perAtlet ? buildPerAtletChart(setIndex, period) : buildAverageChart(setIndex, period);

          if (window.__programChartInstance) {
            window.__programChartInstance.destroy();
          }

          window.__programChartInstance = new Chart(canvas.getContext('2d'), config);

          if (perAtlet && legendEl) {
            buildLegend(window.__programChartInstance);
          } else if (legendEl) {
            legendEl.style.display = 'none';
            legendEl.innerHTML = '';
          }
        }

        function initChart() {
          if (!programSetSelect || !programPeriodSelect || !togglePerAtlet) return;
          renderChart();
          programSetSelect.addEventListener('change', renderChart);
          programPeriodSelect.addEventListener('change', renderChart);
          togglePerAtlet.addEventListener('change', renderChart);
        }

        if (document.readyState === 'loading') {
          document.addEventListener('DOMContentLoaded', initChart);
        } else {
          initChart();
        }
      })();
    </script>
  @endpush
@endsection
