@extends('layouts.app')

@section('page_title', 'Wiki Documental')

@section('content')

<style>
  :root{
    --spgi-primary:#0d6efd;
    --spgi-primary-2:#2b7bff;
    --spgi-ink:#0f172a;
    --spgi-muted:#64748b;
    --spgi-border: rgba(15, 23, 42, .10);
    --spgi-radius: 16px;
    --spgi-shadow: 0 18px 45px rgba(2, 6, 23, .10);
  }

  .spgi-page{ padding: 12px 0 24px 0; }
  .spgi-head{ display:flex; justify-content:space-between; align-items:flex-start; gap:12px; flex-wrap:wrap; margin-bottom: 14px; }
  .page-title{ font-size: 1.65rem; font-weight: 800; letter-spacing: .2px; margin: 0; color: var(--spgi-ink); }
  .page-sub{ color: var(--spgi-muted); font-size: .95rem; margin-top: 4px; }
  .spgi-head-actions{ display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
  
  .badge-spgi{
    border-radius: 999px; padding: 8px 12px; font-weight: 700; font-size: .82rem;
    border: 1px solid rgba(0,0,0,.08); background: rgba(255,255,255,.9); color: #495057; box-shadow: 0 10px 24px rgba(2,6,23,.07);
  }

  .btn-spgi{
    background: linear-gradient(135deg, var(--spgi-primary), var(--spgi-primary-2));
    border: 0; color: #fff !important; min-height:44px; border-radius:12px; padding:0 14px; white-space:nowrap;
    box-shadow: 0 10px 24px rgba(2,6,23,.07); font-weight:700;
  }
  .btn-spgi:hover{ filter: brightness(.98); transform: translateY(-1px); }

  .spgi-card{
    border: 1px solid var(--spgi-border); border-radius: var(--spgi-radius);
    box-shadow: var(--spgi-shadow); background: rgba(255,255,255,.92);
    backdrop-filter: blur(6px); overflow: hidden;
  }
  .spgi-card .card-head{
    padding: 14px 16px; border-bottom: 1px solid rgba(0,0,0,.06);
    display: flex; gap: 12px; align-items: center; justify-content: space-between; flex-wrap: wrap;
  }
  .spgi-card .card-body-spgi{ padding: 0; }

  .search-wrap{ display: flex; gap: 10px; align-items: center; flex-wrap: wrap; width: 100%; }
  .search-group{
    display:flex; align-items:center; flex: 1 1 340px; min-width: 0;
    background:#fff; border:1px solid var(--spgi-border); border-radius:12px;
    box-shadow: 0 8px 20px rgba(2,6,23,.05); overflow:hidden;
  }
  .search-group .search-icon{
    min-width:44px; height:44px; display:flex; align-items:center; justify-content:center;
    color:#64748b; background:#fff;
  }
  .search-input{ border:0 !important; box-shadow:none !important; border-radius:0 !important; height:44px; min-width:0; }
  .btn-search, .btn-clear{ min-height:44px; border-radius:12px; padding:0 14px; white-space:nowrap; font-weight:700; }
  .btn-clear{ border: 1px solid rgba(0,0,0,.08); background:#fff; }

  .table-spgi{ margin: 0; }
  .table-spgi thead th{
    font-size: .92rem; letter-spacing: .2px; background: #0b1220; color:#fff;
    border-bottom: 1px solid rgba(255,255,255,.08) !important; padding: 12px 14px; vertical-align: middle;
  }
  .table-spgi tbody td{ padding: 12px 14px; vertical-align: middle; border-color: rgba(15,23,42,.08) !important; }
  .table-spgi tbody tr:hover{ background: #fbfcff; }
  
  .acciones{ display: inline-flex; gap: 8px; align-items: center; justify-content: center; flex-wrap: wrap; }
  .acciones .btn{
    height: 38px; padding: 0 12px; border-radius: 10px; display: inline-flex;
    align-items: center; justify-content: center; gap: 6px; font-weight: 600; font-size: .88rem; white-space: nowrap;
  }

  .tag-badge { background-color: #e2e8f0; color: #334155; padding: 4px 8px; border-radius: 6px; font-size: 0.8rem; margin-right: 4px; display: inline-block; margin-bottom: 4px; }
  .modal-content{ border-radius: 16px; border: 1px solid rgba(0,0,0,.08); box-shadow: 0 18px 40px rgba(0,0,0,.12); }
  .modal-header{ border-top-left-radius: 16px; border-top-right-radius: 16px; }
  .form-control{ border-radius: 12px; }

  @media (max-width: 767.98px){
    .spgi-head{ align-items:stretch; }
    .spgi-head-actions{ width:100%; justify-content:stretch; }
    .badge-spgi, .spgi-head-actions .btn{ width:100%; justify-content:center; }
    .search-wrap{ flex-direction:column; align-items:stretch; gap:8px; }
    .search-group{ flex: 0 0 auto !important; width:100%; }
    .btn-search, .btn-clear{ width:100%; }
  }

  /* Tag Input Styles */
  .tag-input-container {
    display: flex; flex-wrap: wrap; align-items: center; gap: 6px;
    padding: 8px 12px; background: #fff; border: 1px solid var(--spgi-border);
    border-radius: 12px; box-shadow: 0 8px 18px rgba(2,6,23,.03); min-height: 45px;
    cursor: text;
  }
  .tag-input-container:focus-within {
    border-color: rgba(13,110,253, 0.4);
    box-shadow: 0 0 0 0.2rem rgba(13,110,253, 0.15);
  }
  .tag-list { display: flex; flex-wrap: wrap; gap: 4px; align-items: center; }
  .tag-chip {
    background: #e0f2fe; color: #0369a1; padding: 4px 8px; border-radius: 6px;
    font-size: 0.85rem; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;
    border: 1px solid #bae6fd;
  }
  .tag-chip .remove-tag { cursor: pointer; color: #0284c7; font-weight: bold; font-size: 0.95rem; }
  .tag-chip .remove-tag:hover { color: #0369a1; }
  .tag-input-field {
    border: 0 !important; outline: none !important; padding: 4px 0 !important;
    background: transparent !important; flex-grow: 1; font-size: 0.9rem; min-width: 120px;
    box-shadow: none !important;
  }
</style>

<div class="spgi-page">
  <div class="container">

    <div class="spgi-head">
      <div>
        <h3 class="page-title">Wiki Documental</h3>
        <div class="page-sub">Consulta y sube documentos para base de conocimiento.</div>
      </div>

      <div class="spgi-head-actions">
        <a href="{{ route('wiki.index', ['categoria' => 'Manual'] + request()->except('categoria')) }}" 
           class="badge-spgi text-decoration-none {{ request('categoria') == 'Manual' ? 'active' : '' }}" 
           style="{{ request('categoria') == 'Manual' ? 'background: #0d6efd; color: #fff; border-color: #0d6efd;' : 'color: #0d6efd; border-color: rgba(13,110,253,.2);' }}" title="Filtrar por Manuales">
          <i class="bi bi-book me-1"></i> MANUALES
        </a>

        <a href="{{ route('wiki.index', ['categoria' => 'Script'] + request()->except('categoria')) }}" 
           class="badge-spgi text-decoration-none {{ request('categoria') == 'Script' ? 'active' : '' }}" 
           style="{{ request('categoria') == 'Script' ? 'background: #ffc107; color: #000; border-color: #ffc107;' : 'color: #856404; border-color: rgba(255,193,7,.3);' }}" title="Filtrar por Scripts">
          <i class="bi bi-code-slash me-1"></i> SCRIPTS
        </a>

        <a href="{{ route('wiki.index', ['categoria' => 'Query'] + request()->except('categoria')) }}" 
           class="badge-spgi text-decoration-none {{ request('categoria') == 'Query' ? 'active' : '' }}" 
           style="{{ request('categoria') == 'Query' ? 'background: #198754; color: #fff; border-color: #198754;' : 'color: #198754; border-color: rgba(25,135,84,.2);' }}" title="Filtrar por Queries">
          <i class="bi bi-database me-1"></i> QUERY
        </a>

        <div class="d-flex align-items-center gap-2 ms-lg-auto">
          <a href="{{ route('wiki.index', ['categoria' => 'Todos'] + request()->except('categoria')) }}" class="badge-spgi text-decoration-none {{ request('categoria', 'Todos') == 'Todos' ? 'border-primary text-primary' : '' }}">
            <i class="bi bi-file-earmark-text me-1"></i>
            Total: {{ $documents->total() ?? 0 }}
          </a>

          <button class="btn btn-spgi d-flex align-items-center" type="button" data-bs-toggle="modal" data-bs-target="#modalDocumento">
            <i class="bi bi-cloud-arrow-up me-1"></i> Subir Documento
          </button>
        </div>
      </div>
    </div>

    @if(session('success'))
      <div class="alert alert-success text-center" id="alerta-exito">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
      </div>
    @endif

    @if(session('error'))
      <div class="alert alert-danger text-center">
        <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
      </div>
    @endif
    
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="spgi-card">
      <div class="card-head">
        <form class="search-wrap m-0" action="{{ route('wiki.index') }}" method="GET">
          <div class="search-group">
            <span class="search-icon">
              <i class="bi bi-search"></i>
            </span>
            <input class="form-control search-input" type="search" name="search" value="{{ request('search') }}" placeholder="Buscar por título, descripción o etiqueta...">
          </div>

          <div class="filter-group">
            <select name="estado" class="form-select" onchange="this.form.submit()" style="height: 44px; border-radius: 12px; border: 1px solid var(--spgi-border); min-width: 140px; font-weight: 600;">
              <option value="Todos" {{ request('estado') == 'Todos' ? 'selected' : '' }}>Todos</option>
              <option value="Validado" {{ request('estado') == 'Validado' ? 'selected' : '' }}>Validados</option>
              <option value="Sin validar" {{ request('estado') == 'Sin validar' ? 'selected' : '' }}>Sin validar</option>
            </select>
          </div>

          <button class="btn btn-outline-success btn-search" type="submit">Buscar</button>

          @if(request('search') || (request('estado') && request('estado') !== 'Todos'))
            <a href="{{ route('wiki.index') }}" class="btn btn-clear">Limpiar</a>
          @endif
        </form>
      </div>

      <div class="card-body-spgi">
        <div class="table-responsive">
          <table class="table table-spgi table-bordered align-middle mb-0" id="tabla-wiki" data-current-filter="{{ request('estado') }}">
            <thead>
              <tr>
                <th style="width: 20%;">Documento</th>
                <th style="width: 20%;">Descripción</th>
                <th>Categoría</th>
                <th>Etiquetas</th>
                <th>Estado</th>
                <th class="text-center" style="width: 140px;">Acciones</th>
              </tr>
            </thead>
            <tbody>
            @forelse($documents as $doc)
              <tr id="row-doc-{{ $doc->id }}">
                <td class="fw-semibold text-primary">
                    <i class="bi bi-file-earmark-pdf me-2 text-danger"></i>{{ $doc->title }}
                </td>
                <td class="text-muted" style="font-size: .9rem;">
                    {{ \Illuminate\Support\Str::limit($doc->description, 60) }}
                </td>
                <td>
                    @if($doc->categoria == 'Manual')
                        <span class="badge rounded-pill bg-primary text-white" style="font-size: 0.75rem;">{{ $doc->categoria }}</span>
                    @elseif($doc->categoria == 'Script')
                        <span class="badge rounded-pill bg-warning text-dark" style="font-size: 0.75rem;">{{ $doc->categoria }}</span>
                    @elseif($doc->categoria == 'Query')
                        <span class="badge rounded-pill bg-success text-white" style="font-size: 0.75rem;">{{ $doc->categoria }}</span>
                    @elseif($doc->categoria)
                        <span class="badge rounded-pill bg-info text-dark" style="font-size: 0.75rem;">{{ $doc->categoria }}</span>
                    @else
                        <span class="text-muted small">-</span>
                    @endif
                </td>
                <td>
                    @if($doc->tags)
                        @foreach(explode(',', $doc->tags) as $tag)
                            <span class="tag-badge">{{ trim($tag) }}</span>
                        @endforeach
                    @else
                        <span class="text-muted small">Sin etiquetas</span>
                    @endif
                </td>
                <td class="status-cell">
                    @if($doc->estado == 'Validado')
                        <span class="badge bg-success text-white">Validado</span>
                    @else
                        <span class="badge bg-warning text-dark">Sin validar</span>
                    @endif
                </td>
                <td class="text-center">
                  <div class="acciones">
                    @if($doc->estado == 'Validado' || (auth()->user()->role && in_array(auth()->user()->role->nombre, ['Administracion', 'Encargado'])))
                    <a href="{{ route('wiki.download', $doc->id) }}" class="btn btn-success text-white" style="width: 32px; height: 32px; padding: 0;" title="Descargar">
                      <i class="bi bi-download"></i>
                    </a>
                    @else
                    <button class="btn btn-secondary text-white-50" style="width: 32px; height: 32px; padding: 0;" title="No disponible hasta validar" disabled>
                      <i class="bi bi-download"></i>
                    </button>
                    @endif

                    <button class="btn btn-warning" type="button" data-bs-toggle="modal" data-bs-target="#modalEditar{{ $doc->id }}" style="width: 32px; height: 32px; padding: 0;" title="Editar">
                      <i class="bi bi-pencil-square"></i>
                    </button>

                    <button class="btn btn-danger" type="button" data-bs-toggle="modal" data-bs-target="#confirmarEliminar{{ $doc->id }}" style="width: 32px; height: 32px; padding: 0;" title="Eliminar">
                      <i class="bi bi-trash"></i>
                    </button>

                    @if(auth()->user()->role && in_array(auth()->user()->role->nombre, ['Administracion', 'Encargado']) && $doc->estado !== 'Validado')
                    <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#confirmarAprobar{{ $doc->id }}" style="width: 32px; height: 32px; padding: 0;" title="Validar">
                      <i class="bi bi-check-circle"></i>
                    </button>
                    @endif
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center py-5 text-muted">
                  <i class="bi bi-inbox fs-1 d-block mb-3"></i>No se encontraron documentos
                </td>
              </tr>
            @endforelse
            </tbody>
          </table>
        </div>
      </div>
      
      @if($documents->hasPages())
      <div class="card-footer bg-white border-top border-light px-4 py-3">
          {{ $documents->links() }}
      </div>
      @endif
      
    </div>

  </div>
</div>

{{-- MODALES DE ELIMINAR --}}
@foreach($documents as $doc)
  <div class="modal fade" id="confirmarEliminar{{ $doc->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
      <div class="modal-content border-0 shadow" style="border-radius: 20px;">
        <div class="modal-body text-center py-4 px-3">
          <div class="mb-3">
             <div class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 64px; height: 64px; background: rgba(220, 53, 69, 0.1);">
                 <i class="bi bi-trash-fill text-danger" style="font-size: 1.85rem;"></i>
             </div>
          </div>
          <h5 class="fw-bold text-dark mb-2">Eliminar Documento</h5>
          <p class="text-secondary small mb-1">¿Estás seguro de que deseas eliminar <strong>{{ $doc->title }}</strong>?</p>
          <div class="d-flex justify-content-center gap-2 mt-4 w-100">
            <button type="button" class="btn btn-light border px-3 w-50" data-bs-dismiss="modal">Cancelar</button>
            <form action="{{ route('wiki.destroy', $doc->id) }}" method="POST" class="m-0 w-50">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn btn-danger w-100">Eliminar</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="confirmarAprobar{{ $doc->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
      <div class="modal-content border-0 shadow" style="border-radius: 20px;">
        <div class="modal-body text-center py-4 px-3">
          <div class="mb-3">
             <div class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 64px; height: 64px; background: rgba(13, 110, 253, 0.1);">
                 <i class="bi bi-check-circle-fill text-primary" style="font-size: 1.85rem;"></i>
             </div>
          </div>
          <h5 class="fw-bold text-dark mb-2">Validar Documento</h5>
          <p class="text-secondary small mb-0">¿Deseas validar <strong>{{ $doc->title }}</strong>?</p>
          <div class="d-flex justify-content-center gap-2 mt-4 w-100">
            <button type="button" class="btn btn-light border px-3 w-50" data-bs-dismiss="modal">Cancelar</button>
            <form action="{{ route('wiki.approve', $doc->id) }}" method="POST" class="m-0 w-50 form-validar-ajax" data-id="{{ $doc->id }}">
              @csrf
              <button type="submit" class="btn btn-primary w-100">Validar</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
@endforeach

{{-- MODALES DE EDITAR --}}
@foreach($documents as $doc)
  <div class="modal fade" id="modalEditar{{ $doc->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header bg-warning text-dark">
          <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i> Editar Documento</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form action="{{ route('wiki.update', $doc->id) }}" method="POST" enctype="multipart/form-data">
          @csrf
          @method('PUT')
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label fw-semibold">Título <span class="text-danger">*</span></label>
              <input type="text" class="form-control" name="title" value="{{ $doc->title }}" required>
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Categoría <span class="text-danger">*</span></label>
              <select class="form-select" name="categoria" required>
                <option value="" disabled>Seleccione una categoría</option>
                <option value="Manual" {{ $doc->categoria == 'Manual' ? 'selected' : '' }}>Manual</option>
                <option value="Script" {{ $doc->categoria == 'Script' ? 'selected' : '' }}>Script</option>
                <option value="Query" {{ $doc->categoria == 'Query' ? 'selected' : '' }}>Query</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Descripción</label>
              <textarea class="form-control" name="description" rows="3">{{ $doc->description }}</textarea>
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Etiquetas</label>
              <div class="tag-input-container" onclick="this.querySelector('.tag-input-field').focus()">
                <div class="tag-list" id="tag-list-edit{{ $doc->id }}"></div>
                <input type="text" class="tag-input-field" placeholder="Agrega etiquetas..." data-target="edit{{ $doc->id }}">
              </div>
              <input type="hidden" name="tags" id="tags-hidden-edit{{ $doc->id }}" value="{{ $doc->tags }}">
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Reemplazar Archivo (Opcional)</label>
              <input type="file" class="form-control" name="file">
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-warning">Guardar Cambios</button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endforeach

{{-- MODAL SUBIR DOCUMENTO --}}
<div class="modal fade" id="modalDocumento" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title"><i class="bi bi-cloud-arrow-up me-2"></i> Subir Documento</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form action="{{ route('wiki.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label fw-semibold">Título <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="title" required>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Categoría <span class="text-danger">*</span></label>
            <select class="form-select" name="categoria" required>
              <option value="" selected disabled>Seleccione una categoría</option>
              <option value="Manual">Manual</option>
              <option value="Script">Script</option>
              <option value="Query">Query</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Descripción</label>
            <textarea class="form-control" name="description" rows="3"></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Etiquetas</label>
            <div class="tag-input-container" onclick="this.querySelector('.tag-input-field').focus()">
              <div class="tag-list" id="tag-list-create"></div>
              <input type="text" class="tag-input-field" placeholder="Agrega etiquetas..." data-target="create">
            </div>
            <input type="hidden" name="tags" id="tags-hidden-create">
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Archivo <span class="text-danger">*</span></label>
            <input type="file" class="form-control" name="file" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Subir</button>
        </div>
      </form>
    </div>
  </div>
</div>

@push('scripts')
<script>
  const alerta = document.getElementById('alerta-exito');
  if (alerta) {
    setTimeout(() => {
      alerta.style.transition = "opacity 0.4s";
      alerta.style.opacity = '0';
      setTimeout(() => alerta.remove(), 400);
    }, 3000);
  }

  // Tag Input Logic
  window.removeTag = function(id, index) {
      if (typeof window[`tags_${id}`] !== 'undefined') {
          window[`tags_${id}`].splice(index, 1);
          window[`render_${id}`]();
      }
  }

  function initTagInput(targetId) {
    const list = document.getElementById(`tag-list-${targetId}`);
    const hidden = document.getElementById(`tags-hidden-${targetId}`);
    const input = document.querySelector(`.tag-input-field[data-target="${targetId}"]`);
    if (!list || !hidden || !input) return;
    window[`tags_${targetId}`] = hidden.value ? hidden.value.split(',').map(t => t.trim()).filter(t => t) : [];
    window[`render_${targetId}`] = function() {
      list.innerHTML = '';
      window[`tags_${targetId}`].forEach((tag, index) => {
        const chip = document.createElement('span');
        chip.className = 'tag-chip';
        chip.innerHTML = `${tag} <span class="remove-tag" onclick="removeTag('${targetId}', ${index})">&times;</span>`;
        list.appendChild(chip);
      });
      hidden.value = window[`tags_${targetId}`].join(',');
    }
    input.addEventListener('keydown', function(e) {
      if (e.key === ' ' || e.key === ',') {
        e.preventDefault();
        const value = input.value.trim().replace(/,/g, '');
        if (value && !window[`tags_${targetId}`].includes(value)) {
          window[`tags_${targetId}`].push(value);
          window[`render_${targetId}`]();
        }
        input.value = '';
      } else if (e.key === 'Backspace' && input.value === '' && window[`tags_${targetId}`].length > 0) {
        window[`tags_${targetId}`].pop();
        window[`render_${targetId}`]();
      }
    });
    window[`render_${targetId}`]();
  }

  // ✅ AJAX Validation Logic
  document.querySelectorAll('.form-validar-ajax').forEach(form => {
    form.addEventListener('submit', function(e) {
      e.preventDefault();
      const docId = this.getAttribute('data-id');
      const url = this.getAttribute('action');
      const modal = bootstrap.Modal.getInstance(document.getElementById(`confirmarAprobar${docId}`));
      const submitBtn = this.querySelector('button[type="submit"]');

      submitBtn.disabled = true;
      submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';

      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
      
      if (!csrfToken) {
        alert('Error de seguridad: Token CSRF no encontrado.');
        submitBtn.disabled = false;
        submitBtn.innerHTML = 'Validar';
        return;
      }

      fetch(url, {
        method: 'POST',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': csrfToken
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          modal.hide();
          
          const row = document.getElementById(`row-doc-${docId}`);
          const currentFilter = document.getElementById('tabla-wiki').getAttribute('data-current-filter');

          if (currentFilter === 'Sin validar') {
            // Fade out and remove
            row.style.transition = 'all 0.5s ease';
            row.style.opacity = '0';
            row.style.transform = 'translateX(20px)';
            setTimeout(() => row.remove(), 550);
          } else {
            // Just update badge and remove validate button
            const statusCell = row.querySelector('.status-cell');
            statusCell.innerHTML = '<span class="badge bg-success text-white">Validado</span>';
            
            const validateBtn = row.querySelector('.btn-primary[title="Validar"]');
            if (validateBtn) validateBtn.remove();
          }

          // Show small toast or notification if desired
          console.log(data.message);
        } else {
          alert('Error: ' + data.message);
          submitBtn.disabled = false;
          submitBtn.innerHTML = 'Validar';
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Ocurrió un error al procesar la solicitud.');
        submitBtn.disabled = false;
        submitBtn.innerHTML = 'Validar';
      });
    });
  });

  document.addEventListener('DOMContentLoaded', () => {
      initTagInput('create');
      // Re-init for edit modals if needed
      @foreach($documents as $doc)
         initTagInput('edit{{ $doc->id }}');
      @endforeach
  });
</script>
@endpush

@endsection
