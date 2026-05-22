@extends('layouts.app')

@section('page_title', 'Planillas de Horas Extras')

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
</style>

<div class="spgi-bg">
  <div class="container">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold text-gradient">Planillas de Horas Extras</h1>
            <p class="text-muted mb-0">Control, registro y exportación de horas extras de colaboradores para INTECSOL, SRL.</p>
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

    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4 animate__animated animate__fadeIn">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
        </div>
    @endif

    <div class="spgi-toolbar">
      <div class="toolbar-actions">
        <a href="{{ route('horas-extras.create') }}" class="btn btn-spgi">
          <i class="bi bi-plus-lg me-1"></i> Nueva Planilla
        </a>
      </div>
    </div>

    <div class="spgi-table-box animate__animated animate__fadeInUp">
      <div class="table-responsive">
        <table class="table spgi-table align-middle">
          <thead>
            <tr>
              <th class="text-start">Título / Descripción</th>
              <th>Fecha Registro</th>
              <th>Reportado Por</th>
              <th>Aprobado Por (Supervisor)</th>
              <th>Estado</th>
              <th>Total Horas</th>
              <th style="width: 180px;">Acciones</th>
            </tr>
          </thead>
          <tbody>
            @forelse($planillas as $p)
            <tr>
              <td class="text-start fw-bold">
                <a href="{{ route('horas-extras.show', $p->id) }}" class="text-decoration-none text-primary">
                  {{ $p->titulo }}
                </a>
              </td>
              <td>{{ $p->fecha_registro->format('d/m/Y') }}</td>
              <td>{{ $p->user->name ?? 'N/A' }}</td>
              <td>
                @if($p->responsable)
                  <span class="badge bg-secondary px-2.5 py-1.5 rounded-pill">{{ $p->responsable->name }}</span>
                @else
                  <span class="text-muted small">No aprobado</span>
                @endif
              </td>
              <td>
                @if($p->estado == 'Borrador')
                  <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">Borrador</span>
                @else
                  <span class="badge bg-success px-3 py-2 rounded-pill">{{ $p->estado }}</span>
                @endif
              </td>
              <td class="fw-bold">{{ number_format($p->total_horas, 2) }} hrs</td>
              <td class="text-center acciones">
                <a href="{{ route('horas-extras.show', $p->id) }}" class="btn btn-outline-info" title="Ver / Editar Detalles">
                  <i class="bi bi-eye"></i>
                </a>
                <a href="{{ route('horas-extras.pdf', $p->id) }}" target="_blank" class="btn btn-outline-primary" title="Ver PDF">
                  <i class="bi bi-file-earmark-pdf"></i>
                </a>
                <form action="{{ route('horas-extras.destroy', $p->id) }}" method="POST" class="d-inline">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-outline-danger" onclick="return confirm('¿Seguro que deseas eliminar esta planilla y todos sus detalles asociados?')" title="Eliminar">
                    <i class="bi bi-trash"></i>
                  </button>
                </form>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="7" class="text-center py-5">
                <i class="bi bi-clock-history display-4 text-muted mb-3 d-block"></i>
                <p class="text-muted">No se encontraron planillas de horas extras registradas.</p>
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
