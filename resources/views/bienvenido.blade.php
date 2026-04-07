@extends('layouts.app')

@section('page_title', 'Bienvenidos')

@section('content')
<style>
  :root{
    --spgi-border: rgba(15, 23, 42, .10);
    --spgi-ink:#0f172a;
    --spgi-muted:#64748b;
  }

  body{
    background:
      radial-gradient(900px 600px at 15% 18%, rgba(59,130,246,.18), transparent 55%),
      radial-gradient(800px 600px at 90% 20%, rgba(236,72,153,.14), transparent 55%),
      radial-gradient(700px 500px at 50% 90%, rgba(34,197,94,.12), transparent 60%),
      linear-gradient(180deg, #f7f8fb 0%, #eef2f7 45%, #f7f8fb 100%);
    background-attachment: fixed;
  }

  .welcome-bg{
    min-height: calc(100vh - 110px);
    display: grid;
    place-items: center;
    padding: 12px 0 24px 0;
  }

  .welcome-card{
    width: 100%;
    max-width: 860px;
    border-radius: 22px;
    background: rgba(255,255,255,.90);
    border: 1px solid var(--spgi-border);
    box-shadow: 0 30px 80px rgba(0,0,0,.18);
    backdrop-filter: blur(12px);
    padding: 34px 30px;
  }

  .badge-chip{
    display:inline-flex;
    align-items:center;
    gap:8px;
    padding: 8px 14px;
    border-radius: 999px;
    background: rgba(17,24,39,.06);
    font-weight: 700;
    border: 1px solid rgba(17,24,39,.08);
    font-size: .95rem;
  }

  .badge-chip i{
    color:#2563eb;
  }

  .welcome-title{
    font-weight: 900;
    font-size: 2rem;
    line-height: 1.15;
    margin-top: 18px;
    margin-bottom: 10px;
    color: var(--spgi-ink);
    word-break: break-word;
  }

  .welcome-subtitle{
    color: var(--spgi-muted);
    margin: 0;
    font-size: 1rem;
  }

  .divider{
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(17,24,39,.18), transparent);
    margin: 22px 0;
  }

  .quick-title{
    font-weight: 800;
    margin-bottom: 14px;
    text-align: left;
    color: var(--spgi-ink);
    font-size: 1.08rem;
  }

  .quick-grid{
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 16px;
  }

  .quick-card{
    text-decoration: none;
    color: inherit;
    border-radius: 16px;
    border: 1px solid var(--spgi-border);
    background: rgba(255,255,255,.78);
    box-shadow: 0 14px 30px rgba(2,6,23,.08);
    padding: 16px;
    display: flex;
    align-items: center;
    gap: 14px;
    transition: .2s ease;
    min-height: 88px;
  }

  .quick-card:hover{
    transform: translateY(-2px);
    box-shadow: 0 18px 40px rgba(2,6,23,.12);
    color: inherit;
  }

  .quick-card.flex-column{
    min-height: auto;
  }

  .quick-ico{
    width: 48px;
    height: 48px;
    border-radius: 14px;
    display: grid;
    place-items: center;
    background: rgba(13,110,253,.12);
    flex-shrink: 0;
  }

  .quick-ico i{
    font-size: 1.2rem;
    color: #0d6efd;
  }

  .quick-meta{
    min-width: 0;
  }

  .quick-meta .t{
    font-weight: 900;
    margin: 0 0 2px 0;
    color: var(--spgi-ink);
    line-height: 1.2;
  }

  .quick-meta .d{
    margin: 0;
    font-size: .85rem;
    color: var(--spgi-muted);
    line-height: 1.35;
    word-break: break-word;
  }

  .submenu{
    margin-top: 12px;
    padding-left: 62px;
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    width: 100%;
  }

  .submenu a{
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 12px;
    border-radius: 999px;
    font-size: .85rem;
    font-weight: 800;
    text-decoration: none;
    background: rgba(13,110,253,.08);
    border: 1px solid rgba(13,110,253,.18);
    color: #0d6efd;
    transition: .2s ease;
  }

  .submenu a i{
    font-size: .95rem;
  }

  .submenu a:hover{
    background: rgba(13,110,253,.16);
    box-shadow: 0 10px 22px rgba(13,110,253,.15);
    transform: translateY(-1px);
    color: #0d6efd;
  }

  .quick-card.disabled{
    pointer-events: none;
    opacity: .55;
    filter: grayscale(.15);
  }

  .welcome-logout{
    min-height: 46px;
    border-radius: 12px;
    font-weight: 600;
  }

  @media (max-width: 767.98px){
    .welcome-bg{
      min-height: auto;
      padding: 6px 0 20px 0;
    }

    .welcome-card{
      padding: 20px 14px;
      border-radius: 18px;
    }

    .welcome-title{
      font-size: 1.55rem;
      margin-top: 14px;
    }

    .welcome-subtitle{
      font-size: .95rem;
    }

    .quick-title{
      font-size: 1rem;
    }

    .quick-grid{
      grid-template-columns: 1fr;
      gap: 12px;
    }

    .quick-card{
      padding: 14px;
      gap: 12px;
      min-height: 82px;
      align-items: flex-start;
    }

    .quick-ico{
      width: 44px;
      height: 44px;
      border-radius: 12px;
    }

    .quick-ico i{
      font-size: 1.1rem;
    }

    .submenu{
      padding-left: 0;
      margin-top: 14px;
    }

    .submenu a{
      width: 100%;
      justify-content: center;
      padding: 10px 12px;
      border-radius: 12px;
    }
  }

  @media (max-width: 420px){
    .welcome-title{
      font-size: 1.35rem;
    }

    .badge-chip{
      width: 100%;
      justify-content: center;
    }
  }
