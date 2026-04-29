<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión - Proyecto SPGI</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}?v=3">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --spgi-primary: #3b82f6;
            --spgi-primary-glow: rgba(59, 130, 246, 0.5);
            --bg-deep: #050b18;
            --bg-surface-glass: rgba(15, 23, 42, 0.8);
            --border-main: rgba(255, 255, 255, 0.08);
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --shadow-main: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        body {
            min-height: 100vh; margin: 0; display: grid; place-items: center; padding: 20px;
            background: 
                radial-gradient(circle at 15% 15%, rgba(59, 130, 246, 0.15), transparent 40%),
                radial-gradient(circle at 85% 85%, rgba(37, 99, 235, 0.1), transparent 40%),
                var(--bg-deep);
            color: var(--text-main); font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }

        .login-card {
            width: 100%; max-width: 480px; border-radius: 28px;
            background: var(--bg-surface-glass); border: 1px solid var(--border-main);
            box-shadow: var(--shadow-main); backdrop-filter: blur(24px); padding: 48px;
            position: relative; animation: cardIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0; transform: translateY(20px);
        }

        @keyframes cardIn { to { opacity: 1; transform: translateY(0); } }

        .badge-chip {
            display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; border-radius: 999px;
            background: rgba(59, 130, 246, 0.1); font-weight: 800; font-size: .8rem;
            color: var(--spgi-primary); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 24px;
            border: 1px solid var(--border-main);
        }

        .title { font-weight: 900; font-size: 2.2rem; margin-bottom: 8px; letter-spacing: -1px; }
        .subtitle { color: var(--text-muted); margin-bottom: 32px; font-size: 1.05rem; }

        .divider { height: 1px; background: var(--border-main); margin: 24px 0 32px; }

        .form-label { font-weight: 700; color: var(--text-muted); font-size: .8rem; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px; }

        .form-control {
            background: rgba(0,0,0,0.2) !important; color: white !important;
            border-radius: 14px; padding: 14px; border: 1px solid var(--border-main); transition: all 0.3s ease;
        }
        .form-control:focus {
            background: rgba(0,0,0,0.3) !important; border-color: var(--spgi-primary);
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }

        .btn-main {
            border-radius: 16px; font-weight: 800; padding: 16px; transition: all 0.3s ease;
            background: linear-gradient(135deg, var(--spgi-primary), #2563eb); border: none;
            color: white; font-size: 1.1rem; box-shadow: 0 10px 20px var(--spgi-primary-glow);
        }
        .btn-main:hover {
            transform: translateY(-2px); box-shadow: 0 15px 30px var(--spgi-primary-glow); filter: brightness(1.1);
        }

        .brand {
            position: fixed; bottom: 20px; text-align: center; font-size: .9rem;
            color: var(--text-muted); font-weight: 600; letter-spacing: 1px;
        }
    </style>
</head>

<body>

<div class="login-card text-center">

    <div class="badge-chip">
        <i class="bi bi-shield-lock"></i> Acceso seguro
    </div>

    <div class="title">Iniciar sesión</div>
    <div class="subtitle">Accede al sistema <b>SPGI</b></div>

    <div class="divider"></div>

    {{-- Mensaje de error --}}
    @if (session('error'))
        <div class="alert alert-danger py-2">{{ session('error') }}</div>
    @endif

    {{-- Validación --}}
    @if ($errors->any())
        <div class="alert alert-danger py-2">
            <ul class="mb-0 text-start">
                @foreach ($errors->all() as $error)
                    <li class="small">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(isset($seconds_left) && $seconds_left > 0)
        <div class="alert alert-danger py-4 text-center mt-4" style="border-radius: 20px; background: rgba(220, 38, 38, 0.1); border-color: rgba(220, 38, 38, 0.2); color: #f87171;">
            <i class="bi bi-shield-lock-fill d-block mb-3" style="font-size: 3rem;"></i>
            <h5 class="fw-bold mb-2">Acceso bloqueado</h5>
            <p class="small mb-0">Demasiados intentos fallidos. Podrá intentar nuevamente en <strong id="lockout-timer">{{ ceil($seconds_left / 60) }} minutos</strong>.</p>
        </div>
        <script>
            let secondsLeft = {{ $seconds_left }};
            setInterval(() => {
                secondsLeft--;
                if(secondsLeft <= 0) {
                    window.location.reload();
                } else {
                    let mins = Math.ceil(secondsLeft / 60);
                    document.getElementById('lockout-timer').innerText = mins + ' minuto' + (mins !== 1 ? 's' : '');
                }
            }, 1000);
        </script>
    @else
        <form action="{{ route('login.submit') }}" method="POST">
            @csrf

            <div class="mb-3 text-start">
                <label class="form-label">Nombre de usuario</label>
                <input type="text" name="name" class="form-control" required autofocus>
            </div>

            <div class="mb-3 text-start">
                <label class="form-label">Contraseña</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <button class="btn btn-primary w-100 btn-main">
                <i class="bi bi-box-arrow-in-right me-2"></i>
                Entrar
            </button>
        </form>
    @endif

</div>

<div class="brand">
    <div class="mb-3">SPGI • {{ date('Y') }}</div>
    <div class="d-inline-flex align-items-center gap-2 px-3 py-2 rounded-pill" style="background: rgba(255,255,255,0.03); border: 1px solid var(--border-main); backdrop-filter: blur(12px); box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
        <i class="bi bi-code-slash" style="color: var(--spgi-primary); font-size: 0.9rem;"></i>
        <span style="font-size: 0.75rem; color: var(--text-muted); letter-spacing: 0.5px;">Desarrollado por <strong style="color: var(--text-main);">Sebastian Lopez Maria</strong></span>
    </div>
</div>

</body>
</html>
