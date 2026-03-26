@extends('layouts.app')

@section('page_title', 'Nuevo Requerimiento')

@section('content')

<style>
  :root{
    --spgi-primary:#0d6efd;
    --spgi-primary-2:#2b7bff;
    --spgi-ink:#0f172a;
    --spgi-muted:#64748b;
    --spgi-border: rgba(15, 23, 42, .10);
    --shadow: 0 18px 45px rgba(2, 6, 23, .10);
    --shadowSoft: 0 10px 24px rgba(2, 6, 23, .07);
  }

  body{
    background:
      radial-gradient(900px 400px at 20% 10%, rgba(13,110,253,.14), transparent 60%),
      radial-gradient(900px 450px at 85% 20%, rgba(168,85,247,.12), transparent 55%),
      radial-gradient(900px 450px at 70% 90%, rgba(34,197,94,.10), transparent 55%),
      linear-gradient(135deg, rgba(13,110,253,.10), rgba(168,85,247,.08) 45%, rgba(34,197,94,.08));
    background-attachment: fixed;
  }

  .spgi-bg{ background: transparent !important; padding: 24px 0; }

  .spgi-toolbar{
    background: rgba(255,255,255,.92);
    border: 1px solid var(--spgi-border);
    border-radius: 18px;
    box-shadow: var(--shadow);
    backdrop-filter: blur(6px);
    padding: 16px;
  }

  .spgi-title{
    font-weight: 800;
    font-size: 1.15rem;
    color: var(--spgi-ink);
    margin:0;
  }
  .spgi-subtitle{
    color: var(--spgi-muted);
    font-size: .9rem;
    margin-top: 4px;
  }

  .btn-spgi{
    background: linear-gradient(135deg, var(--spgi-primary), var(--spgi-primary-2));
    border: 0;
    color: #fff !important;
  }
  .btn-spgi:hover{ filter: brightness(.98); transform: translateY(-1px); }

  .btn-soft{
    background: #eef2ff;
    color: #1e40af;
    border: 1px solid rgba(30,64,175,.12);
  }
  .btn-soft:hover{ background:#e0e7ff; transform: translateY(-1px); }

  .toolbar-actions{
    display:flex;
    justify-content:flex-end;
    gap:10px;
    flex-wrap:wrap;
  }

  .toolbar-actions .btn{
    height:44px;
    border-radius:12px;
    padding:0 14px;
    white-space:nowrap;
    box-shadow: var(--shadowSoft);
    display:inline-flex;
    align-items:center;
    gap:.5rem;
    font-weight:700;
  }

  .spgi-card{
    margin-top: 14px;
    background: rgba(255,255,255,.92);
    border: 1px solid var(--spgi-border);
    border-radius: 18px;
    box-shadow: var(--shadow);
    backdrop-filter: blur(6px);
    overflow: hidden;
  }

  .spgi-card-header{
    padding: 14px 16px;
    border-bottom: 1px solid var(--spgi-border);
  }
  .spgi-card-body{ padding: 16px; }

  .spgi-label{
    font-weight:700;
    font-size:.9rem;
    color:var(--spgi-ink);
    margin-bottom:.35rem;
  }

  .spgi-control{
    border-radius:14px !important;
    border:1px solid var(--spgi-border) !important;
    padding:.7rem .9rem !important;
    box-shadow:0 8px 18px rgba(2,6,23,.04);
  }
  .spgi-control:focus{
    border-color:rgba(37,99,235,.35) !important;
    box-shadow:0 0 0 .22rem rgba(37,99,235,.12) !important;
  }
</style>

<div class="spgi-bg">
  <div class="container">

    {{-- TOPBAR --}}
    <div class="spgi-toolbar mb-3 d-flex justify-content-between align-items-center flex-wrap gap-3">
      <div>
        <h2 class="spgi-title">Nuevo Requerimiento</h2>
        <div class="spgi-subtitle">
          Proyecto: <b>{{ $proyecto->nombre }}</b>
        </div>
      </div>

      <div class="toolbar-actions">
        {{-- ✅ vuelve al listado de requerimientos del proyecto --}}
        <a href="{{ route('proyectos.requerimientos.index', $proyecto->id) }}" class="btn btn-soft">
          <i class="bi bi-arrow-left"></i> Volver
        </a>

        <button form="form-req" type="submit" class="btn btn-spgi">
          <i class="bi bi-save"></i> Guardar
        </button>
      </div>
    </div>

    {{-- FORM CARD --}}
    <div class="spgi-card">
      <div class="spgi-card-header">
        <strong>Datos del Requerimiento</strong>
        <div class="small text-muted">El cliente y contacto se heredarán automáticamente del proyecto.</div>
      </div>

      <div class="spgi-card-body">

        @if ($errors->any())
          <div class="alert alert-danger rounded-4">
            <ul class="mb-0">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <form id="form-req"
              method="POST"
              action="{{ route('proyectos.requerimientos.store', $proyecto->id) }}"
              enctype="multipart/form-data">
          @csrf

          <div class="row g-3">

            <div class="col-12">
              <label class="spgi-label">Descripción</label>
              <textarea name="texto_imagen"
                        class="form-control spgi-control"
                        rows="6"
                        required>{{ old('texto_imagen') }}</textarea>
            </div>

            <div class="col-md-4">
              <label class="spgi-label">Estado</label>
              <select name="estado_id" class="form-select spgi-control">
                @foreach(($estados ?? collect()) as $e)
                  <option value="{{ $e->id }}" {{ old('estado_id', 1) == $e->id ? 'selected' : '' }}>{{ $e->nombre }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-md-8">
              <label class="spgi-label">Adjunto (foto/pdf)</label>
              <input type="file" name="foto" class="form-control spgi-control" accept="image/*,application/pdf">
              <div class="text-muted small mt-1">Máx 8MB. Formatos: jpg, png, webp, pdf.</div>
            </div>

          </div>
        </form>

      </div>
    </div>

  </div>
</div>

@endsection