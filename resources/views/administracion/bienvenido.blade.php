@extends('layouts.app')

@section('page_title', 'Administración')

@section('content')
<style>
  :root {
    --admin-primary: 99, 102, 241;
    --admin-primary-hex: #6366f1;
  }

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
    background: rgba(var(--admin-primary), 0.1); font-weight: 800; border: 1px solid rgba(var(--admin-primary), 0.2);
    font-size: .85rem; color: var(--admin-primary-hex); text-transform: uppercase; letter-spacing: 1px;
  }

  .divider{
    height: 1px; background: var(--border-main); margin: 32px 0;
  }

  .quick-grid{ display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; }

  .quick-card{
    text-decoration: none; color: inherit; border-radius: 20px;
    border: 1px solid var(--border-main); background: var(--bg-surface);
    box-shadow: var(--shadow-main); padding: 20px; display: flex; align-items: center;
    gap: 16px; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); min-height: 90px;
  }

  .quick-card:hover{
    transform: translateY(-4px); box-shadow: 0 15px 35px rgba(var(--admin-primary), 0.15);
    border-color: var(--admin-primary-hex); background: var(--bg-surface-glass); color: inherit;
  }

  .quick-ico{
    width: 52px; height: 52px; border-radius: 14px; display: grid; place-items: center;
    background: rgba(var(--admin-primary), 0.1); color: var(--admin-primary-hex); flex-shrink: 0; font-size: 1.4rem;
  }

  .quick-meta .t{ font-weight: 800; margin: 0 0 2px 0; color: var(--text-main); font-size: 1.05rem; }
  .quick-meta .d{ margin: 0; font-size: .85rem; color: var(--text-muted); line-height: 1.4; }

  @media (max-width: 767.98px){
    .welcome-card{ padding: 30px 20px; }
    .quick-grid{ grid-template-columns: 1fr; }
  }
</style>

<div class="welcome-bg">
  <div class="welcome-card text-center">

    <div class="badge-chip">
      <i class="bi bi-person-badge-fill"></i>
      Área de Administración
    </div>

    <div class="divider"></div>

    <div class="quick-grid">

      <a class="quick-card glass-card-premium" href="{{ route('requerimientos.facturacion') }}">
        <div class="quick-ico icon-float"><i class="bi bi-receipt-cutoff"></i></div>
        <div class="quick-meta text-start">
          <p class="t">Facturación</p>
          <p class="d">Gestión de cobros y requerimientos industriales</p>
        </div>
      </a>

      <a class="quick-card glass-card-premium" href="{{ route('proveedores.index') }}">
        <div class="quick-ico icon-float"><i class="bi bi-truck"></i></div>
        <div class="quick-meta text-start">
          <p class="t">Proveedores</p>
          <p class="d">Gestión de proveedores de servicios</p>
        </div>
      </a>

      <a class="quick-card glass-card-premium" href="{{ route('tarifarios.index') }}">
        <div class="quick-ico icon-float"><i class="bi bi-currency-dollar"></i></div>
        <div class="quick-meta text-start">
          <p class="t">Tarifario</p>
          <p class="d">Gestión de tarifas por tipo de soporte</p>
        </div>
      </a>

      <a class="quick-card glass-card-premium" href="{{ route('rendiciones.index') }}">
        <div class="quick-ico icon-float"><i class="bi bi-receipt"></i></div>
        <div class="quick-meta text-start">
          <p class="t">Rendición de Gastos</p>
          <p class="d">Registro y visualización en formato PDF</p>
        </div>
      </a>

      <a class="quick-card glass-card-premium" href="{{ route('horas-extras.index') }}">
        <div class="quick-ico icon-float"><i class="bi bi-clock-history"></i></div>
        <div class="quick-meta text-start">
          <p class="t">Horas Extras</p>
          <p class="d">Planillas de registro y exportación a PDF</p>
        </div>
      </a>

      <a class="quick-card glass-card-premium" href="{{ route('estado-cuentas.index') }}">
        <div class="quick-ico icon-float"><i class="bi bi-wallet2"></i></div>
        <div class="quick-meta text-start">
          <p class="t">Estado de Cuenta</p>
          <p class="d">Conciliación de pagos, facturas y saldos de clientes</p>
        </div>
      </a>

    </div>

    <div class="divider"></div>

    <div class="d-flex flex-column gap-2">
        <a href="{{ route('seleccion') }}" class="btn btn-outline-primary w-100 rounded-pill py-2 fw-bold" style="border-color: var(--admin-primary-hex); color: var(--admin-primary-hex);">
          <i class="bi bi-grid me-2"></i>
          Volver a Selección de Área
        </a>
    </div>

  </div>
</div>
@endsection
