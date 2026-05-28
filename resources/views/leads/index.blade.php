@extends('layouts.app')

@section('page_title', 'Leads de Clientes')

@section('content')

<style>
  .spgi-bg{ padding: 12px 0 24px 0; }

  .btn-spgi{
    background: linear-gradient(135deg, #10b981, #059669);
    border: 0; color: #fff !important; min-height:46px; border-radius:14px; padding:0 24px;
    box-shadow: 0 10px 25px rgba(16, 185, 129, 0.2); font-weight:700;
  }
  .btn-spgi:hover{ filter: brightness(1.1); transform: translateY(-1px); }

  .btn-outline-spgi{
    border: 1px solid var(--border-main); background: var(--bg-surface);
    color: var(--text-main); border-radius: 12px; height: 46px; font-weight: 700;
  }
  .btn-outline-spgi:hover{ background: rgba(16, 185, 129, 0.05); }

  .spgi-toolbar{
    background: var(--bg-surface-glass); border: 1px solid var(--border-main);
    border-radius: 20px; box-shadow: var(--shadow-main); backdrop-filter: blur(16px);
    padding: 24px; margin-bottom: 24px;
  }

  .toolbar-actions{ display:flex; justify-content:flex-end; gap:10px; flex-wrap:wrap; margin-bottom: 20px; }
  .toolbar-actions .btn{ min-height:46px; border-radius:14px; padding:0 24px; font-weight: 700; }

  .toolbar-selects{ display:flex; gap:12px; align-items:center; flex-wrap:wrap; margin:0; }
  .toolbar-selects .form-control, .toolbar-selects .form-select{
    height:46px; border-radius:12px; border:1px solid var(--border-main);
    background-color: var(--bg-surface); color: var(--text-main); min-width:200px;
    box-shadow: none !important;
  }

  .spgi-table-box{
    background: var(--bg-surface-glass); border: 1px solid var(--border-main);
    border-radius: 22px; box-shadow: var(--shadow-main); overflow: hidden; backdrop-filter: blur(16px);
  }

  .spgi-table{ margin-bottom: 0; }
  .spgi-table thead th{
    background: #0b1220; color: #fff; text-align:center;
    border-color: rgba(255,255,255,0.08) !important;
    font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; padding: 16px;
  }
  .spgi-table tbody td{ border-color: var(--border-main) !important; color: var(--text-main); padding: 16px; }
  .spgi-table tbody tr:hover{ background: rgba(16, 185, 129, 0.05); }

  .acciones .btn{ width: 38px; height: 38px; display: inline-flex; align-items: center; justify-content: center; padding: 0; border-radius: 10px; }

  .status-badge {
    padding: 6px 12px;
    border-radius: 999px;
    font-size: 0.75rem;
    font-weight: 800;
    text-transform: uppercase;
  }

  .status-pendiente { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
  .status-seguimiento { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
  .status-ganado { background: rgba(16, 185, 129, 0.1); color: #10b981; }
  .status-perdido { background: rgba(239, 68, 68, 0.1); color: #ef4444; }

  @media (max-width: 767.98px) {
    .spgi-table-desktop { display: none; }
  }
</style>

<div class="spgi-bg">
  <div class="container">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold text-gradient">Leads de clientes</h1>
            <p class="text-muted mb-0">Gestión de prospectos y oportunidades comerciales.</p>
        </div>
        <a href="{{ route('leads.bienvenido') }}" class="btn btn-outline-secondary rounded-pill">
            <i class="bi bi-arrow-left me-2"></i> Volver
        </a>
    </div>

    <div class="spgi-toolbar">
      <div class="toolbar-actions">
        <a href="{{ route('leads.indexCalculos') }}" class="btn btn-outline-primary rounded-pill d-flex align-items-center gap-2">
          <i class="bi bi-calculator"></i> Dashboard de Cálculos
        </a>
        <a href="{{ route('leads.create') }}" class="btn btn-spgi">
          <i class="bi bi-plus-lg me-1"></i> Nuevo Lead
        </a>
      </div>

      <form action="{{ route('leads.index') }}" method="GET" class="toolbar-selects">
        <input type="text" name="search" class="form-control" placeholder="Buscar por nombre..." value="{{ request('search') }}">
        
        <select name="status" class="form-select" onchange="this.form.submit()">
          <option value="">Todos los estados</option>
          <option value="Pendiente" {{ request('status') == 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
          <option value="Seguimiento" {{ request('status') == 'Seguimiento' ? 'selected' : '' }}>En Seguimiento</option>
          <option value="Ganado" {{ request('status') == 'Ganado' ? 'selected' : '' }}>Ganado</option>
          <option value="Perdido" {{ request('status') == 'Perdido' ? 'selected' : '' }}>Perdido</option>
        </select>
        
        <button type="submit" class="btn btn-outline-spgi px-4">
            <i class="bi bi-search"></i>
        </button>
      </form>
    </div>

    <div class="spgi-table-box spgi-table-desktop">
        <div class="table-responsive spgi-table">
            <table class="table table-bordered align-middle mb-0 text-center">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Contacto</th>
                        <th>Correo</th>
                        <th>Total Est.</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($leads as $lead)
                    <tr>
                        <td class="text-start fw-bold">{{ $lead->nombre }}</td>
                        <td>{{ $lead->contacto ?? '---' }}</td>
                        <td>{{ $lead->correo ?? '---' }}</td>
                        <td class="fw-bold text-success text-gradient">
                            {{ $lead->total_estimado ? '$' . number_format($lead->total_estimado, 2) : '---' }}
                        </td>
                        <td>
                            @php
                                $statusClass = 'status-' . strtolower(str_replace(' ', '-', $lead->status));
                            @endphp
                            <span class="status-badge {{ $statusClass }}">
                                {{ $lead->status }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex justify-content-center gap-2 acciones">
                                <a href="{{ route('leads.show', $lead->id) }}" class="btn btn-primary" title="Ver detalle">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('leads.show', $lead->id) }}#novedadModal" class="btn btn-secondary" title="Novedades / Bitácora">
                                    <i class="bi bi-journal-text"></i>
                                </a>
                                <a href="{{ route('leads.calculadora', $lead->id) }}" class="btn btn-warning" title="Calculadora Matrix">
                                    <i class="bi bi-calculator text-dark"></i>
                                </a>
                                <a href="{{ route('leads.edit', $lead->id) }}" class="btn btn-outline-info" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('leads.destroy', $lead->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('¿Eliminar este lead?')" title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-5 text-muted">No se encontraron leads.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $leads->withQueryString()->links() }}
    </div>

  </div>
</div>

@endsection
