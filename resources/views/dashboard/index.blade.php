@extends('layouts.app')

@section('page_title', 'Dashboard - Requerimientos')

@section('content')

<style>
  .spgi-toolbar{
    background: var(--bg-surface-glass); border: 1px solid var(--border-main);
    border-radius: 20px; box-shadow: var(--shadow-main); backdrop-filter: blur(16px); padding: 20px;
  }

  .toolbar-selects .form-select, .toolbar-selects .form-control{
    height:44px; border-radius:12px; border:1px solid var(--border-main);
    background-color: var(--bg-surface); color: var(--text-main); font-size:.9rem;
  }

  .chart-box{
    background: var(--bg-surface-glass); border: 1px solid var(--border-main);
    border-radius: 24px; box-shadow: var(--shadow-main); padding: 24px; height: 100%;
    backdrop-filter: blur(16px);
  }

  .chart-title{
    font-weight: 800; font-size: 0.85rem; color: var(--text-muted);
    margin-bottom: 24px; text-align: center; text-transform: uppercase; letter-spacing: 2px;
  }

  .canvas-container { position: relative; height: 350px; }

  /* Summary Row Styles */
  .summary-card{
    background: var(--bg-surface-glass); border: 1px solid var(--border-main);
    border-radius: 20px; box-shadow: var(--shadow-main); padding: 24px;
    margin-bottom: 20px; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); backdrop-filter: blur(16px);
    border-left: 6px solid var(--spgi-primary);
  }

  .summary-card:hover{
    transform: translateY(-4px); 
    border-left-color: #60a5fa;
    background: rgba(var(--text-main), 0.05);
    box-shadow: 0 20px 40px rgba(0,0,0,0.3);
  }

  .summary-client-name{
    font-weight: 800; font-size: 1.1rem; color: var(--text-main);
    margin: 0; text-transform: uppercase; letter-spacing: 1.5px;
  }

  .summary-grid{ display: flex; flex-wrap: wrap; gap: 32px; margin-top: 20px; }

  .summary-label{
    font-size: .65rem; font-weight: 800; color: var(--text-muted);
    text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 8px;
  }

  .summary-value{
    font-size: 1.6rem; font-weight: 900; color: var(--text-main);
    text-decoration: none; line-height: 1; transition: all 0.2s ease;
  }
  .summary-value:hover{ color: var(--spgi-primary); transform: scale(1.1); }
  .summary-value.total{ color: var(--spgi-primary); }
  .summary-value.zero{ color: var(--text-muted); opacity: 0.2; pointer-events: none; }

  .toggle-container{
    background: rgba(var(--text-main), 0.05); padding: 6px; border-radius: 14px;
    display: inline-flex; gap: 6px; border: 1px solid var(--border-main);
  }

  .btn-toggle{
    border: 0; padding: 8px 24px; border-radius: 10px;
    font-size: .8rem; font-weight: 800; color: var(--text-muted);
    background: transparent; transition: all 0.3s ease; text-transform: uppercase; letter-spacing: 1px;
  }

  .btn-toggle.active{
    background: var(--spgi-primary); color: #fff;
    box-shadow: 0 4px 12px var(--spgi-primary-glow);
  }
</style>

</style>

<div class="container-fluid mb-5">

  <div class="row mb-4">
    <div class="col-12">
      <div class="spgi-toolbar d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
        <h5 class="fw-bold mb-0">Analítica de Requerimientos</h5>
        
        <form action="{{ route('dashboard') }}" method="GET" class="toolbar-selects">
          
          <select name="estado" class="form-select" onchange="this.form.submit()">
            <option value="Todos" {{ request('estado', 'Todos') == 'Todos' ? 'selected' : '' }}>Todos los estados</option>
            @foreach($estadosList as $e)
              <option value="{{ $e->id }}" {{ request('estado') == $e->id ? 'selected' : '' }}>{{ $e->nombre }}</option>
            @endforeach
            <option value="Solo_Pendientes" {{ request('estado') == 'Solo_Pendientes' ? 'selected' : '' }}>Estados pendientes</option>
          </select>

          <select name="cliente_id" class="form-select" onchange="this.form.submit()">
            <option value="">Todos los clientes</option>
            @foreach($clientes as $c)
              <option value="{{ $c->id }}" {{ request('cliente_id') == $c->id ? 'selected' : '' }}>{{ $c->nombre }}</option>
            @endforeach
          </select>

          <select name="asignado_id" class="form-select" onchange="this.form.submit()">
            <option value="todos" {{ request('asignado_id', 'todos') === 'todos' ? 'selected' : '' }}>Todos los usuarios</option>
            <option value="mios" {{ request('asignado_id') === 'mios' ? 'selected' : '' }}>Mis requerimientos</option>
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
    <div class="modal-content">
      <form action="{{ route('dashboard') }}" method="GET">
        <div class="modal-header">
          <h5 class="modal-title fw-bold">
            <i class="bi bi-filter-circle me-2 text-primary"></i>Filtros Avanzados
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body p-4">
          <div class="row g-4">
            <div class="col-md-6">
              <label class="form-label">Desde</label>
              <input type="date" name="desde" class="form-control" value="{{ request('desde') }}">
            </div>
            <div class="col-md-6">
              <label class="form-label">Hasta</label>
              <input type="date" name="hasta" class="form-control" value="{{ request('hasta') }}">
            </div>
            <div class="col-12 text-divider">
                <span>Configuración Adicional</span>
            </div>
            <div class="col-12">
              <label class="form-label">Categoría Iguala</label>
              <select name="categoria_iguala" class="form-select">
                <option value="">Todas las categorías</option>
                @foreach($categoriasIguala as $plan)
                  <option value="{{ $plan->id }}" {{ (string)request('categoria_iguala') === (string)$plan->id ? 'selected' : '' }}>
                    {{ $plan->nombre }}
                  </option>
                @endforeach
              </select>
            </div>
            @if($esAdmin || $esEncargado)
              <div class="col-12">
                <label class="form-label">Estado de Facturación</label>
                <select name="facturado" class="form-select">
                  <option value="">Todos los registros</option>
                  <option value="1" {{ request('facturado') === '1' ? 'selected' : '' }}>Facturados</option>
                  <option value="0" {{ request('facturado') === '0' ? 'selected' : '' }}>Sin facturar</option>
                </select>
              </div>
            @endif
          </div>
          {{-- Mantenemos los filtros simples actuales --}}
          <input type="hidden" name="estado" value="{{ request('estado', 'Todos') }}">
          <input type="hidden" name="cliente_id" value="{{ request('cliente_id') }}">
          <input type="hidden" name="asignado_id" value="{{ request('asignado_id', 'todos') }}">
        </div>
        <div class="modal-footer">
          <a href="{{ route('dashboard') }}" class="btn btn-secondary px-4 rounded-pill">Limpiar</a>
          <button type="submit" class="btn btn-spgi">Aplicar Filtros</button>
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
