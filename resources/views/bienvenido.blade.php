@extends('layouts.app')

@section('page_title', 'Bienvenidos')

@section('content')
<style>
  .welcome-bg{ min-height: calc(100vh - 110px); display: grid; place-items: center; padding: 24px 0; }

  .welcome-card{
    width: 100%; max-width: 900px; border-radius: 24px;
    background: var(--bg-surface-glass); border: 1px solid var(--border-main);
    box-shadow: var(--shadow-main); backdrop-filter: blur(20px); padding: 40px;
  }

  .badge-chip{
    display:inline-flex; align-items:center; gap:8px; padding: 10px 18px; border-radius: 999px;
    background: rgba(var(--spgi-primary), 0.1); font-weight: 800; border: 1px solid var(--border-main);
    font-size: .85rem; color: var(--spgi-primary); text-transform: uppercase; letter-spacing: 1px;
  }

  .welcome-title{
    font-weight: 900; font-size: 2.2rem; line-height: 1.1; margin-top: 24px;
    margin-bottom: 12px; color: var(--text-main); letter-spacing: -1px;
  }
  .welcome-subtitle{ color: var(--text-muted); margin: 0; font-size: 1.1rem; }

  .divider{
    height: 1px; background: var(--border-main); margin: 32px 0;
  }

  .quick-title{
    font-weight: 800; margin-bottom: 20px; text-align: left; color: var(--text-main);
    font-size: 1.1rem; text-transform: uppercase; letter-spacing: 1px;
  }

  .quick-grid{ display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; }

  .quick-card{
    text-decoration: none; color: inherit; border-radius: 20px;
    border: 1px solid var(--border-main); background: var(--bg-surface);
    box-shadow: var(--shadow-main); padding: 20px; display: flex; align-items: center;
    gap: 16px; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); min-height: 90px;
  }

  .quick-card:hover{
    transform: translateY(-4px); box-shadow: 0 15px 35px var(--spgi-primary-glow);
    border-color: var(--spgi-primary); background: var(--bg-surface-glass); color: inherit;
  }

  .quick-ico{
    width: 52px; height: 52px; border-radius: 14px; display: grid; place-items: center;
    background: rgba(var(--spgi-primary), 0.1); color: var(--spgi-primary); flex-shrink: 0; font-size: 1.4rem;
  }

  .quick-meta .t{ font-weight: 800; margin: 0 0 2px 0; color: var(--text-main); font-size: 1.05rem; }
  .quick-meta .d{ margin: 0; font-size: .85rem; color: var(--text-muted); line-height: 1.4; }

  .submenu{ margin-top: 16px; display: flex; flex-wrap: wrap; gap: 8px; width: 100%; border-top: 1px solid var(--border-main); padding-top: 16px; }
  .submenu a{
    display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; border-radius: 999px;
    font-size: .8rem; font-weight: 800; text-decoration: none;
    background: var(--bg-surface); border: 1px solid var(--border-main); color: var(--text-main);
    transition: all 0.2s ease;
  }
  .submenu a:hover{ background: var(--spgi-primary); color: #fff; border-color: var(--spgi-primary); }

  .welcome-logout{
    min-height: 50px; border-radius: 14px; font-weight: 700; border: 1px solid var(--border-main);
  }

  @media (max-width: 767.98px){
    .welcome-card{ padding: 30px 20px; }
    .quick-grid{ grid-template-columns: 1fr; }
    .welcome-title{ font-size: 1.8rem; }
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