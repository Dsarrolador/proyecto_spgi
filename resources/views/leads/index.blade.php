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

  select.status-select {
    border: 1px solid transparent !important;
    cursor: pointer;
    text-align: center;
    text-align-last: center;
    padding: 6px 28px 6px 12px;
    border-radius: 999px;
    font-size: 0.75rem;
    font-weight: 800;
    text-transform: uppercase;
    width: auto;
    margin: 0 auto;
  }
  select.status-select:focus {
    box-shadow: 0 0 0 0.25rem rgba(16, 185, 129, 0.25) !important;
  }
  select.status-select.status-pendiente { background-color: rgba(245, 158, 11, 0.1) !important; color: #f59e0b !important; }
  select.status-select.status-seguimiento { background-color: rgba(59, 130, 246, 0.1) !important; color: #3b82f6 !important; }
  select.status-select.status-ganado { background-color: rgba(16, 185, 129, 0.1) !important; color: #10b981 !important; }
  select.status-select.status-perdido { background-color: rgba(239, 68, 68, 0.1) !important; color: #ef4444 !important; }

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
                            <select class="form-select form-select-sm status-select {{ $statusClass }}" data-lead-id="{{ $lead->id }}">
                                <option value="Pendiente" {{ $lead->status == 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                                <option value="Seguimiento" {{ $lead->status == 'Seguimiento' ? 'selected' : '' }}>Seguimiento</option>
                                <option value="Ganado" {{ $lead->status == 'Ganado' ? 'selected' : '' }}>Ganado</option>
                                <option value="Perdido" {{ $lead->status == 'Perdido' ? 'selected' : '' }}>Perdido</option>
                            </select>
                        </td>
                        <td>
                            <div class="d-flex justify-content-center gap-2 acciones">
                                @if($lead->status !== 'Ganado' && $lead->status !== 'Perdido')
                                <button type="button" class="btn btn-success btn-ganar" data-lead-id="{{ $lead->id }}" data-lead-nombre="{{ $lead->nombre }}" title="Aprobar / Ganado">
                                    <i class="bi bi-check-circle-fill"></i>
                                </button>
                                <button type="button" class="btn btn-danger btn-perder" data-lead-id="{{ $lead->id }}" data-lead-nombre="{{ $lead->nombre }}" title="Marcar como Perdido">
                                    <i class="bi bi-x-circle-fill"></i>
                                </button>
                                @endif
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

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.status-select').forEach(select => {
        select.addEventListener('change', async function() {
            const leadId = this.dataset.leadId;
            const newStatus = this.value;
            const selectEl = this;
            
            const originalClass = selectEl.className;
            
            // Cambiar la clase dinámicamente de inmediato para dar feedback visual rápido
            selectEl.className = `form-select form-select-sm status-select status-${newStatus.toLowerCase().replace(' ', '-')}`;
            
            try {
                const response = await fetch(`/leads/${leadId}/update-status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ status: newStatus })
                });
                
                const data = await response.json();
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Estado Actualizado',
                        text: `El lead ahora está en estado: ${newStatus}`,
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                } else {
                    throw new Error(data.error || 'Error al actualizar');
                }
            } catch (error) {
                console.error(error);
                // Revertir clase y valor en caso de error
                selectEl.className = originalClass;
                const match = originalClass.match(/status-(\w+)/);
                if (match) {
                    const oldStatus = match[1];
                    selectEl.value = oldStatus.charAt(0).toUpperCase() + oldStatus.slice(1);
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo actualizar el estado del lead.'
                });
            }
        });
    });

    document.querySelectorAll('.btn-ganar').forEach(btn => {
        btn.addEventListener('click', async function() {
            const leadId = this.dataset.leadId;
            const leadNombre = this.dataset.leadNombre;
            
            const result = await Swal.fire({
                title: '¿Confirmar Lead como GANADO?',
                text: `El lead "${leadNombre}" será marcado como Ganado. Se creará automáticamente el cliente y proyecto respectivo y pasarás al módulo de rentabilidad.`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, ganar y cotizar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#10b981'
            });
            
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Procesando...',
                    text: 'Creando cliente y proyecto en administración...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                try {
                    const response = await fetch(`/leads/${leadId}/convertir-ganado`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });
                    
                    const data = await response.json();
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Conversión Exitosa!',
                            text: 'El cliente y el proyecto han sido creados.',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = data.redirect_url;
                        });
                    } else {
                        throw new Error(data.error || 'Error al convertir');
                    }
                } catch (error) {
                    console.error(error);
                    Swal.fire('Error', error.message || 'Ocurrió un error al convertir el lead a proyecto.', 'error');
                }
            }
        });
    });

    document.querySelectorAll('.btn-perder').forEach(btn => {
        btn.addEventListener('click', async function() {
            const leadId = this.dataset.leadId;
            const leadNombre = this.dataset.leadNombre;
            
            const result = await Swal.fire({
                title: '¿Marcar Lead como PERDIDO?',
                text: `El lead "${leadNombre}" pasará al estado de Perdido.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, marcar perdido',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#ef4444'
            });
            
            if (result.isConfirmed) {
                try {
                    const response = await fetch(`/leads/${leadId}/marcar-perdido`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });
                    
                    const data = await response.json();
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Lead Perdido',
                            text: 'El estado del lead ha sido actualizado.',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        throw new Error(data.error || 'Error al actualizar');
                    }
                } catch (error) {
                    console.error(error);
                    Swal.fire('Error', 'No se pudo actualizar el estado del lead.', 'error');
                }
            }
        });
    });
});
</script>
@endsection
