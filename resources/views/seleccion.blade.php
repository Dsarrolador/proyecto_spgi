@extends('layouts.app')

@section('page_title', 'Selección de Área')

@section('content')
<style>
    .selection-container {
        min-height: calc(100vh - 110px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px 20px;
        background: radial-gradient(circle at top right, rgba(var(--spgi-primary), 0.05), transparent);
    }

    .selection-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 30px;
        width: 100%;
        max-width: 1100px;
    }

    .selection-card {
        background: var(--bg-surface-glass);
        border: 1px solid var(--border-main);
        border-radius: 30px;
        padding: 50px 30px;
        text-align: center;
        text-decoration: none;
        color: inherit;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 20px;
        backdrop-filter: blur(20px);
        box-shadow: var(--shadow-main);
        position: relative;
        overflow: hidden;
    }

    .selection-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, rgba(var(--spgi-primary), 0.1), transparent);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .selection-card:hover {
        transform: translateY(-10px) scale(1.02);
        border-color: var(--spgi-primary);
        box-shadow: 0 20px 40px rgba(var(--spgi-primary), 0.15);
    }

    .selection-card:hover::before {
        opacity: 1;
    }

    .selection-icon {
        width: 100px;
        height: 100px;
        border-radius: 25px;
        display: grid;
        place-items: center;
        font-size: 3rem;
        background: rgba(var(--spgi-primary), 0.1);
        color: var(--spgi-primary);
        transition: all 0.3s ease;
        z-index: 1;
    }

    .selection-card:hover .selection-icon {
        background: var(--spgi-primary);
        color: #fff;
        transform: rotate(-5deg);
    }

    .selection-title {
        font-size: 1.8rem;
        font-weight: 800;
        margin: 0;
        color: var(--text-main);
        z-index: 1;
    }

    .selection-desc {
        color: var(--text-muted);
        font-size: 1rem;
        margin: 0;
        line-height: 1.5;
        z-index: 1;
    }

    .selection-badge {
        position: absolute;
        top: 20px;
        right: 20px;
        background: rgba(var(--spgi-primary), 0.1);
        color: var(--spgi-primary);
        padding: 5px 15px;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    @media (max-width: 767.98px) {
        .selection-grid {
            grid-template-columns: 1fr;
        }

        .selection-card {
            padding: 40px 20px;
        }
    }
</style>

<div class="selection-container">
    <div class="text-center w-100" style="max-width: 900px; position: relative; z-index: 2;">
        
        <!-- Welcome Header -->
        <div class="mb-5 animate__animated animate__fadeInDown">
            <div class="badge-chip mb-3" style="background: rgba(var(--spgi-primary), 0.1); color: var(--spgi-primary); border: 1px solid var(--border-main); padding: 8px 16px; border-radius: 999px; display: inline-flex; align-items: center; gap: 8px; font-weight: 800; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px;">
                <i class="bi bi-shield-lock-fill"></i> Sesión Autorizada
            </div>
            <h1 class="selection-title" style="font-size: 3.5rem; font-weight: 900; letter-spacing: -2px; line-height: 1; margin-bottom: 15px;">
                ¡Hola, <span class="text-gradient">{{ auth()->user()->name }}</span>!
            </h1>
            <p class="selection-desc" style="font-size: 1.1rem; opacity: 0.8; max-width: 500px; margin: 0 auto;">
                Has ingresado al ecosistema <strong>SPGI</strong>. ¿En qué área deseas trabajar hoy?
            </p>
        </div>

        <!-- Selection Grid -->
        <div class="selection-grid mb-5 animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
            <a href="{{ route('leads.bienvenido') }}" class="selection-card glass-card-premium">
                <span class="selection-badge">Ventas</span>
                <div class="selection-icon icon-float">
                    <i class="bi bi-briefcase-fill"></i>
                </div>
                <h2 class="selection-title">Comerciales</h2>
                <p class="selection-desc">Gestión de Leads, cotizaciones y seguimiento de clientes potenciales.</p>
            </a>

            <a href="{{ route('bienvenido') }}" class="selection-card glass-card-premium">
                <span class="selection-badge">Operaciones</span>
                <div class="selection-icon icon-float">
                    <i class="bi bi-tools"></i>
                </div>
                <h2 class="selection-title">Requerimientos</h2>
                <p class="selection-desc">Sistema de solicitudes, soporte técnico y mantenimiento de cuentas.</p>
            </a>

            <a href="{{ route('administracion.bienvenido') }}" class="selection-card glass-card-premium" style="--spgi-primary: 99, 102, 241; --spgi-primary-glow: rgba(99, 102, 241, 0.5);">
                <span class="selection-badge" style="background: rgba(99, 102, 241, 0.1); color: rgb(99, 102, 241);">Control</span>
                <div class="selection-icon icon-float" style="background: rgba(99, 102, 241, 0.1); color: rgb(99, 102, 241);">
                    <i class="bi bi-person-badge-fill"></i>
                </div>
                <h2 class="selection-title">Administración</h2>
                <p class="selection-desc">Gestión financiera, facturación y control administrativo del ecosistema.</p>
            </a>
        </div>

        <!-- Logout Action -->
        <div class="animate__animated animate__fadeIn" style="animation-delay: 0.4s;">
            <a href="{{ route('logout') }}" class="btn btn-outline-danger px-5 rounded-pill py-3 fw-bold shadow-sm d-inline-flex align-items-center gap-2 hover-scale" style="border-width: 2px; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);">
                <i class="bi bi-box-arrow-right fs-5"></i>
                Finalizar Sesión
            </a>
        </div>

    </div>
</div>
@endsection
