<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Proyecto SPGI</title>

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

<style id="theme-reset">
  :root { --topbar-bg: #ffffff; }
  [data-bs-theme="dark"] { --topbar-bg: #0f172a; }
</style>

<script>
  (function() {
    const savedTheme = localStorage.getItem('spgi-theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
    document.documentElement.setAttribute('data-bs-theme', savedTheme);
  })();
</script>

<style>
  @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

  :root {
    /* Global Brand */
    --spgi-primary: #3b82f6;
    --spgi-primary-glow: rgba(59, 130, 246, 0.4);
    --spgi-sidebar-width: 260px;
    --spgi-radius: 18px;
    --spgi-font: 'Inter', system-ui, -apple-system, sans-serif;
    
    /* Light Theme - Clean and Professional */
    --bg-master: #f1f5f9;
    --bg-surface: #ffffff;
    --bg-surface-glass: rgba(255, 255, 255, 0.9);
    --border-main: rgba(0, 0, 0, 0.08);
    --text-main: #0f172a;
    --text-muted: #64748b;
    --topbar-bg: rgba(255, 255, 255, 0.85);
    --sidebar-bg: #0f172a;
    --sidebar-text: rgba(255, 255, 255, 0.75);
    --shadow-main: 0 20px 50px rgba(0, 0, 0, 0.05);
  }

  [data-bs-theme="dark"] {
    /* Dark Theme - Deep & Modern */
    --bg-master: #050a17;
    --bg-surface: #0f172a;
    --bg-surface-glass: rgba(15, 23, 42, 0.85);
    --border-main: rgba(255, 255, 255, 0.08);
    --text-main: #f1f5f9;
    --text-muted: #94a3b8;
    --topbar-bg: rgba(15, 23, 42, 0.85);
    --sidebar-bg: #030712;
    --sidebar-text: rgba(255, 255, 255, 0.8);
    --shadow-main: 0 20px 50px rgba(0, 0, 0, 0.4);
  }

  body {
    background-color: var(--bg-master);
    color: var(--text-main);
    font-family: var(--spgi-font);
    transition: background-color 0.4s cubic-bezier(0.4, 0, 0.2, 1), color 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    -webkit-font-smoothing: antialiased;
    position: relative;
    overflow-x: hidden;
  }

  /* GLOBAL DYNAMIC BACKGROUND */
  .spgi-bg-glow {
    position: fixed; top: 0; left: 0; width: 100%; height: 100%;
    z-index: -1; pointer-events: none; overflow: hidden; background: var(--bg-master);
  }

  .glow-blob {
    position: absolute; width: 600px; height: 600px;
    background: radial-gradient(circle, var(--spgi-primary-glow) 0%, transparent 70%);
    filter: blur(100px); opacity: 0.25;
    animation: float-complex 20s infinite ease-in-out alternate;
  }

  .blob-1 { top: -10%; left: -10%; animation-duration: 25s; }
  .blob-2 { bottom: -10%; right: -10%; animation-duration: 30s; animation-delay: -5s; }
  .blob-3 { top: 40%; left: 60%; width: 400px; height: 400px; animation-duration: 22s; animation-delay: -10s; }
  .blob-4 { bottom: 30%; right: 50%; width: 500px; height: 500px; animation-duration: 28s; animation-delay: -15s; }

  @keyframes float-complex {
    0% { transform: translate(0, 0) rotate(0deg) scale(1); }
    33% { transform: translate(100px, 150px) rotate(120deg) scale(1.2); }
    66% { transform: translate(-100px, 50px) rotate(240deg) scale(0.8); }
    100% { transform: translate(50px, -100px) rotate(360deg) scale(1.1); }
  }

  /* PREMIUM UI UTILITIES */
  .text-gradient {
    background: linear-gradient(135deg, var(--spgi-primary), #60a5fa, #2dd4bf);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-size: 200% auto;
    animation: shine 5s linear infinite;
    display: inline-block;
  }

  @keyframes shine {
    to { background-position: 200% center; }
  }

  .glass-card-premium {
    background: var(--bg-surface-glass);
    border: 1px solid var(--border-main);
    backdrop-filter: blur(20px);
    border-radius: 24px;
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    position: relative;
    overflow: hidden;
  }

  /* Interactive Border Glow (Tracing) */
  .glass-card-premium::before {
    content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 100%;
    background: radial-gradient(circle at var(--mouse-x, 50%) var(--mouse-y, 50%), 
                var(--spgi-primary-glow) 0%, transparent 60%);
    opacity: 0; transition: opacity 0.3s; pointer-events: none;
  }
  .glass-card-premium:hover::before { opacity: 1; }
  .glass-card-premium:hover { transform: translateY(-10px) scale(1.02); border-color: var(--spgi-primary); }

  .hover-scale { transition: transform 0.3s ease; }
  .hover-scale:hover { transform: scale(1.05); }

  .icon-float { animation: iconFloat 3s infinite ease-in-out; }
  @keyframes iconFloat {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-8px); }
  }

  /* SIDEBAR STRUCTURE */
  .spgi-sidebar {
    width: var(--spgi-sidebar-width);
    height: 100vh;
    position: fixed;
    top: 0;
    right: -260px; /* Oculto por defecto */
    background: var(--spgi-sidebar-bg);
    color: #fff;
    z-index: 1050; /* Mayor que el overlay */
    transition: all 0.3s ease;
    overflow-y: auto;
    border-left: 1px solid rgba(255,255,255,0.05);
  }

  .spgi-sidebar.show {
    right: 0 !important;
  }

  .spgi-content {
    margin-right: 0;
    transition: all 0.3s ease;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
  }

  /* Desktop behavior based on body class */
  body.sidebar-open-desktop .spgi-sidebar {
    right: 0;
  }
  body.sidebar-open-desktop .spgi-content {
    margin-right: var(--spgi-sidebar-width);
  }

  /* OVERLAY */
  .sidebar-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    z-index: 1045; /* Justo debajo del sidebar */
    display: none;
    backdrop-filter: blur(2px);
  }

  .sidebar-overlay.show {
    display: block;
  }

  /* TOP BAR */
  .spgi-topbar {
    height: 70px;
    background: var(--topbar-bg);
    border-bottom: 1px solid var(--border-main);
    backdrop-filter: blur(16px);
    transition: all 0.4s ease;
    display: flex;
    align-items: center;
    padding: 0 2rem;
    position: sticky;
    top: 0;
    z-index: 1000;
  }

  /* NAV LINKS */
  .nav-sidebar {
    padding: 1.5rem 1rem;
  }

  .nav-sidebar .nav-link {
    color: rgba(255,255,255,0.7);
    padding: 10px 15px;
    border-radius: 12px;
    margin-bottom: 4px;
    display: flex;
    align-items: center;
    transition: all 0.2s;
    font-size: 0.95rem;
  }

  .nav-sidebar .nav-link i {
    font-size: 1.2rem;
    margin-right: 12px;
  }

  .nav-sidebar .nav-link:hover {
    background: rgba(255,255,255,0.05);
    color: #fff;
  }

  .nav-sidebar .nav-link.active {
    background: var(--spgi-primary);
    color: #fff;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
  }

  .nav-section-title {
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: rgba(255,255,255,0.4);
    margin: 1.5rem 0 0.5rem 1rem;
    font-weight: 700;
  }

  @media (max-width: 991.98px) {
    .spgi-sidebar {
      left: calc(-1 * var(--spgi-sidebar-width));
    }
    .spgi-sidebar.show {
      left: 0;
    }
    .spgi-content {
      margin-left: 0;
    }
    .spgi-topbar {
      padding: 0 1rem;
    }
  }

  .spgi-card, .card {
    background: var(--bg-surface-glass);
    border: 1px solid var(--border-main);
    border-radius: var(--spgi-radius);
    box-shadow: var(--shadow-main);
    backdrop-filter: blur(12px);
    transition: transform 0.3s ease, box-shadow 0.3s ease, background-color 0.4s ease;
  }

  .table {
    --bs-table-bg: transparent;
    --bs-table-color: var(--text-main);
    --bs-table-border-color: var(--border-main);
  }

  /* Specific fix for table headers */
  .table thead th {
    background-color: #0b1220 !important; /* Keep consistent dark header */
    color: #ffffff !important;
    border-color: rgba(255,255,255,0.1) !important;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.5px;
  }

  [data-bs-theme="dark"] .table tbody tr:hover {
    background-color: rgba(255, 255, 255, 0.03) !important;
  }

  .form-control, .form-select {
    background-color: var(--bg-surface);
    border: 1px solid var(--border-main);
    color: var(--text-main);
    border-radius: 12px;
    padding: 0.6rem 1rem;
    transition: all 0.2s ease;
  }

  .form-control:focus, .form-select:focus {
    background-color: var(--bg-surface);
    color: var(--text-main);
    border-color: var(--spgi-primary);
    box-shadow: 0 0 0 4px var(--spgi-primary-glow);
  }

  .theme-switch-wrap {
    background: rgba(0,0,0,0.05);
    border: 1px solid var(--border-main) !important;
  }

  .theme-btn {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text-muted);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  }

  .theme-btn:hover {
    color: var(--text-main);
    background: rgba(0,0,0,0.05);
  }

  .theme-btn.active {
    background: var(--bg-surface) !important;
    color: var(--spgi-primary) !important;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  }

  [data-bs-theme="dark"] .theme-switch-wrap {
    background: rgba(255,255,255,0.05);
  }
