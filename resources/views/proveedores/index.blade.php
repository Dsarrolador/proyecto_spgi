@extends('layouts.app')

@section('page_title', 'Gestión de Proveedores')

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

  .toolbar-actions{ display:flex; justify-content:flex-end; gap:10px; flex-wrap:wrap; margin-bottom: 20px; }
  .toolbar-actions .btn{ min-height:46px; border-radius:14px; padding:0 24px; font-weight: 700; }

  .toolbar-selects{ display:flex; gap:12px; align-items:center; flex-wrap:wrap; margin:0; }
  .toolbar-selects .form-control{
    height:46px; border-radius:12px; border:1px solid var(--border-main);
    background-color: var(--bg-surface); color: var(--text-main); min-width:300px;
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
  .spgi-table tbody tr:hover{ background: rgba(59, 130, 246, 0.05); }

  .acciones .btn{ width: 38px; height: 38px; display: inline-flex; align-items: center; justify-content: center; padding: 0; border-radius: 10px; }
  
  .provider-avatar {
    width: 40px; height: 40px; background: rgba(59, 130, 246, 0.1); color: var(--spgi-primary);
    border-radius: 12px; display: flex; align-items: center; justify-content: center; font-weight: 700;
  }
</style>

<div class="spgi-bg">
  <div class="container">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold text-gradient">Proveedores</h1>
            <p class="text-muted mb-0">Gestión de proveedores de servicios y productos.</p>
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
        <a href="{{ route('proveedores.create') }}" class="btn btn-spgi">
          <i class="bi bi-plus-lg me-1"></i> Nuevo Proveedor
        </a>
      </div>

      <form action="{{ route('proveedores.index') }}" method="GET" class="toolbar-selects">
        <div class="input-group">
            <span class="input-group-text bg-surface border-end-0" style="border-radius: 12px 0 0 12px; border: 1px solid var(--border-main);"><i class="bi bi-search"></i></span>
            <input type="text" name="search" class="form-control border-start-0" placeholder="Buscar por nombre, RNC o correo..." value="{{ request('search') }}" style="border-radius: 0 12px 12px 0;">
            <button type="submit" class="btn btn-primary ms-2 rounded-pill px-4">Buscar</button>
        </div>
      </form>
    </div>

    <div class="spgi-table-box animate__animated animate__fadeInUp">
      <div class="table-responsive">
        <table class="table spgi-table align-middle">
          <thead>
            <tr>
              <th style="width: 60px;">#</th>
              <th>Proveedor</th>
              <th>RNC / Cédula</th>
              <th>Contacto</th>
              <th>Teléfono / Correo</th>
              <th>Categoría</th>
              <th style="width: 150px;">Acciones</th>
            </tr>
          </thead>
          <tbody>
            @forelse($proveedores as $p)
            <tr>
              <td class="text-center text-muted small">{{ $p->id }}</td>
              <td>
                <div class="d-flex align-items-center gap-3">
                    <div class="provider-avatar">
                        {{ strtoupper(substr($p->nombre, 0, 1)) }}
                    </div>
                    <div>
                        <div class="fw-bold">{{ $p->nombre }}</div>
                        <div class="text-muted small">{{ $p->direccion }}</div>
                    </div>
                </div>
              </td>
              <td class="text-center fw-medium">{{ $p->rnc ?? '---' }}</td>
              <td>
                <div class="fw-medium text-dark">{{ $p->persona_contacto ?? '---' }}</div>
              </td>
              <td>
                <div><i class="bi bi-telephone text-muted me-1"></i> {{ $p->telefono ?? '---' }}</div>
                <div class="small text-primary">{{ $p->correo ?? '---' }}</div>
              </td>
              <td class="text-center">
                <span class="badge bg-light text-dark border rounded-pill px-3">{{ $p->categoria ?? 'General' }}</span>
              </td>
              <td class="text-center acciones">
                <a href="{{ route('proveedores.edit', $p->id) }}" class="btn btn-outline-primary" title="Editar">
                  <i class="bi bi-pencil"></i>
                </a>
                <form action="{{ route('proveedores.destroy', $p->id) }}" method="POST" class="d-inline">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-outline-danger" onclick="return confirm('¿Seguro que deseas eliminar este proveedor?')" title="Eliminar">
                    <i class="bi bi-trash"></i>
                  </button>
                </form>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="7" class="text-center py-5">
                <i class="bi bi-truck display-4 text-muted mb-3 d-block"></i>
                <p class="text-muted">No se encontraron proveedores registrados.</p>
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      
      @if($proveedores->hasPages())
      <div class="p-4 border-top">
        {{ $proveedores->links() }}
      </div>
      @endif
    </div>

  </div>
</div>

@endsection