</style>

@php
  $rolesRouteExists = \Illuminate\Support\Facades\Route::has('mantenimiento.roles.index');

  $proyectosRoute =
    \Illuminate\Support\Facades\Route::has('proyectos.index') ? 'proyectos.index' :
    (\Illuminate\Support\Facades\Route::has('proyecto.index') ? 'proyecto.index' :
    (\Illuminate\Support\Facades\Route::has('proyectos') ? 'proyectos' : null));
@endphp

<div class="welcome-bg">
  <div class="welcome-card text-center">

    <div class="badge-chip">
      <i class="bi bi-shield-check"></i>
      Acceso concedido
    </div>

    <h1 class="welcome-title">¡Bienvenido, {{ auth()->user()->name }}!</h1>

    <p class="welcome-subtitle">
      Has iniciado sesión correctamente en el sistema <b>SPGI</b>.
    </p>

    <div class="divider"></div>

    <div class="quick-title">Accesos rápidos</div>

    <div class="quick-grid">

      <a class="quick-card" href="{{ route('usuarios.index') }}">
        <div class="quick-ico"><i class="bi bi-people"></i></div>
        <div class="quick-meta text-start">
          <p class="t">Usuarios</p>
          <p class="d">Gestionar usuarios y roles</p>
        </div>
      </a>

      <a class="quick-card" href="{{ route('clientes.index') }}">
        <div class="quick-ico"><i class="bi bi-building"></i></div>
        <div class="quick-meta text-start">
          <p class="t">Clientes</p>
          <p class="d">Listado y contactos</p>
        </div>
      </a>

      <a class="quick-card" href="{{ route('requerimientos.index') }}">
        <div class="quick-ico"><i class="bi bi-clipboard-check"></i></div>
        <div class="quick-meta text-start">
          <p class="t">Requerimientos</p>
          <p class="d">Seguimiento de solicitudes</p>
        </div>
      </a>

      <a class="quick-card {{ $proyectosRoute ? '' : 'disabled' }}"
         href="{{ $proyectosRoute ? route($proyectosRoute) : '#' }}"
         title="{{ $proyectosRoute ? '' : 'No existe la ruta de Proyectos (proyectos.index)' }}">
        <div class="quick-ico"><i class="bi bi-kanban"></i></div>
        <div class="quick-meta text-start">
          <p class="t">Proyectos</p>
          <p class="d">Gestión y seguimiento</p>
        </div>
      </a>

      <a class="quick-card" href="{{ route('wiki.index') }}">
        <div class="quick-ico"><i class="bi bi-journal-bookmark"></i></div>
        <div class="quick-meta text-start">
          <p class="t">Wiki</p>
          <p class="d">Documentos y Etiquetas</p>
        </div>
      </a>

      <div class="quick-card flex-column align-items-start">
        <div class="d-flex align-items-center gap-2 w-100">
          <div class="quick-ico"><i class="bi bi-tools"></i></div>
          <div class="quick-meta text-start">
            <p class="t">Mantenimiento</p>
            <p class="d">Configuraciones del sistema</p>
          </div>
        </div>

        <div class="submenu">
          @if($rolesRouteExists)
            <a href="{{ route('mantenimiento.roles.index') }}">
              <i class="bi bi-person-badge"></i> Roles
            </a>
          @endif

          <a href="{{ route('mantenimiento.tipo-soporte.index') }}">
            <i class="bi bi-headset"></i> Tipo de soporte
          </a>

          <a href="{{ route('mantenimiento.iguala.index') }}">
            <i class="bi bi-award"></i> Iguala
          </a>

          <a href="{{ route('mantenimiento.categorias.index') }}">
            <i class="bi bi-tags"></i> Categorías
          </a>

          <a href="{{ route('mantenimiento.estados-requerimiento.index') }}">
            <i class="bi bi-flag"></i> Estados de Req.
          </a>
        </div>
      </div>

    </div>

    <div class="divider"></div>

    <a href="{{ route('logout') }}" class="btn btn-outline-danger w-100 mt-2 welcome-logout">
      <i class="bi bi-box-arrow-right me-2"></i>
      Cerrar Sesión
    </a>

  </div>
</div>

@endsection