</style>
</head>

<body class="{{ request()->is('/') || request()->is('login') ? 'auth-page' : '' }}">
  <div class="spgi-bg-glow">
    <div class="glow-blob blob-1"></div>
    <div class="glow-blob blob-2"></div>
    <div class="glow-blob blob-3"></div>
    <div class="glow-blob blob-4"></div>
  </div>

@php

$rolesRouteExists=\Illuminate\Support\Facades\Route::has('mantenimiento.roles.index');

$mantenimientoActive=
($rolesRouteExists && request()->routeIs('mantenimiento.roles.*')) ||
request()->routeIs('mantenimiento.roles-usuario.*') ||
request()->routeIs('mantenimiento.tipo-soporte.*') ||
request()->routeIs('mantenimiento.iguala.*') ||
request()->routeIs('mantenimiento.tipos-equipo.*') ||
request()->routeIs('mantenimiento.categorias.*');

@endphp

  <!-- SIDEBAR -->
  <aside class="spgi-sidebar" id="sidebar">
    <div class="p-4 d-flex align-items-center justify-content-between">
      <h5 class="mb-0 fw-bold text-white">PROYECTO SPGI</h5>
      <button class="btn btn-link text-white d-lg-none p-0" onclick="toggleSidebar()">
        <i class="bi bi-x-lg"></i>
      </button>
    </div>

    <nav class="nav-sidebar">
      @if(request()->is('comerciales/*') || request()->is('lead-requirements*') || request()->routeIs('leads.*') || request()->routeIs('lead-requirements.*'))
        <!-- SIDEBAR COMERCIAL -->
        <div class="nav-section-title">Comerciales</div>
        <a class="nav-link {{ request()->routeIs('seleccion') ? 'active' : '' }}" href="{{ route('seleccion') }}">
          <i class="bi bi-grid-fill"></i> Inicio
        </a>
        <a class="nav-link {{ request()->routeIs('leads.bienvenido') ? 'active' : '' }}" href="{{ route('leads.bienvenido') }}">
          <i class="bi bi-house-door"></i> Dashboard Ventas
        </a>
        <a class="nav-link {{ request()->routeIs('leads.index') || request()->routeIs('leads.create') || request()->routeIs('leads.edit') || request()->routeIs('leads.show') ? 'active' : '' }}" href="{{ route('leads.index') }}">
          <i class="bi bi-people"></i> Leads de clientes
        </a>
        <a class="nav-link {{ request()->routeIs('lead-requirements.*') ? 'active' : '' }}" href="{{ route('lead-requirements.index') }}">
          <i class="bi bi-journal-text"></i> Requerimientos Comerciales
        </a>
        <a class="nav-link {{ request()->routeIs('leads.reportes') ? 'active' : '' }}" href="{{ route('leads.reportes') }}">
          <i class="bi bi-bar-chart-line"></i> Reportes de Ventas
        </a>
        <a class="nav-link" href="{{ route('bienvenido') }}" style="background: rgba(var(--spgi-primary), 0.05); margin-top: 10px;">
          <i class="bi bi-clipboard-check"></i> Requerimientos
        </a>
      @elseif(request()->is('administracion/*') || request()->routeIs('administracion.*') || request()->routeIs('requerimientos.facturacion'))
        <!-- SIDEBAR ADMINISTRACIÓN -->
        <div class="nav-section-title">Administración</div>
        <a class="nav-link {{ request()->routeIs('seleccion') ? 'active' : '' }}" href="{{ route('seleccion') }}">
          <i class="bi bi-grid-fill"></i> Inicio
        </a>
        <a class="nav-link {{ request()->routeIs('administracion.bienvenido') ? 'active' : '' }}" href="{{ route('administracion.bienvenido') }}">
          <i class="bi bi-house-door"></i> Dashboard Admin
        </a>
        <a class="nav-link {{ request()->routeIs('requerimientos.facturacion') ? 'active' : '' }}" href="{{ route('requerimientos.facturacion') }}">
          <i class="bi bi-receipt-cutoff"></i> Facturación
        </a>
        <a class="nav-link" href="{{ route('leads.bienvenido') }}" style="background: rgba(16, 185, 129, 0.05); margin-top: 10px; color: #10b981;">
          <i class="bi bi-briefcase"></i> Comerciales
        </a>
      @else
        <!-- SIDEBAR ESTÁNDAR -->
        <div class="nav-section-title">Principal</div>
        <a class="nav-link {{ request()->routeIs('seleccion') ? 'active' : '' }}" href="{{ route('seleccion') }}">
          <i class="bi bi-grid-fill"></i> Inicio
        </a>
        <a class="nav-link {{ request()->routeIs('bienvenido') ? 'active' : '' }}" href="{{ route('bienvenido') }}">
          <i class="bi bi-house-door"></i> Dashboard Operaciones
        </a>
        @if(Route::has('dashboard') && (Auth::user()->es_admin || Auth::user()->es_encargado))
        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
          <i class="bi bi-speedometer2"></i> Dashboard
        </a>
        @endif
        <a class="nav-link" href="{{ route('leads.bienvenido') }}" style="color: #10b981; border: 1px solid rgba(16, 185, 129, 0.2); background: rgba(16, 185, 129, 0.05); margin-top: 10px;">
          <i class="bi bi-briefcase"></i> Comerciales
        </a>

        <div class="nav-section-title">Industriales</div>
        <a class="nav-link {{ request()->routeIs('requerimientos.*') ? 'active' : '' }}" href="{{ route('requerimientos.index') }}">
          <i class="bi bi-clipboard-check"></i> Requerimientos Industriales
        </a>
        <a class="nav-link {{ request()->routeIs('proyectos.*') ? 'active' : '' }}" href="{{ route('proyectos.index') }}">
          <i class="bi bi-kanban"></i> Proyectos
        </a>
        <a class="nav-link {{ request()->routeIs('clientes.*') ? 'active' : '' }}" href="{{ route('clientes.index') }}">
          <i class="bi bi-person-vcard"></i> Clientes
        </a>
        <a class="nav-link {{ request()->routeIs('wiki.*') ? 'active' : '' }}" href="{{ route('wiki.index') }}">
          <i class="bi bi-book"></i> Wiki
        </a>

        @if(Auth::user()->es_admin)
        <div class="nav-section-title">Control de Gestión</div>
        <a class="nav-link {{ request()->routeIs('dashboard.iguala-control') ? 'active' : '' }}" href="{{ route('dashboard.iguala-control') }}">
          <i class="bi bi-shield-check"></i> Control de Igualas
        </a>
        <a class="nav-link {{ request()->routeIs('notificaciones.admin') ? 'active' : '' }}" href="{{ route('notificaciones.admin') }}">
          <i class="bi bi-megaphone"></i> Control de Avisos
        </a>
        @endif

        <div class="nav-section-title">Configuración</div>
        <a class="nav-link {{ request()->routeIs('usuarios.*') ? 'active' : '' }}" href="{{ route('usuarios.index') }}">
          <i class="bi bi-people"></i> Usuarios
        </a>

        <div class="dropdown">
          <a class="nav-link dropdown-toggle {{ $mantenimientoActive ? 'active' : '' }}" href="#" data-bs-toggle="dropdown">
            <i class="bi bi-gear"></i> Mantenimiento
          </a>
          <ul class="dropdown-menu dropdown-menu-dark w-100">
            <li><a class="dropdown-item" href="{{ route('mantenimiento.tipo-soporte.index') }}">Tipo de Soporte</a></li>
            <li><a class="dropdown-item" href="{{ route('mantenimiento.iguala.index') }}">Igualas</a></li>
            <li><a class="dropdown-item" href="{{ route('mantenimiento.tipos-equipo.index') }}">Tipos de Equipo</a></li>
            <li><a class="dropdown-item" href="{{ route('mantenimiento.equipos.index') }}">Equipos (Catálogo)</a></li>
            <li><a class="dropdown-item" href="{{ route('mantenimiento.categorias.index') }}">Categorías</a></li>
            <li><a class="dropdown-item" href="{{ route('mantenimiento.estados-requerimiento.index') }}">Estados de Req.</a></li>
          </ul>
        </div>
      @endif

      <hr class="my-4 border-secondary opacity-25">
      <a class="nav-link text-danger" href="{{ route('logout') }}">
        <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
      </a>
    </nav>
  </aside>

  <!-- CONTENT -->
  <div class="spgi-content">
    <header class="spgi-topbar">
      <div class="d-flex align-items-center justify-content-between w-100">
        <div class="d-flex align-items-center gap-3">
          <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-sm rounded-circle d-none d-md-flex align-items-center justify-content-center" style="width:32px; height:32px;">
            <i class="bi bi-arrow-left"></i>
          </a>
          <h5 class="mb-0 fw-bold">@yield('page_title', 'Requerimientos')</h5>
        </div>
        <div class="d-flex align-items-center gap-3">
          <!-- Notificaciones -->
          <div class="dropdown me-2">
            <button class="btn btn-link p-0 position-relative" type="button" data-bs-toggle="dropdown" style="color: var(--text-main);">
              <i class="bi bi-bell fs-4"></i>
              <span id="notif-count" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none">
                0
              </span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0 p-0" style="width: 380px; background: var(--bg-surface); border: 1px solid var(--border-main) !important;" id="notif-list-container">
              <li class="p-2 border-bottom d-flex justify-content-between align-items-center rounded-top" style="background: rgba(var(--text-main), 0.03);">
                <div class="d-flex align-items-center gap-2">
                    <span class="small fw-bold px-2" style="color: var(--text-main);">Notificaciones</span>
                  <a href="{{ route('notificaciones.index') }}" class="btn btn-link btn-sm p-0 text-muted ms-1" style="text-decoration: none; font-size: 0.75rem;" title="Ver todo el historial">
                    <i class="bi bi-eye"></i> Ver historial
                  </a>
                </div>
                <button onclick="confirmDeleteAll()" class="btn btn-link btn-sm text-danger p-0 px-2" style="text-decoration: none; font-size: 0.75rem;">
                  <i class="bi bi-trash3"></i> Limpiar todo
                </button>
              </li>
              <div id="notif-list" style="max-height: 400px; overflow-y: auto;">
                <li class="p-4 text-center text-muted small">No hay notificaciones</li>
              </div>
            </ul>
          </div>

          <!-- Theme Toggle -->
          <div class="theme-switch-wrap p-1 rounded-pill border d-flex align-items-center me-3" style="background: rgba(var(--text-main), 0.03);">
            <button class="btn btn-sm rounded-circle p-1 theme-btn theme-light-btn active" onclick="setTheme('light')" title="Modo Claro">
               <i class="bi bi-sun"></i>
            </button>
            <button class="btn btn-sm rounded-circle p-1 theme-btn theme-dark-btn" onclick="setTheme('dark')" title="Modo Oscuro">
               <i class="bi bi-moon-stars"></i>
            </button>
          </div>

          <span class="text-muted d-none d-sm-inline">{{ Auth::user()->name }}</span>
          <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width:38px; height:38px;">
            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
          </div>
          <button class="btn btn-outline-secondary btn-sm rounded-3 border-0" onclick="toggleSidebar()">
            <i class="bi bi-layout-sidebar-reverse"></i>
          </button>
        </div>
      </div>
    </header>

    <main class="p-4">
      @yield('content')
    </main>
  </div>

  <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  
  <script>
    document.addEventListener('mousemove', (e) => {
      const cards = document.querySelectorAll('.glass-card-premium');
      cards.forEach(card => {
        const rect = card.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;
        card.style.setProperty('--mouse-x', `${x}px`);
        card.style.setProperty('--mouse-y', `${y}px`);
      });
    });

    function toggleSidebar() {
      const sidebar = document.getElementById('sidebar');
      const overlay = document.getElementById('sidebarOverlay');
      
      if (window.innerWidth >= 992) {
        // Desktop toggle
        const isOpen = document.body.classList.toggle('sidebar-open-desktop');
        overlay.classList.toggle('show'); // Añadimos overlay en desktop para permitir cierre externo
      } else {
        // Mobile toggle
        const isShown = sidebar.classList.toggle('show');
        overlay.classList.toggle('show');
      }
    }

    // Cerrar sidebar al hacer clic fuera (Overlay)
    document.getElementById('sidebarOverlay').addEventListener('click', () => {
      const sidebar = document.getElementById('sidebar');
      const overlay = document.getElementById('sidebarOverlay');

      if (window.innerWidth >= 992) {
        document.body.classList.remove('sidebar-open-desktop');
        overlay.classList.remove('show');
      } else {
        sidebar.classList.remove('show');
        overlay.classList.remove('show');
      }
    });


    // NOTIFICACIONES EN TIEMPO REAL (POLLING)
    function checkNotifications() {
      fetch('{{ route("api.notificaciones.unread") }}')
        .then(res => res.json())
        .then(data => {
          const countBadge = document.getElementById('notif-count');
          const list = document.getElementById('notif-list');
          
          const unreadCount = data.filter(n => n.leido_at === null).length;

          if (unreadCount > 0) {
            countBadge.textContent = unreadCount > 99 ? '99+' : unreadCount;
            countBadge.classList.remove('d-none');
          } else {

            countBadge.classList.add('d-none');
          }

              if (data.length > 0) {
            list.innerHTML = '';
            data.forEach(n => {
              const item = document.createElement('li');
              const isRead = n.leido_at !== null;
              item.className = `border-bottom px-3 py-2 notification-item ${isRead ? 'is-read' : ''}`;
              
              const clickAction = n.url 
                ? `onclick="handleNotifClick('${n.url}', ${n.id}, event)"` 
                : `onclick="toggleNotif(this, event)"`;

              item.innerHTML = `
                <div class="d-flex justify-content-between align-items-start w-100" ${clickAction} style="cursor: pointer;">
                  <div class="pe-2 flex-grow-1">
                    <div class="d-flex align-items-center gap-2 mb-1">
                      <strong class="small" style="font-size: 0.8rem;">${n.titulo ? n.titulo : (n.sender ? n.sender.name : 'Sistema')}</strong>
                      <span class="text-muted" style="font-size: 0.65rem;">${new Date(n.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</span>
                    </div>
                    <p class="mb-0 notif-msg" style="font-size: 0.85rem; line-height: 1.3; color: var(--text-muted);">${n.mensaje}</p>
                  </div>
                  <div class="d-flex align-items-center gap-2 pt-1 border-start ps-2">
                    <button onclick="markAsRead(${n.id}, this, event)" class="btn btn-link p-0 ${isRead ? 'text-muted' : 'text-primary'}" title="Marcar como leída">
                      <i class="bi bi-check-all fs-5"></i>
                    </button>
                    <button onclick="deleteNotification(${n.id}, this, event)" class="btn btn-link p-0 text-danger" title="Eliminar">
                      <i class="bi bi-trash fs-6"></i>
                    </button>
                  </div>
                </div>
              `;
              list.appendChild(item);
            });
          } else {
            const countBadge = document.getElementById('notif-count');
            countBadge.classList.add('d-none');
            list.innerHTML = '<li class="p-4 text-center small" style="color: var(--text-muted);">No hay notificaciones</li>';
          }
        });
    }

    function handleNotifClick(url, id, event) {
        if (event) event.stopPropagation();
        
        // Marcar como leída y luego ir a la URL
        fetch(`{{ url('api/notificaciones') }}/${id}/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        }).then(() => {
            window.location.href = url;
        });
    }

    function toggleNotif(el, event) {
      if (event) event.stopPropagation();
      const msg = el.querySelector('.notif-msg');
      if (msg) msg.classList.toggle('expanded');
    }

    function markAsRead(id, btn, event) {
      if (event) event.stopPropagation();
      // No aplicamos fade-out si el usuario quiere que se quede
      fetch(`{{ url('api/notificaciones') }}/${id}/read`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Content-Type': 'application/json'
        }
      }).then(() => checkNotifications());
    }

    function deleteNotification(id, btn, event) {
      if (event) event.stopPropagation();
      const item = btn ? btn.closest('.notification-item') : null;
      if (item) item.classList.add('fade-out');

      setTimeout(() => {
        fetch(`{{ url('api/notificaciones') }}/${id}`, {
          method: 'DELETE',
          headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
          }
        }).then(() => checkNotifications());
      }, 400);
    }

    function confirmDeleteAll() {
      const modal = new bootstrap.Modal(document.getElementById('modalConfirmDeleteAll'));
      modal.show();
    }

    function deleteAllNotifications() {
      fetch(`{{ route('api.notificaciones.destroyAll') }}`, {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Content-Type': 'application/json'
        }
      }).then(() => {
        bootstrap.Modal.getInstance(document.getElementById('modalConfirmDeleteAll')).hide();
        checkNotifications();
      });
    }

    // Intervalo de polling: 30 segundos
    document.addEventListener('DOMContentLoaded', () => {
      checkNotifications();
      setInterval(checkNotifications, 30000);
    });
  </script>

  @stack('scripts')

  <!-- Modal Confirmación Eliminar Todo -->
  <div class="modal fade" id="modalConfirmDeleteAll" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
        <div class="modal-body p-4 text-center">
          <div class="mb-3">
            <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size: 3rem;"></i>
          </div>
          <h4 class="fw-bold mb-3">¿Eliminar todas las notificaciones?</h4>
          <p class="text-muted mb-4">Esta acción no se puede deshacer y borrará todo tu historial de avisos.</p>
          <div class="d-flex gap-2 justify-content-center">
            <button type="button" class="btn btn-light px-4 rounded-pill" data-bs-dismiss="modal">Cancelar</button>
            <button type="button" onclick="deleteAllNotifications()" class="btn btn-danger px-4 rounded-pill">Sí, eliminar todo</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- GLOBAL FTP UPLOAD LOADER -->
  <style>
    #ftp-upload-loader {
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0, 0, 0, 0.85);
        backdrop-filter: blur(10px);
        z-index: 99999;
        display: none; /* Oculto por defecto */
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: white;
        text-align: center;
    }
    #ftp-upload-loader .loader-content {
        background: rgba(255,255,255,0.05);
        padding: 2.5rem;
        border-radius: 20px;
        border: 1px solid rgba(255,255,255,0.1);
        box-shadow: 0 10px 30px rgba(0,0,0,0.5);
    }
    #ftp-upload-loader .spinner-spgi {
        width: 3.5rem; height: 3.5rem;
        border: 4px solid rgba(255, 255, 255, 0.1);
        border-top-color: #3b82f6;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin: 0 auto 1.5rem auto;
    }
    @keyframes spin { 100% { transform: rotate(360deg); } }
    .loader-title { font-size: 1.25rem; font-weight: bold; margin-bottom: 0.5rem; }
    .loader-msg { color: #cbd5e1; font-size: 0.85rem; max-width: 320px; margin: 0 auto; line-height: 1.5; }
  </style>
  <div id="ftp-upload-loader">
    <div class="loader-content">
      <div class="spinner-spgi"></div>
      <div class="loader-title">🚀 Subiendo archivos de forma segura</div>
      <p class="loader-msg">Este proceso puede tardar unos segundos dependiendo del tamaño de los archivos.</p>
      <div class="progress mt-3" style="height: 6px; background: rgba(255,255,255,0.1); border-radius: 3px;">
        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 100%"></div>
      </div>
    </div>
  </div>

  <script>
    // Interceptar envíos de formularios con archivos
    document.addEventListener('submit', function(e) {
      const form = e.target;
      if (form.getAttribute('enctype') === 'multipart/form-data') {
        // Solo mostrar si hay al menos un input de tipo file con archivos seleccionados
        const fileInputs = form.querySelectorAll('input[type="file"]');
        let hasFiles = false;
        fileInputs.forEach(input => {
          if (input.files && input.files.length > 0) hasFiles = true;
        });

        // Si no hay archivos, dejamos que el formulario se envíe normalmente sin loader
        if (!hasFiles) return;

        // Mostrar el loader
        const loader = document.getElementById('ftp-upload-loader');
        if (loader) {
          loader.style.display = 'flex';
        }
        
        // Deshabilitar botones de envío
        const submitBtns = form.querySelectorAll('button[type="submit"], input[type="submit"]');
        submitBtns.forEach(btn => {
          btn.disabled = true;
          if (btn.tagName === 'BUTTON') {
             const originalHtml = btn.innerHTML;
             btn.setAttribute('data-original-html', originalHtml);
             btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Subiendo...';
          }
        });
      }
    });

    // Manejar errores o cancelaciones (si el navegador se queda en la misma página)
    window.addEventListener('pageshow', function(event) {
        if (event.persisted) {
            const loader = document.getElementById('ftp-upload-loader');
            if (loader) loader.style.display = 'none';
            
            document.querySelectorAll('button[data-original-html]').forEach(btn => {
                btn.disabled = false;
                btn.innerHTML = btn.getAttribute('data-original-html');
            });
        }
    });

    // --- DARK MODE LOGIC ---
    function setTheme(theme) {
        document.documentElement.setAttribute('data-bs-theme', theme);
        localStorage.setItem('spgi-theme', theme);
        
        // Update toggle UI
        const btns = document.querySelectorAll('.theme-btn');
        btns.forEach(btn => btn.classList.remove('active'));
        
        if (theme === 'dark') {
            document.querySelector('.theme-dark-btn').classList.add('active');
        } else {
            document.querySelector('.theme-light-btn').classList.add('active');
        }
    }

    // Aplicar tema inicial lo antes posible
    (function() {
        const savedTheme = localStorage.getItem('spgi-theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
        document.documentElement.setAttribute('data-bs-theme', savedTheme);
        document.addEventListener('DOMContentLoaded', () => {
            setTheme(savedTheme);
        });
    })();
  </script>
</body>
</html>