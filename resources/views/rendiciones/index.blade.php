@extends('layouts.app')

@section('page_title', 'Rendiciones de Gastos')

@section('content')
<style>
  .spgi-bg{ padding: 12px 0 24px 0; }
  .btn-spgi{
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    border: 0; color: #fff !important; min-height:46px; border-radius:14px; padding:0 24px;
    box-shadow: 0 10px 25px rgba(59, 130, 246, 0.2); font-weight:700;
  }
  .btn-spgi:hover{ filter: brightness(1.1); transform: translateY(-1px); }
  .spgi-toolbar{
    background: var(--bg-surface-glass); border: 1px solid var(--border-main);
    border-radius: 20px; box-shadow: var(--shadow-main); backdrop-filter: blur(16px);
    padding: 24px; margin-bottom: 24px;
  }
  .toolbar-actions{ display:flex; justify-content:flex-end; gap:10px; flex-wrap:wrap; }
  .toolbar-actions .btn{ min-height:46px; border-radius:14px; padding:0 24px; font-weight: 700; }
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
  .spgi-table tbody td{ border-color: var(--border-main) !important; color: var(--text-main); padding: 16px; text-align:center; }
  .spgi-table tbody td.text-start { text-align:left; }
  .spgi-table tbody tr:hover{ background: rgba(59, 130, 246, 0.05); }
  .acciones .btn{ width: 38px; height: 38px; display: inline-flex; align-items: center; justify-content: center; padding: 0; border-radius: 10px; }

  .status-borrador { background: rgba(245, 158, 11, 0.1) !important; color: #f59e0b !important; }
  .status-enviado { background: rgba(59, 130, 246, 0.1) !important; color: #3b82f6 !important; }
  .status-aprobado { background: rgba(16, 185, 129, 0.1) !important; color: #10b981 !important; }
  .status-rechazado { background: rgba(239, 68, 68, 0.1) !important; color: #ef4444 !important; }

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
    box-shadow: 0 0 0 0.25rem rgba(59, 130, 246, 0.25) !important;
  }
</style>

<div class="spgi-bg">
  <div class="container">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold text-gradient">Rendiciones de Gastos</h1>
            <p class="text-muted mb-0">Control y reporte de gastos reembolsables, efectivo y tarjetas.</p>
        </div>
        <a href="{{ route('administracion.bienvenido') }}" class="btn btn-outline-secondary rounded-pill">
            <i class="bi bi-arrow-left me-2"></i> Volver
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4 animate__animated animate__fadeIn">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        </div>
    @endif

    <div class="spgi-toolbar">
      <div class="toolbar-actions">
        <a href="{{ route('rendiciones.create') }}" class="btn btn-spgi">
          <i class="bi bi-plus-lg me-1"></i> Nueva Rendición
        </a>
      </div>
    </div>

    <div class="spgi-table-box animate__animated animate__fadeInUp">
      <div class="table-responsive">
        <table class="table spgi-table align-middle">
          <thead>
            <tr>
              <th class="text-start">Título / Descripción</th>
              <th>Reportado Por</th>
              <th>Encargado / Responsable</th>
              <th>Estado</th>
              <th>Monto Total</th>
              <th>Creado el</th>
              <th style="width: 180px;">Acciones</th>
            </tr>
          </thead>
          <tbody>
            @forelse($rendiciones as $r)
            <tr>
              <td class="text-start fw-bold">
                <a href="{{ route('rendiciones.show', $r->id) }}" class="text-decoration-none text-primary">
                  {{ $r->titulo }}
                </a>
              </td>
              <td>{{ $r->user->name ?? 'N/A' }}</td>
              <td>
                @if($r->responsable)
                  <span class="badge bg-secondary px-2.5 py-1.5 rounded-pill">{{ $r->responsable->name }}</span>
                @else
                  <span class="text-muted small">No asignado</span>
                @endif
              </td>
              <td>
                @php
                  $statusClass = 'status-' . strtolower(str_replace(' ', '-', $r->estado));
                @endphp
                <select class="form-select form-select-sm status-select {{ $statusClass }}" data-rendicion-id="{{ $r->id }}">
                  <option value="Borrador" {{ $r->estado == 'Borrador' ? 'selected' : '' }}>Borrador</option>
                  <option value="Enviado" {{ $r->estado == 'Enviado' ? 'selected' : '' }}>Enviado</option>
                  <option value="Aprobado" {{ $r->estado == 'Aprobado' ? 'selected' : '' }}>Aprobado</option>
                  <option value="Rechazado" {{ $r->estado == 'Rechazado' ? 'selected' : '' }}>Rechazado</option>
                </select>
              </td>
              <td class="fw-bold">RD$ {{ number_format($r->total, 2) }}</td>
              <td>{{ $r->created_at->format('d/m/Y h:i A') }}</td>
              <td class="text-center acciones">
                <a href="{{ route('rendiciones.show', $r->id) }}" class="btn btn-outline-info" title="Ver / Editar Gastos">
                  <i class="bi bi-eye"></i>
                </a>
                <a href="{{ route('rendiciones.pdf', $r->id) }}" target="_blank" class="btn btn-outline-primary" title="Ver PDF">
                  <i class="bi bi-file-earmark-pdf"></i>
                </a>
                <form action="{{ route('rendiciones.destroy', $r->id) }}" method="POST" class="d-inline">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-outline-danger" onclick="return confirm('¿Seguro que deseas eliminar esta rendición y todos sus gastos asociados?')" title="Eliminar">
                    <i class="bi bi-trash"></i>
                  </button>
                </form>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="7" class="text-center py-5">
                <i class="bi bi-receipt display-4 text-muted mb-3 d-block"></i>
                <p class="text-muted">No se encontraron rendiciones registradas.</p>
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
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
            const rendicionId = this.dataset.rendicionId;
            const newStatus = this.value;
            const selectEl = this;
            
            // Cambiar la clase dinámicamente de inmediato para dar feedback visual rápido
            selectEl.className = `form-select form-select-sm status-select status-${newStatus.toLowerCase()}`;
            
            try {
                const response = await fetch(`/rendiciones/${rendicionId}/update-status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ estado: newStatus })
                });
                
                const data = await response.json();
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Estado Actualizado',
                        text: `La rendición ahora está en estado: ${newStatus}`,
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
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message || 'No se pudo actualizar el estado'
                });
            }
        });
    });
});
</script>
@endsection
