@extends('layouts.app')

@section('page_title', 'Visitas de Campo')

@section('content')
<style>
  .spgi-bg { padding: 24px 0; }
  .spgi-title { font-weight: 800; font-size: 1.6rem; color: var(--text-main); letter-spacing: -.5px; margin: 0; }
  
  .btn-spgi {
    background: linear-gradient(135deg, var(--spgi-primary), #2563eb);
    border: 0; color: #fff !important; min-height: 46px; border-radius: 14px; padding: 0 20px;
    box-shadow: 0 10px 25px var(--spgi-primary-glow); font-weight: 700;
  }
  .btn-spgi:hover { filter: brightness(1.1); transform: translateY(-1px); }

  .visit-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 20px;
    margin-top: 20px;
  }

  .visit-card {
    background: var(--bg-surface-glass);
    border: 1px solid var(--border-main);
    border-radius: 20px;
    padding: 20px;
    box-shadow: var(--shadow-main);
    backdrop-filter: blur(16px);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
  }

  .visit-card:hover {
    transform: translateY(-4px);
    border-color: var(--spgi-primary);
    box-shadow: 0 15px 35px var(--spgi-primary-glow);
  }

  .status-badge {
    padding: 6px 14px;
    border-radius: 999px;
    font-weight: 800;
    font-size: 0.75rem;
    text-transform: uppercase;
  }

  .status-critico { background: rgba(239, 68, 68, 0.15); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.2); }
  .status-regular { background: rgba(245, 158, 11, 0.15); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.2); }
  .status-estable, .status-optimo { background: rgba(16, 185, 129, 0.15); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.2); }
  .status-pendiente { background: rgba(156, 163, 175, 0.15); color: #9ca3af; border: 1px solid rgba(156, 163, 175, 0.2); }

  .meta-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.85rem;
    color: var(--text-muted);
    margin-bottom: 6px;
  }

  .meta-item i { color: var(--spgi-primary); }

  @media (max-width: 575.98px) {
    .visit-grid {
      grid-template-columns: 1fr;
    }
  }
</style>

<div class="spgi-bg">
  <div class="container">

    {{-- ALERTAS --}}
    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show mb-4" role="alert" style="border-radius: 12px;">
        <strong>Listo:</strong> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
      </div>
    @endif

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
      <div>
        <h4 class="spgi-title">Visitas de Campo</h4>
        <p class="text-muted small mb-0">Gestiona y ejecuta checklists técnicos durante visitas técnicas o comerciales.</p>
      </div>

      <div class="d-flex gap-2">
        <a href="{{ route('leads.bienvenido') }}" class="btn btn-outline-secondary rounded-pill px-3 d-flex align-items-center gap-2">
          <i class="bi bi-arrow-left"></i> Dashboard
        </a>
        <a href="{{ route('visitas.create') }}" class="btn btn-spgi d-flex align-items-center gap-2">
          <i class="bi bi-geo-alt-fill"></i> Registrar Visita
        </a>
      </div>
    </div>

    {{-- GRID DE VISITAS --}}
    @if($visitas->count() > 0)
      <div class="visit-grid">
        @foreach($visitas as $v)
          <div class="visit-card">
            <div>
              <div class="d-flex justify-content-between align-items-start mb-3">
                <span class="badge bg-secondary text-white" style="font-size: 0.75rem;">ID: #{{ $v->id }}</span>
                @if($v->estado_cliente)
                  <span class="status-badge status-{{ strtolower(str_replace(['ó', 'á', 'é', 'í', 'ú'], ['o', 'a', 'e', 'i', 'u'], $v->estado_cliente)) }}">
                    {{ $v->estado_cliente }}
                  </span>
                @else
                  <span class="status-badge status-pendiente">
                    Pendiente
                  </span>
                @endif
              </div>

              <h5 class="fw-bold text-white mb-3">{{ $v->nombre_visitado }}</h5>

              <div class="meta-item">
                <i class="bi bi-envelope-fill"></i>
                <span>{{ $v->correo_visitado ?? 'Sin correo registrado' }}</span>
              </div>

              <div class="meta-item">
                <i class="bi bi-person-fill-check"></i>
                <span>Recibió: {{ $v->nombre_recibio ?? 'Sin especificar' }} {{ $v->telefono_recibio ? '(Tel: ' . $v->telefono_recibio . ')' : '' }}</span>
              </div>

              <div class="meta-item">
                <i class="bi bi-clipboard2-check-fill"></i>
                <span>Plantilla: <strong>{{ $v->template->nombre ?? 'N/A' }}</strong></span>
              </div>

              <div class="meta-item">
                <i class="bi bi-calendar-event-fill"></i>
                <span>Fecha: {{ \Carbon\Carbon::parse($v->created_at)->format('d/m/Y h:i A') }}</span>
              </div>
            </div>

            <div class="mt-4 pt-3 border-top border-secondary border-opacity-25 d-flex justify-content-between align-items-center">
              <div class="text-white fw-bold">
                @if($v->estado_cliente)
                  <span class="text-warning fs-5">{{ $v->total_puntos }}</span> <small class="text-muted">pts</small>
                @else
                  <small class="text-muted">Sin evaluar</small>
                @endif
              </div>

              <div class="d-flex gap-2">
                @if($v->estado_cliente)
                  <a href="{{ route('visitas.show', $v->id) }}" class="btn btn-sm btn-outline-info rounded-pill px-3">
                    <i class="bi bi-eye"></i> Ver
                  </a>
                @endif
                <a href="{{ route('visitas.edit', $v->id) }}" class="btn btn-sm btn-warning text-dark rounded-pill px-3">
                  <i class="bi bi-pencil-square"></i> {{ $v->estado_cliente ? 'Editar' : 'Evaluar' }}
                </a>
                <form action="{{ route('visitas.destroy', $v->id) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar esta visita?');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-danger rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="Eliminar Visita">
                    <i class="bi bi-trash"></i>
                  </button>
                </form>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    @else
      <div class="visit-card py-5 text-center text-muted" style="min-height: 250px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
        <i class="bi bi-geo-alt fs-1 mb-3 d-block text-secondary"></i>
        <h5 class="text-white fw-bold">No hay visitas registradas</h5>
        <p class="text-muted mb-4 small" style="max-width: 320px;">Registra tu primera visita para aplicar cuestionarios técnicos en terreno.</p>
        <a href="{{ route('visitas.create') }}" class="btn btn-spgi rounded-pill px-4">
          <i class="bi bi-geo-alt-fill me-2"></i> Registrar Visita
        </a>
      </div>
    @endif

  </div>
</div>
@endsection
