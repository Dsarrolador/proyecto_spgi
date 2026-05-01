<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal de Clientes - SPGI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --spgi-primary: #0d6efd;
            --bg-master: #f8f9fa;
        }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-master);
        }
        .sidebar {
            width: 280px;
            height: 100vh;
            position: fixed;
            background: #fff;
            border-right: 1px solid #eee;
            padding: 2rem 1.5rem;
        }
        .main-content {
            margin-left: 280px;
            padding: 2rem 3rem;
        }
        .nav-link {
            color: #6c757d;
            font-weight: 500;
            padding: 0.8rem 1rem;
            border-radius: 10px;
            margin-bottom: 0.5rem;
        }
        .nav-link:hover, .nav-link.active {
            background: rgba(13, 110, 253, 0.05);
            color: var(--spgi-primary);
        }
        .stat-card {
            background: #fff;
            border-radius: 15px;
            padding: 1.5rem;
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.02);
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="mb-5">
            <h4 class="fw-bold text-primary">SPGI <span class="text-dark">Portal</span></h4>
        </div>
        <nav class="nav flex-column">
            <a class="nav-link {{ request()->routeIs('cliente.dashboard') ? 'active' : '' }}" href="{{ route('cliente.dashboard') }}">
                <i class="bi bi-speedometer2 me-2"></i> Mi Resumen
            </a>
            <a class="nav-link {{ request()->routeIs('cliente.historial') ? 'active' : '' }}" href="{{ route('cliente.historial') }}">
                <i class="bi bi-clock-history me-2"></i> Historial
            </a>
            <a class="nav-link {{ request()->routeIs('cliente.novedades') ? 'active' : '' }}" href="{{ route('cliente.novedades') }}">
                <i class="bi bi-journal-text me-2"></i> Novedades
            </a>
            <hr>
            <form action="{{ route('cliente.logout') }}" method="POST">
                @csrf
                <button type="submit" class="nav-link border-0 bg-transparent w-100 text-start text-danger">
                    <i class="bi bi-box-arrow-left me-2"></i> Cerrar Sesión
                </button>
            </form>
        </nav>
    </div>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-0">Bienvenido, {{ Auth::guard('cliente')->user()->cliente->nombre }}</h2>
                <p class="text-muted">Aquí tienes el estado actual de tus servicios.</p>
            </div>
        </div>

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
