<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión - Proyecto SPGI</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body{
            min-height:100vh;
            margin:0;
            display:grid;
            place-items:center;
            padding:20px;

            background:
                radial-gradient(900px 600px at 15% 18%, rgba(59,130,246,.18), transparent 55%),
                radial-gradient(800px 600px at 90% 20%, rgba(236,72,153,.14), transparent 55%),
                radial-gradient(700px 500px at 50% 90%, rgba(34,197,94,.12), transparent 60%),
                linear-gradient(180deg, #f7f8fb 0%, #eef2f7 45%, #f7f8fb 100%);
        }

        .login-card{
            width:100%;
            max-width:500px;
            border-radius:22px;
            background:rgba(255,255,255,.88);
            border:1px solid rgba(0,0,0,.08);
            box-shadow:0 30px 80px rgba(0,0,0,.18);
            backdrop-filter:blur(12px);
            padding:35px;
            position:relative;

            transform:translateY(15px) scale(.98);
            opacity:0;
            animation:cardIn .7s cubic-bezier(.2,.8,.2,1) forwards;
        }

        @keyframes cardIn{
            to{transform:translateY(0) scale(1); opacity:1;}
        }

        .badge-chip{
            display:inline-flex;
            align-items:center;
            gap:6px;
            padding:7px 12px;
            border-radius:999px;
            background:rgba(0,0,0,.05);
            font-weight:700;
            font-size:.9rem;
            margin-bottom:18px;
        }

        .title{
            font-weight:900;
            font-size:1.9rem;
            margin-bottom:8px;
        }

        .subtitle{
            color:#6b7280;
            margin-bottom:20px;
        }

        .divider{
            height:1px;
            background:linear-gradient(90deg, transparent, rgba(0,0,0,.2), transparent);
            margin:15px 0 25px;
        }

        .form-label{
            font-weight:600;
        }

        .form-control{
            border-radius:12px;
            padding:11px;
        }

        .btn-main{
            border-radius:14px;
            font-weight:800;
            padding:12px;
            transition:.2s;
            position:relative;
            overflow:hidden;
        }

        .btn-main:hover{
            transform:translateY(-2px);
            box-shadow:0 15px 30px rgba(37,99,235,.2);
        }

        .btn-main::after{
            content:"";
            position:absolute;
            top:-50%;
            left:-30%;
            width:40%;
            height:200%;
            background:linear-gradient(90deg, transparent, rgba(255,255,255,.5), transparent);
            transform:rotate(20deg);
            transition:left .5s;
        }

        .btn-main:hover::after{
            left:120%;
        }

        .brand{
            position:fixed;
            bottom:15px;
            left:0;
            right:0;
            text-align:center;
            font-size:.9rem;
            color:rgba(0,0,0,.5);
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

</div>

<div class="brand">
    SPGI • {{ date('Y') }}
</div>

</body>
</html>
