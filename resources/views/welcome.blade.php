<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SPGI - Sistema Profesional de Gestión Interna</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800;900&display=swap" rel="stylesheet">
    
    <!-- Icons & Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --spgi-primary: #3b82f6;
            --spgi-primary-glow: rgba(59, 130, 246, 0.4);
            --bg-deep: #050b18;
            --bg-surface-glass: rgba(15, 23, 42, 0.7);
            --border-main: rgba(255, 255, 255, 0.08);
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
        }

        body {
            background-color: var(--bg-deep);
            color: var(--text-main);
            font-family: 'Outfit', sans-serif;
            margin: 0; min-height: 100vh;
            display: flex; flex-direction: column;
            overflow-x: hidden;
            background: 
                radial-gradient(circle at 10% 20%, rgba(59, 130, 246, 0.15), transparent 40%),
                radial-gradient(circle at 90% 80%, rgba(37, 99, 235, 0.1), transparent 40%),
                var(--bg-deep);
        }

        /* Animated background blobs */
        .blob {
            position: absolute; width: 500px; height: 500px;
            background: var(--spgi-primary-glow); filter: blur(80px);
            border-radius: 50%; z-index: -1; animation: float 20s infinite alternate;
        }
        @keyframes float {
            0% { transform: translate(0, 0) scale(1); }
            100% { transform: translate(50px, 100px) scale(1.1); }
        }

        .navbar {
            padding: 30px 0; border-bottom: 1px solid var(--border-main);
            backdrop-filter: blur(10px); background: rgba(5, 11, 24, 0.5);
        }

        .hero-section {
            flex: 1; display: flex; align-items: center; justify-content: center;
            padding: 100px 0; position: relative;
        }

        .glass-panel {
            background: var(--bg-surface-glass);
            border: 1px solid var(--border-main);
            backdrop-filter: blur(24px);
            border-radius: 40px;
            padding: 80px 40px;
            max-width: 900px;
            width: 100%;
            box-shadow: 0 40px 100px -20px rgba(0,0,0,0.6);
            animation: fadeIn 1s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .badge-premium {
            display: inline-flex; align-items: center; gap: 8px;
            background: rgba(var(--spgi-primary-rgb, 59, 130, 246), 0.1);
            color: var(--spgi-primary); font-weight: 800; font-size: 0.8rem;
            text-transform: uppercase; letter-spacing: 2px;
            padding: 12px 24px; border-radius: 999px; border: 1px solid var(--border-main);
            margin-bottom: 32px;
        }

        .main-title {
            font-size: 4.5rem; font-weight: 900; line-height: 1;
            letter-spacing: -3px; margin-bottom: 24px;
            background: linear-gradient(135deg, #fff 0%, #94a3b8 100%);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        }

        .lead-text {
            color: var(--text-muted); font-size: 1.25rem; max-width: 600px;
            margin: 0 auto 48px; line-height: 1.6;
        }

        .btn-get-started {
            padding: 20px 48px; border-radius: 20px; font-weight: 800;
            font-size: 1.2rem; transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            background: linear-gradient(135deg, var(--spgi-primary), #2563eb);
            border: none; color: white; box-shadow: 0 20px 40px var(--spgi-primary-glow);
        }

        .btn-get-started:hover {
            transform: scale(1.05) translateY(-5px);
            box-shadow: 0 30px 60px var(--spgi-primary-glow);
            filter: brightness(1.1); color: white;
        }

        .stats-grid {
            display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;
            margin-top: 60px; border-top: 1px solid var(--border-main); padding-top: 60px;
        }

        .stat-item h3 { font-weight: 900; font-size: 2rem; margin: 0; color: #fff; }
        .stat-item p { color: var(--text-muted); font-size: 0.9rem; margin: 0; text-transform: uppercase; letter-spacing: 1px; font-weight: 600; }

        footer {
            padding: 40px 0; border-top: 1px solid var(--border-main);
            text-align: center; color: var(--text-muted); font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            .main-title { font-size: 2.8rem; letter-spacing: -1px; }
            .glass-panel { padding: 40px 20px; margin: 0 20px; }
            .stats-grid { grid-template-columns: 1fr; gap: 40px; }
        }
    </style>
</head>
<body>

    <div class="blob" style="top: -10%; left: -10%;"></div>
    <div class="blob" style="bottom: -10%; right: -10%; animation-delay: -5s;"></div>

    <nav class="navbar navbar-expand-lg">
        <div class="container d-flex justify-content-between">
            <a class="navbar-brand d-flex align-items-center gap-2" href="#">
                <div style="width: 38px; height: 38px; background: var(--spgi-primary); border-radius: 12px; display: grid; place-items: center; color: white; font-weight: 900;">G</div>
                <span style="font-weight: 900; font-size: 1.5rem; letter-spacing: -1px; color: #fff;">SPGI</span>
            </a>
            <div>
                @auth
                    <a href="{{ url('/home') }}" class="btn btn-link text-decoration-none fw-bold" style="color: var(--spgi-primary);">Panel de Control</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-spgi py-2 px-4 rounded-pill fw-bold" style="border-radius: 12px; border: 1px solid var(--border-main); color: #fff;">
                        Iniciar Sesión
                    </a>
                @endif
            </div>
        </div>
    </nav>

    <section class="hero-section">
        <div class="container text-center">
            <div class="glass-panel mx-auto">
                <div class="badge-premium">
                    <i class="bi bi-stars"></i> Potenciamos tu Productividad
                </div>
                
                <h1 class="main-title">Control Total del Sistema SPGI</h1>
                
                <p class="lead-text">
                    La plataforma definitiva para la gestión de requerimientos, clientes y proyectos técnicos con una interfaz premium de alto rendimiento.
                </p>

                <div class="d-flex flex-column align-items-center gap-4">
                    <a href="{{ route('login') }}" class="btn btn-get-started">
                        Empezar Ahora <i class="bi bi-arrow-right-short ms-2"></i>
                    </a>
                    <span class="text-muted small">Plataforma segura e interna • v2.5 Rediseño Elite</span>
                </div>

                <div class="stats-grid">
                    <div class="stat-item">
                        <h3>99.9%</h3>
                        <p>Disponibilidad</p>
                    </div>
                    <div class="stat-item">
                        <h3>+1.5k</h3>
                        <p>Requerimientos</p>
                    </div>
                    <div class="stat-item">
                        <h3>&lt;40ms</h3>
                        <p>Latencia</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer>
        <div class="container">
            <p>&copy; {{ date('Y') }} Derechos reservados por Intecsol SRL</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
