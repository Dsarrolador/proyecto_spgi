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
        grid-template-columns: repeat(2, 1fr);
        gap: 30px;
        width: 100%;
        max-width: 900px;
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
    <div class="selection-grid">
        
        <a href="{{ route('leads.index') }}" class="selection-card">
            <span class="selection-badge">Ventas</span>
            <div class="selection-icon">
                <i class="bi bi-briefcase"></i>
            </div>
            <h2 class="selection-title">Comerciales</h2>
            <p class="selection-desc">Gestión de Leads, cotizaciones y seguimiento de clientes potenciales.</p>
        </a>

        <a href="{{ route('bienvenido') }}" class="selection-card">
            <span class="selection-badge">Operaciones</span>
            <div class="selection-icon">
                <i class="bi bi-tools"></i>
            </div>
            <h2 class="selection-title">Requerimientos</h2>
            <p class="selection-desc">Sistema de solicitudes, soporte técnico y mantenimiento de cuentas.</p>
        </a>

    </div>
</div>
@endsection
