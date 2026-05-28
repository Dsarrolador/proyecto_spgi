@extends('layouts.app')

@section('page_title', 'Plantillas de Cuestionarios Técnicos')

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
  .spgi-table tbody td{ border-color: var(--border-main) !important; color: var(--text-main); padding: 16px; }
  .spgi-table tbody tr:hover{ background: rgba(16, 185, 129, 0.05); }

  .acciones .btn{ width: 38px; height: 38px; display: inline-flex; align-items: center; justify-content: center; padding: 0; border-radius: 10px; }
</style>

<div class="spgi-bg">
  <div class="container">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold text-gradient">Plantillas de Cuestionarios</h1>
            <p class="text-muted mb-0">Administra los cuestionarios y checklists técnicos para leads de clientes.</p>
        </div>
        <a href="{{ route('leads.bienvenido') }}" class="btn btn-outline-secondary rounded-pill">
            <i class="bi bi-arrow-left me-2"></i> Volver a Ventas
        </a>
    </div>

    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4" role="alert">
        <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    @endif

    <div class="spgi-toolbar">
      <div class="toolbar-actions">
        <a href="{{ route('checklists.create') }}" class="btn btn-spgi d-flex align-items-center gap-2">
          <i class="bi bi-plus-lg"></i> Nueva Plantilla
        </a>
      </div>
    </div>

    <div class="spgi-table-box">
        <div class="table-responsive spgi-table">
            <table class="table table-bordered align-middle mb-0 text-center">
                <thead>
                    <tr>
                        <th style="width: 80px;">ID</th>
                        <th class="text-start">Nombre de la Plantilla</th>
                        <th class="text-start">Descripción</th>
                        <th>Preguntas</th>
                        <th>Fecha Creación</th>
                        <th style="width: 150px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($templates as $template)
                    <tr>
                        <td>{{ $template->id }}</td>
                        <td class="text-start fw-bold">{{ $template->nombre }}</td>
                        <td class="text-start text-muted">{{ $template->descripcion ?? 'Sin descripción' }}</td>
                        <td>
                            <span class="badge bg-primary rounded-pill px-3">
                                {{ $template->questions()->count() }}
                            </span>
                        </td>
                        <td>{{ $template->created_at->format('d/m/Y') }}</td>
                        <td>
                            <div class="d-flex justify-content-center gap-2 acciones">
                                <a href="{{ route('checklists.edit', $template->id) }}" class="btn btn-outline-info" title="Configurar Preguntas y Opciones">
                                    <i class="bi bi-gear-fill"></i>
                                </a>
                                <form action="{{ route('checklists.destroy', $template->id) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar esta plantilla y todas sus preguntas?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" title="Eliminar Plantilla">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-5 text-muted">No hay plantillas de cuestionario creadas aún.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

  </div>
</div>

@endsection
