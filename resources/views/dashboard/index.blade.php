@extends('layouts.app')

@section('page_title', 'Dashboard - Requerimientos')

@section('content')

<style>
  :root{
    --spgi-primary:#0d6efd;
    --spgi-border: rgba(15, 23, 42, .10);
    --spgi-ink:#0f172a;
    --spgi-muted:#64748b;
  }

  body{
    background:
      radial-gradient(900px 400px at 20% 10%, rgba(13,110,253,.15), transparent 60%),
      radial-gradient(800px 450px at 85% 20%, rgba(168,85,247,.12), transparent 55%),
      linear-gradient(135deg, rgba(13,110,253,.10), rgba(168,85,247,.08) 45%);
    background-attachment: fixed;
  }

  .spgi-toolbar{
    background: rgba(255,255,255,.94);
    border: 1px solid var(--spgi-border);
    border-radius: 18px;
    box-shadow: 0 15px 40px rgba(2, 6, 23, .08);
    backdrop-filter: blur(8px);
    padding: 16px;
  }

  .toolbar-selects{
    display:flex;
    gap:12px;
    align-items:center;
    flex-wrap:wrap;
  }

  .toolbar-selects .form-select,
  .toolbar-selects .form-control{
    height:42px;
    border-radius:10px;
    border:1px solid var(--spgi-border);
    min-width:180px;
    font-size:.9rem;
  }

  .chart-box{
    background: rgba(255,255,255,.92);
    border: 1px solid var(--spgi-border);
    border-radius: 20px;
    box-shadow: 0 14px 35px rgba(2, 6, 23, .07);
    padding: 24px;
    height: 100%;
    backdrop-filter: blur(8px);
  }

  .chart-title{
    font-weight: 800;
    font-size: 1.1rem;
    color: var(--spgi-ink);
    margin-bottom: 20px;
    text-align: center;
    letter-spacing: -.2px;
  }

  .canvas-container {
    position: relative;
    height: 350px;
  }

  .canvas-container canvas {
    cursor: pointer;
  }

  .btn-filter{
    background: linear-gradient(135deg, var(--spgi-primary), #2b7bff);
    color: #fff;
    border-radius: 10px;
    min-width: 100px;
    border: 0;
    box-shadow: 0 8px 15px rgba(13,110,253,.2);
  }

  /* Summary Row Styles */
  .summary-container{
    margin-top: 40px;
  }

  .summary-card{
    background: rgba(255,255,255,.94);
    border: 1px solid var(--spgi-border);
    border-radius: 16px;
    box-shadow: 0 10px 30px rgba(2, 6, 23, .05);
    padding: 18px 24px;
    margin-bottom: 16px;
    transition: all 0.2s ease;
    border-left: 4px solid var(--spgi-primary);
  }

  .summary-card:hover{
    transform: translateX(4px);
    box-shadow: 0 15px 40px rgba(2, 6, 23, .08);
    background: #fff;
  }

  .summary-client-name{
    font-weight: 800;
    font-size: 1.05rem;
    color: var(--spgi-ink);
    margin: 0;
    text-transform: uppercase;
    letter-spacing: .5px;
  }

  .summary-grid{
    display: flex;
    flex-wrap: wrap;
    gap: 30px;
    margin-top: 12px;
  }

  .summary-item{
    display: flex;
    flex-direction: column;
    min-width: 80px;
  }

  .summary-label{
    font-size: .7rem;
    font-weight: 700;
    color: var(--spgi-muted);
    text-transform: uppercase;
    letter-spacing: .8px;
    margin-bottom: 4px;
  }

  .summary-value{
    font-size: 1.3rem;
    font-weight: 900;
    color: var(--spgi-ink);
    text-decoration: none;
    line-height: 1;
  }

  .summary-value:hover{
    color: var(--spgi-primary);
  }

  .summary-value.total{
    color: var(--spgi-primary);
  }

  .summary-value.zero{
    color: #cbd5e1;
    pointer-events: none;
  }

  .toggle-container{
    background: rgba(15, 23, 42, .05);
    padding: 4px;
    border-radius: 12px;
    display: inline-flex;
    gap: 4px;
  }

  .btn-toggle{
    border: 0;
    padding: 6px 16px;
    border-radius: 8px;
    font-size: .85rem;
    font-weight: 700;
    color: var(--spgi-muted);
    background: transparent;
    transition: all 0.2s ease;
  }

  .btn-toggle.active{
    background: #fff;
    color: var(--spgi-primary);
    box-shadow: 0 4px 10px rgba(2, 6, 23, .05);
  }

  .summary-view{
    transition: opacity 0.3s ease;
  }

  .summary-view.d-none{
    display: none;
    opacity: 0;
  }

</style>

<div class="container-fluid mb-5">

  <div class="row mb-4">
    <div class="col-12">
      <div class="spgi-toolbar d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
        <h5 class="fw-bold mb-0">Analítica de Requerimientos</h5>
        
        <form action="{{ route('dashboard') }}" method="GET" class="toolbar-selects">
          
          <select name="estado" class="form-select" onchange="this.form.submit()">
            <option value="">Estado: Pendientes</option>
            @foreach($estadosList as $e)
              <option value="{{ $e->id }}" {{ request('estado') == $e->id ? 'selected' : '' }}>{{ $e->nombre }}</option>
            @endforeach
            <option value="Todos" {{ request('estado') == 'Todos' ? 'selected' : '' }}>Todos los estados</option>
          </select>

          <select name="cliente_id" class="form-select" onchange="this.form.submit()">
            <option value="">Todos los clientes</option>
            @foreach($clientes as $c)
              <option value="{{ $c->id }}" {{ request('cliente_id') == $c->id ? 'selected' : '' }}>{{ $c->nombre }}</option>
            @endforeach
          </select>

          <select name="asignado_id" class="form-select" onchange="this.form.submit()">
            <option value="mios" {{ request('asignado_id', 'mios') === 'mios' ? 'selected' : '' }}>Mis requerimientos</option>
            <option value="todos" {{ request('asignado_id') === 'todos' ? 'selected' : '' }}>Todos los usuarios</option>
            @foreach($asignados as $u)
              <option value="{{ $u->id }}" {{ request('asignado_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
            @endforeach
          </select>

          <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalAdvancedFilters">
            <i class="bi bi-sliders me-1"></i> Filtros
          </button>
        </form>
      </div>
    </div>
  </div>

  <div class="row g-4">
    
    <div class="col-12 col-md-6 col-lg-3">
      <div class="chart-box">
        <h6 class="chart-title">Distribución por Cliente</h6>
        <div class="canvas-container">
          <canvas id="chartClientes"></canvas>
        </div>
      </div>
    </div>

    <div class="col-12 col-md-6 col-lg-3">
      <div class="chart-box">
        <h6 class="chart-title">Distribución por Encargado</h6>
        <div class="canvas-container">
          <canvas id="chartEncargados"></canvas>
        </div>
      </div>
    </div>

    <div class="col-12 col-lg-6">
      <div class="chart-box">
        <h6 class="chart-title">Estados por Encargado</h6>
        <div class="canvas-container">
          <canvas id="chartStackEncargado"></canvas>
        </div>
      </div>
    </div>

    <div class="col-12">
      <div class="chart-box">
        <h6 class="chart-title">Distribución de Estados por Cliente</h6>
        <div class="canvas-container" style="height: 450px;">
          <canvas id="chartStackCliente"></canvas>
        </div>
      </div>
    </div>

  </div>

  <div class="row summary-container">
    <div class="col-12">
      <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <h4 class="fw-bold mb-0">Resumen de Requerimientos</h4>
        
        <div class="toggle-container">
          <button type="button" class="btn-toggle active" id="btn-general" onclick="switchView('general')">General</button>
          <button type="button" class="btn-toggle" id="btn-detailed" onclick="switchView('detailed')">Detallado</button>
        </div>
      </div>

      <!-- VISTA GENERAL -->
      <div id="view-general" class="summary-view">
        <div class="summary-card" style="border-left-width: 8px; padding: 30px;">
          <h6 class="summary-client-name" style="font-size: 1.2rem; color: var(--spgi-primary);">Totales de la Selección Actual</h6>
          
          <div class="summary-grid" style="gap: 40px; margin-top: 20px;">
            <div class="summary-item">
              <span class="summary-label" style="font-size: .8rem;">Total General</span>
              <a href="{{ route('requerimientos.index') }}?asignado_id={{ request('asignado_id', 'mios') }}&estado={{ request('estado') }}" 
                 class="summary-value total" style="font-size: 2.2rem;">
                {{ $generalTotals['total'] }}
              </a>
            </div>

            @foreach($estadosList as $estado)
              @php
                $count = $generalTotals['states'][$estado->id] ?? 0;
              @endphp
              <div class="summary-item">
                <span class="summary-label" style="font-size: .8rem;">{{ $estado->nombre }}</span>
                <a href="{{ route('requerimientos.index') }}?estado={{ $estado->id }}&asignado_id={{ request('asignado_id', 'todos') }}" 
                   class="summary-value {{ $count == 0 ? 'zero' : '' }}"
                   style="font-size: 1.8rem; {{ $count > 0 ? 'color: ' . ($estado->color ?? 'inherit') . ';' : '' }}">
                  {{ $count }}
                </a>
              </div>
            @endforeach
          </div>
        </div>
      </div>

      <!-- VISTA DETALLADA -->
      <div id="view-detailed" class="summary-view d-none">
        @foreach($clientSummary as $client)
          <div class="summary-card">
            <h6 class="summary-client-name">{{ $client->cliente_nombre }}</h6>
            
            <div class="summary-grid">
              <div class="summary-item">
                <span class="summary-label">Total</span>
                <a href="{{ route('requerimientos.index') }}?cliente_id={{ $client->cliente_id }}&asignado_id=todos" 
                   class="summary-value total">
                  {{ $client->total }}
                </a>
              </div>

              @foreach($estadosList as $estado)
                @php
                  $count = $client->states[$estado->id] ?? 0;
                @endphp
                <div class="summary-item">
                  <span class="summary-label">{{ $estado->nombre }}</span>
                  <a href="{{ route('requerimientos.index') }}?cliente_id={{ $client->cliente_id }}&estado={{ $estado->id }}&asignado_id=todos" 
                     class="summary-value {{ $count == 0 ? 'zero' : '' }}"
                     style="{{ $count > 0 ? 'color: ' . ($estado->color ?? 'inherit') . ';' : '' }}">
                    {{ $count }}
                  </a>
                </div>
              @endforeach
            </div>
          </div>
        @endforeach
      </div>

    </div>
  </div>

</div>

<!-- Modal Advanced Filters -->
<div class="modal fade" id="modalAdvancedFilters" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 18px;">
      <form action="{{ route('dashboard') }}" method="GET">
        <div class="modal-header bg-dark text-white border-0">
          <h5 class="modal-title fw-bold">Filtros Avanzados</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body p-4">
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label small fw-bold">Desde</label>
              <input type="date" name="desde" class="form-control" value="{{ request('desde') }}">
            </div>
            <div class="col-12">
              <label class="form-label small fw-bold">Hasta</label>
              <input type="date" name="hasta" class="form-control" value="{{ request('hasta') }}">
            </div>
            <div class="col-12">
              <label class="form-label small fw-bold">Categoría Iguala</label>
              <select name="categoria_iguala" class="form-select">
                <option value="">Todas</option>
                @foreach($categoriasIguala as $plan)
                  <option value="{{ $plan->id }}" {{ (string)request('categoria_iguala') === (string)$plan->id ? 'selected' : '' }}>
                    {{ $plan->nombre }}
                  </option>
                @endforeach
                {{-- Opciones legacy --}}
                <option value="Cliente de iguala solo sistema" {{ request('categoria_iguala') == 'Cliente de iguala solo sistema' ? 'selected' : '' }}>Solo sistema (viejo)</option>
                <option value="Cliente de iguala premium" {{ request('categoria_iguala') == 'Cliente de iguala premium' ? 'selected' : '' }}>Premium (viejo)</option>
                <option value="Cliente de iguala avanzada" {{ request('categoria_iguala') == 'Cliente de iguala avanzada' ? 'selected' : '' }}>Avanzada (viejo)</option>
                <option value="Cliente de iguala Basico" {{ request('categoria_iguala') == 'Cliente de iguala Basico' ? 'selected' : '' }}>Basico (viejo)</option>
                <option value="Cliente sin iguala" {{ request('categoria_iguala') == 'Cliente sin iguala' ? 'selected' : '' }}>Sin iguala (viejo)</option>
              </select>
            </div>
            @if($esAdmin || $esEncargado)
              <div class="col-12">
                <label class="form-label small fw-bold">Facturación</label>
                <select name="facturado" class="form-select">
                  <option value="">Todos</option>
                  <option value="1" {{ request('facturado') === '1' ? 'selected' : '' }}>Facturados</option>
                  <option value="0" {{ request('facturado') === '0' ? 'selected' : '' }}>No facturados</option>
                </select>
              </div>
            @endif
          </div>
          {{-- Mantenemos los filtros simples actuales --}}
          <input type="hidden" name="estado" value="{{ request('estado') }}">
          <input type="hidden" name="cliente_id" value="{{ request('cliente_id') }}">
          <input type="hidden" name="asignado_id" value="{{ request('asignado_id', 'mios') }}">
        </div>
        <div class="modal-footer border-0 p-4">
          <a href="{{ route('dashboard') }}" class="btn btn-light rounded-pill px-4">Limpiar</a>
          <button type="submit" class="btn btn-primary rounded-pill px-4">Aplicar Filtros</button>
        </div>
      </form>
    </div>
  </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    
    const palette = [
      'rgba(13, 110, 253, 0.85)', 'rgba(54, 162, 235, 0.85)', 'rgba(111, 66, 193, 0.85)',
      'rgba(232, 62, 140, 0.85)', 'rgba(220, 53, 69, 0.85)', 'rgba(253, 126, 20, 0.85)',
      'rgba(255, 193, 7, 0.85)', 'rgba(34, 197, 94, 0.85)', 'rgba(32, 201, 151, 0.85)',
      'rgba(13, 202, 240, 0.85)', 'rgba(108, 117, 125, 0.85)'
    ];

    // 1. Chart Clientes (Pie)
    const chartClientesData = @json($chartClientes);
    new Chart(document.getElementById('chartClientes'), {
      type: 'pie',
      data: {
        labels: chartClientesData.map(d => d.label),
        datasets: [{
          data: chartClientesData.map(d => d.total),
          backgroundColor: palette,
          borderWidth: 1
        }]
      },
      options: { 
        responsive: true, 
        maintainAspectRatio: false,
        onClick: (evt, activeEls, chart) => {
          if (activeEls.length > 0) {
            const index = activeEls[0].index;
            const id = chartClientesData[index].id;
            window.location.href = `{{ route('requerimientos.index') }}?cliente_id=${id}&asignado_id=todos`;
          }
        },
        plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 11 } } } }
      }
    });

    // 2. Chart Encargados (Doughnut)
    const chartEncargadosData = @json($chartEncargados);
    new Chart(document.getElementById('chartEncargados'), {
      type: 'doughnut',
      data: {
        labels: chartEncargadosData.map(d => d.label),
        datasets: [{
          data: chartEncargadosData.map(d => d.total),
          backgroundColor: palette.slice().reverse(),
          borderWidth: 1
        }]
      },
      options: { 
        responsive: true, 
        maintainAspectRatio: false,
        onClick: (evt, activeEls, chart) => {
          if (activeEls.length > 0) {
            const index = activeEls[0].index;
            const id = chartEncargadosData[index].id || 'todos';
            window.location.href = `{{ route('requerimientos.index') }}?asignado_id=${id}`;
          }
        },
        plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 11 } } } }
      }
    });

    // 3. Stacked Bar: Estado por Encargado
    const rawStackEnc = @json($chartEstadoPorEncargado);
    const states = [...new Set(rawStackEnc.map(d => d.estado))];
    const assignees = [...new Set(rawStackEnc.map(d => d.encargado))];

    const datasetsEnc = assignees.map((name, i) => {
      const assigneeData = rawStackEnc.filter(d => d.encargado === name);
      const assigneeId = assigneeData.length > 0 ? assigneeData[0].asignado_user_id : 'todos';
      
      return {
        label: name,
        assigneeId: assigneeId,
        backgroundColor: palette[i % palette.length],
        data: states.map(state => {
          const found = rawStackEnc.find(d => d.estado === state && d.encargado === name);
          return found ? found.total : 0;
        })
      };
    });

    new Chart(document.getElementById('chartStackEncargado'), {
      type: 'bar',
      data: { labels: states, datasets: datasetsEnc },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        onClick: (evt, activeEls, chart) => {
          if (activeEls.length > 0) {
            const dataIndex = activeEls[0].index; // Index of the state label
            const datasetIndex = activeEls[0].datasetIndex;
            const stateName = states[dataIndex];
            const assigneeId = chart.data.datasets[datasetIndex].assigneeId || 'todos';
            
            // Need state ID
            const stateObj = rawStackEnc.find(d => d.estado === stateName);
            const stateId = stateObj ? stateObj.estado_id : '';
            
            window.location.href = `{{ route('requerimientos.index') }}?estado=${stateId}&asignado_id=${assigneeId}`;
          }
        },
        scales: { x: { stacked: true }, y: { stacked: true, beginAtZero: true, ticks: { precision: 0 } } },
        plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 11 } } } }
      }
    });

    // 4. Stacked Bar: Estado por Cliente
    const rawStackCli = @json($chartEstadoPorCliente);
    const statesCli = [...new Set(rawStackCli.map(d => d.estado))];
    const clients = [...new Set(rawStackCli.map(d => d.cliente))];

    const datasetsCli = clients.map((name, i) => {
      const clientData = rawStackCli.filter(d => d.cliente === name);
      const clientId = clientData.length > 0 ? clientData[0].cliente_id : '';

      return {
        label: name,
        clientId: clientId,
        backgroundColor: palette[i % palette.length],
        data: statesCli.map(state => {
          const found = rawStackCli.find(d => d.estado === state && d.cliente === name);
          return found ? found.total : 0;
        })
      };
    });

    new Chart(document.getElementById('chartStackCliente'), {
      type: 'bar',
      data: { labels: statesCli, datasets: datasetsCli },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        indexAxis: 'y',
        onClick: (evt, activeEls, chart) => {
          if (activeEls.length > 0) {
            const dataIndex = activeEls[0].index;
            const datasetIndex = activeEls[0].datasetIndex;
            const stateName = statesCli[dataIndex];
            const clientId = chart.data.datasets[datasetIndex].clientId || '';
            
            const stateObj = rawStackCli.find(d => d.estado === stateName);
            const stateId = stateObj ? stateObj.estado_id : '';
            
            window.location.href = `{{ route('requerimientos.index') }}?estado=${stateId}&cliente_id=${clientId}&asignado_id=todos`;
          }
        },
        scales: { x: { stacked: true, ticks: { precision: 0 } }, y: { stacked: true } },
        plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 11 } } } }
      }
    });

  });

  function switchView(view) {
    const btnGen = document.getElementById('btn-general');
    const btnDet = document.getElementById('btn-detailed');
    const viewGen = document.getElementById('view-general');
    const viewDet = document.getElementById('view-detailed');

    if (view === 'general') {
      btnGen.classList.add('active');
      btnDet.classList.remove('active');
      viewGen.classList.remove('d-none');
      viewDet.classList.add('d-none');
    } else {
      btnDet.classList.add('active');
      btnGen.classList.remove('active');
      viewDet.classList.remove('d-none');
      viewGen.classList.add('d-none');
    }
  }
</script>
@endpush

@endsection
