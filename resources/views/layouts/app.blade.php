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

<style>

  :root {
    --spgi-dark: #0f172a;
    --spgi-dark-2: #1e293b;
    --spgi-sidebar-width: 260px;
    --spgi-primary: #3b82f6;
    --spgi-bg: #f1f5f9;
    --spgi-border: rgba(0,0,0,0.06);
  }

  body {
    background-color: var(--spgi-bg);
    color: #1e293b;
  }

  /* SIDEBAR STRUCTURE */
  .spgi-sidebar {
    width: var(--spgi-sidebar-width);
    height: 100vh;
    position: fixed;
    top: 0;
    right: -260px; /* Oculto por defecto */
    background: var(--spgi-dark);
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
    background: #fff;
    border-bottom: 1px solid var(--spgi-border);
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

  /* Animación de desvanecimiento para notificaciones */
  .fade-out {
    animation: fadeOut 0.4s forwards;
  }

  @keyframes fadeOut {
    from { opacity: 1; transform: translateX(0); }
    to { opacity: 0; transform: translateX(20px); }
  }

  /* Truncado y expansión */
  .notif-msg {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    cursor: pointer;
    transition: all 0.3s ease;
  }
  .notif-msg.expanded {
    -webkit-line-clamp: unset;
    display: block;
  }

  .notification-item.is-read {
    background-color: #f8fafc;
    opacity: 0.8;
  }
  .notification-item.is-read .bi-check-all {
    color: #94a3b8 !important;
  }

</style>
</head>

<body>

@php

$rolesRouteExists=\Illuminate\Support\Facades\Route::has('mantenimiento.roles.index');

$mantenimientoActive=
($rolesRouteExists && request()->routeIs('mantenimiento.roles.*')) ||
request()->routeIs('mantenimiento.roles-usuario.*') ||
request()->routeIs('mantenimiento.tipo-soporte.*') ||
request()->routeIs('mantenimiento.iguala.*') ||
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
      <div class="nav-section-title">Principal</div>
      <a class="nav-link {{ request()->routeIs('bienvenido') ? 'active' : '' }}" href="{{ route('bienvenido') }}">
        <i class="bi bi-house-door"></i> Inicio
      </a>
      @if(Route::has('dashboard') && (Auth::user()->es_admin || Auth::user()->es_encargado))
      <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
        <i class="bi bi-speedometer2"></i> Dashboard
      </a>
      @endif

      <div class="nav-section-title">Operaciones</div>
      <a class="nav-link {{ request()->routeIs('requerimientos.*') ? 'active' : '' }}" href="{{ route('requerimientos.index') }}">
        <i class="bi bi-journal-text"></i> Requerimientos
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

      @if(Auth::user()->esAdmin || Auth::user()->esEncargado)
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
          <li><a class="dropdown-item" href="{{ route('mantenimiento.categorias.index') }}">Categorías</a></li>
          <li><a class="dropdown-item" href="{{ route('mantenimiento.estados-requerimiento.index') }}">Estados de Req.</a></li>
        </ul>
      </div>

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
            <button class="btn btn-link text-dark p-0 position-relative" type="button" data-bs-toggle="dropdown">
              <i class="bi bi-bell fs-4"></i>
              <span id="notif-count" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none">
                0
              </span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0 p-0" style="width: 380px;" id="notif-list-container">
              <li class="p-2 border-bottom d-flex justify-content-between align-items-center bg-light rounded-top">
                <div class="d-flex align-items-center gap-2">
                  <span class="small fw-bold px-2">Notificaciones</span>
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

          <span class="text-muted d-none d-sm-inline">{{ Auth::user()->name }}</span>
          <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width:38px; height:38px;">
            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
          </div>
          <button class="btn btn-outline-dark btn-sm rounded-3" onclick="toggleSidebar()">
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
            countBadge.textContent = unreadCount;
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
              item.innerHTML = `
                <div class="d-flex justify-content-between align-items-start w-100">
                  <div class="pe-2 flex-grow-1" onclick="toggleNotif(this, event)">
                    <div class="d-flex align-items-center gap-2 mb-1">
                      <strong class="small" style="font-size: 0.8rem;">${n.sender ? n.sender.name : 'Sistema'}</strong>
                      <span class="text-muted" style="font-size: 0.65rem;">${new Date(n.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</span>
                    </div>
                    <p class="mb-0 text-dark notif-msg" style="font-size: 0.85rem; line-height: 1.3;">${n.mensaje}</p>
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
            list.innerHTML = '<li class="p-4 text-center text-muted small">No hay notificaciones</li>';
          }
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

</body>
</html>