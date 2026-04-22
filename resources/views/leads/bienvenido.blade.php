@extends('layouts.app')

@section('page_title', 'Bienvenido a Comerciales')

@section('content')
<style>
  .welcome-bg{ 
    min-height: calc(100vh - 110px); display: grid; place-items: center; padding: 24px 0; 
  }

  .welcome-card{
    width: 100%; max-width: 900px; border-radius: 24px;
    background: var(--bg-surface-glass); border: 1px solid var(--border-main);
    box-shadow: var(--shadow-main); backdrop-filter: blur(20px); padding: 40px;
  }

  .badge-chip{
    display:inline-flex; align-items:center; gap:8px; padding: 10px 18px; border-radius: 999px;
    background: rgba(16, 185, 129, 0.1); font-weight: 800; border: 1px solid rgba(16, 185, 129, 0.2);
    font-size: .85rem; color: #10b981; text-transform: uppercase; letter-spacing: 1px;
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
    transform: translateY(-4px); box-shadow: 0 15px 35px rgba(16, 185, 129, 0.15);
    border-color: #10b981; background: var(--bg-surface-glass); color: inherit;
  }

  .quick-ico{
    width: 52px; height: 52px; border-radius: 14px; display: grid; place-items: center;
    background: rgba(16, 185, 129, 0.1); color: #10b981; flex-shrink: 0; font-size: 1.4rem;
  }

  .quick-meta .t{ font-weight: 800; margin: 0 0 2px 0; color: var(--text-main); font-size: 1.05rem; }
  .quick-meta .d{ margin: 0; font-size: .85rem; color: var(--text-muted); line-height: 1.4; }

  @media (max-width: 767.98px){
    .welcome-card{ padding: 30px 20px; }
    .quick-grid{ grid-template-columns: 1fr; }
    .welcome-title{ font-size: 1.8rem; }
  }
</style>

<div class="welcome-bg">
  <div class="welcome-card text-center">

    <div class="badge-chip">
      <i class="bi bi-briefcase"></i>
      Área Comercial
    </div>

    <h1 class="welcome-title text-gradient">Bienvenido a Comerciales</h1>

    <p class="welcome-subtitle">
      Gestión de Leads y Ventas para el sistema <b>SPGI</b>.
    </p>

    <div class="divider"></div>

    <div class="quick-title">Herramientas Comerciales</div>

    <div class="quick-grid">

      <a class="quick-card glass-card-premium" href="{{ route('leads.index') }}">
        <div class="quick-ico icon-float"><i class="bi bi-person-lines-fill"></i></div>
        <div class="quick-meta text-start">
          <p class="t">Leads de Clientes</p>
          <p class="d">Ver todos los prospectos y cotizaciones</p>
        </div>
      </a>

      <a class="quick-card glass-card-premium" href="{{ route('leads.reportes') }}">
        <div class="quick-ico icon-float"><i class="bi bi-graph-up"></i></div>
        <div class="quick-meta text-start">
          <p class="t">Reportes de Ventas</p>
          <p class="d">Analítica, KPIs y cierre de negocios</p>
        </div>
      </a>

      <a class="quick-card glass-card-premium" href="{{ route('lead-requirements.index') }}">
        <div class="quick-ico icon-float"><i class="bi bi-journal-text"></i></div>
        <div class="quick-meta text-start">
          <p class="t">Requerimientos Comerciales</p>
          <p class="d">Tareas y solicitudes de preventa</p>
        </div>
      </a>

    </div>

    <div class="divider"></div>

    <a href="{{ route('bienvenido') }}" class="btn btn-outline-secondary w-100 rounded-pill py-2 fw-bold">
      <i class="bi bi-arrow-left me-2"></i>
      Volver al Menu Principal
    </a>

  </div>
</div>
@endsection
