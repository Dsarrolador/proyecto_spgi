@extends('layouts.app')

@section('page_title', 'Bitácora de Clientes')

@section('content')
<style>
  .admin-bg { padding: 12px 0 24px 0; }
  .admin-toolbar {
    background: var(--bg-surface-glass); border: 1px solid var(--border-main);
    border-radius: 20px; box-shadow: var(--shadow-main); backdrop-filter: blur(16px); padding: 20px;
  }
  .admin-title { font-weight: 800; color: var(--text-main); letter-spacing: -.5px; margin: 0; font-size: 1.6rem; }
  
  .btn-outline-admin {
    height:46px; border-radius:12px; padding:0 20px;
    border: 1px solid var(--border-main); background: var(--bg-surface); color: var(--text-main);
  }

  .admin-table-box {
    background: var(--bg-surface-glass); border: 1px solid var(--border-main);
    border-radius: 20px; box-shadow: var(--shadow-main); overflow: hidden; backdrop-filter: blur(16px);
  }
  
  .table-admin { margin-bottom: 0; background: transparent; }
  .table-admin thead th {
    background: #0b1220; color: #fff; border-color: rgba(255,255,255,0.08) !important;
    font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; padding: 16px;
  }
  .table-admin tbody td { border-color: var(--border-main) !important; color: var(--text-main); padding: 16px; }
  .table-admin tbody tr:hover { background: rgba(var(--spgi-primary), 0.05); }
</style>

<div class="admin-bg">
  <div class="container">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold text-gradient">Bitácora de Clientes (Administración)</h1>
            <p class="text-muted mb-0">Control de contratos, documentos legales e interacciones comerciales manuales.</p>
        </div>
        <a href="{{ route('administracion.bienvenido') }}" class="btn btn-outline-secondary rounded-pill">
            <i class="bi bi-arrow-left me-2"></i> Volver al Panel
        </a>
    </div>

    <div class="admin-toolbar mb-4">
      <form action="{{ route('administracion.bitacora-clientes.index') }}" method="GET" class="d-flex gap-2">
        <input type="text" name="search" class="form-control bg-transparent text-white" placeholder="Buscar por nombre de cliente o RNC..." value="{{ request('search') }}" style="max-width: 350px; border-color: var(--border-main);">
        <button type="submit" class="btn btn-outline-admin"><i class="bi bi-search"></i> Buscar</button>
      </form>
    </div>

    <div class="admin-table-box">
      <div class="table-responsive">
        <table class="table table-admin align-middle text-center">
          <thead>
            <tr>
              <th class="text-start ps-4">Nombre Comercial</th>
              <th>RNC</th>
              <th>Teléfono</th>
              <th>Dirección</th>
              <th style="width: 220px;">Acciones</th>
            </tr>
          </thead>
          <tbody>
            @forelse($clientes as $cliente)
              <tr>
                <td class="text-start ps-4 fw-bold text-main">{{ $cliente->nombre }}</td>
                <td>{{ $cliente->rnc ?? '---' }}</td>
                <td>{{ $cliente->telefono_principal ?? '---' }}</td>
                <td class="text-muted small text-truncate" style="max-width: 300px;">{{ $cliente->direccion_escrita ?? '---' }}</td>
                <td>
                  <a href="{{ route('administracion.bitacora-clientes.show', $cliente->id) }}" class="btn btn-primary d-inline-flex align-items-center gap-2 rounded-pill px-3 py-2 fw-bold" style="font-size: 0.85rem;">
                    <i class="bi bi-book-half"></i> Ver Bitácora
                  </a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="py-5 text-muted">
                  <i class="bi bi-people fs-2 d-block mb-2"></i> No se encontraron clientes registrados.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <div class="mt-4">
      {{ $clientes->withQueryString()->links() }}
    </div>

  </div>
</div>
@endsection
