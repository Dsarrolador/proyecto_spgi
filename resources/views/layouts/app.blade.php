<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Proyecto SPGI</title>

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>

:root{
--spgi-dark:#0b1220;
--spgi-dark-2:#111827;
--spgi-border:#e2e8f0;
--spgi-text:#334155;
--spgi-muted:#64748b;
--spgi-bg:#f8fafc;
--spgi-white:#ffffff;
}

body{
background-color:var(--spgi-bg);
overflow-x:hidden;
}

.spgi-navbar{
background-color:var(--spgi-dark) !important;
min-height:70px;
padding-top:.75rem;
padding-bottom:.75rem;
}

.spgi-navbar .navbar-brand{
font-size:1rem;
line-height:1.2;
white-space:normal;
word-break:break-word;
}

.spgi-back-btn,
.spgi-menu-btn{
width:42px;
height:42px;
display:inline-flex;
align-items:center;
justify-content:center;
border-radius:12px;
}

.spgi-back-btn{
color:#fff;
background:transparent;
border:0;
text-decoration:none;
}

.spgi-back-btn:hover{
background:rgba(255,255,255,.08);
color:#fff;
}

.spgi-menu-btn{
border:1px solid rgba(255,255,255,.18);
background:transparent;
color:#fff;
}

.spgi-menu-btn:hover{
background:rgba(255,255,255,.08);
color:#fff;
border-color:rgba(255,255,255,.28);
}

.spgi-page-offset{
height:86px;
}

.spgi-main{
padding-bottom:2rem;
}

.offcanvas.offcanvas-end{
width:320px;
max-width:88vw;
}

.offcanvas-header{
border-bottom:1px solid rgba(255,255,255,.08);
}

.offcanvas-title{
font-weight:700;
}

.navbar-nav .nav-link{
border-radius:10px;
padding:.8rem .9rem;
color:rgba(255,255,255,.92);
}

.navbar-nav .nav-link:hover{
background-color:rgba(255,255,255,.08);
color:#fff;
}

.navbar-nav .nav-link.active{
background-color:rgba(255,255,255,.12);
color:#fff !important;
}

.table thead th{
background-color:var(--spgi-dark) !important;
color:#ffffff !important;
text-align:center !important;
vertical-align:middle !important;
white-space:nowrap;
}

.table tbody td{
vertical-align:middle !important;
color:var(--spgi-text) !important;
}

.table-responsive{
border-radius:14px;
}

.dropdown-menu{
background-color:#ffffff !important;
border:1px solid var(--spgi-border) !important;
box-shadow:0 10px 25px rgba(0,0,0,.10) !important;
border-radius:12px !important;
padding:.45rem;
}

.dropdown-item{
color:#1e293b !important;
border-radius:8px;
padding:.65rem .8rem;
}

.dropdown-item:hover,
.dropdown-item:focus{
background-color:#f1f5f9 !important;
}

.dropdown-item.active,
.dropdown-item:active{
background-color:#e2e8f0 !important;
color:#0f172a !important;
}

.dropdown-item.text-danger{
color:#dc2626 !important;
}

.card,
.table,
.modal-content{
border-radius:16px;
}

.container,
.container-fluid{
padding-left:1rem;
padding-right:1rem;
}

/* ===== FIX PAGINACION ===== */

.pagination svg{
width:16px !important;
height:16px !important;
}

.pagination{
margin-bottom:0;
}

/* ========================== */

@media (max-width:991.98px){

.spgi-navbar .navbar-brand{
max-width:180px;
font-size:.95rem;
}

.spgi-page-offset{
height:82px;
}

main.container{
max-width:100%;
}

}

@media (max-width:575.98px){

.spgi-navbar{
min-height:64px;
padding-top:.6rem;
padding-bottom:.6rem;
}

.spgi-navbar .navbar-brand{
max-width:145px;
font-size:.90rem;
}

.spgi-back-btn,
.spgi-menu-btn{
width:38px;
height:38px;
border-radius:10px;
}

.spgi-page-offset{
height:76px;
}

.container,
.container-fluid{
padding-left:.75rem;
padding-right:.75rem;
}

.table{
font-size:.9rem;
}

.dropdown-menu{
min-width:100%;
}

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

<nav class="navbar navbar-dark fixed-top shadow-sm spgi-navbar">

<div class="container-fluid">

<div class="d-flex align-items-center justify-content-between w-100 gap-2">

<div class="d-flex align-items-center gap-2 min-w-0">

<a href="{{ url()->previous() }}" class="spgi-back-btn">

<i class="bi bi-arrow-left-circle fs-4"></i>

</a>

<span class="navbar-brand fw-bold mb-0">

@yield('page_title','Requerimientos Cliente')

</span>

</div>

<button class="btn spgi-menu-btn"

type="button"

data-bs-toggle="offcanvas"

data-bs-target="#menuSPGI">

<i class="bi bi-list fs-4"></i>

</button>

</div>

<div class="offcanvas offcanvas-end text-bg-dark"

tabindex="-1"

id="menuSPGI">

<div class="offcanvas-header">

<h5 class="offcanvas-title">Menú</h5>

<button type="button"

class="btn-close btn-close-white"

data-bs-dismiss="offcanvas">

</button>

</div>

<div class="offcanvas-body">

<ul class="navbar-nav gap-1">

<li class="nav-item">

<a class="nav-link {{ request()->routeIs('bienvenido') ? 'active fw-bold' : '' }}"

href="{{ route('bienvenido') }}">

<i class="bi bi-house-door me-2"></i>

Inicio

</a>

</li>

<hr class="border-secondary my-2">

<li class="nav-item">

<a class="nav-link {{ request()->routeIs('usuarios.*') ? 'active fw-bold' : '' }}"

href="{{ route('usuarios.index') }}">

<i class="bi bi-people me-2"></i>

Usuarios

</a>

</li>

<li class="nav-item">

<a class="nav-link {{ request()->routeIs('clientes.*') ? 'active fw-bold' : '' }}"

href="{{ route('clientes.index') }}">

<i class="bi bi-person-vcard me-2"></i>

Clientes

</a>

</li>

<li class="nav-item">

<a class="nav-link {{ request()->routeIs('proyectos.*') ? 'active fw-bold' : '' }}"

href="{{ route('proyectos.index') }}">

<i class="bi bi-kanban me-2"></i>

Proyectos

</a>

</li>

<li class="nav-item">

<a class="nav-link {{ request()->routeIs('requerimientos.*') ? 'active fw-bold' : '' }}"

href="{{ route('requerimientos.index') }}">

<i class="bi bi-journal-text me-2"></i>

Requerimientos

</a>

</li>

<li class="nav-item">

<a class="nav-link {{ request()->routeIs('wiki.*') ? 'active fw-bold' : '' }}"

href="{{ route('wiki.index') }}">

<i class="bi bi-book me-2"></i>

Wiki

</a>

</li>

<hr class="border-secondary my-2">

<li class="nav-item dropdown">

<a class="nav-link dropdown-toggle {{ $mantenimientoActive ? 'active fw-bold' : '' }}"

href="#"

role="button"

data-bs-toggle="dropdown">

<i class="bi bi-gear me-2"></i>

Mantenimiento

</a>

<ul class="dropdown-menu">

@if($rolesRouteExists)

<li>

<a class="dropdown-item {{ request()->routeIs('mantenimiento.roles.*') ? 'active' : '' }}"

href="{{ route('mantenimiento.roles.index') }}">

Roles

</a>

</li>

<li><hr class="dropdown-divider"></li>

@endif

<li>
<a class="dropdown-item {{ request()->routeIs('mantenimiento.roles-usuario.*') ? 'active' : '' }}"
href="{{ route('mantenimiento.roles-usuario.index') }}">
Roles de Usuario
</a>
</li>

<li><hr class="dropdown-divider"></li>

<li>

<a class="dropdown-item {{ request()->routeIs('mantenimiento.tipo-soporte.*') ? 'active' : '' }}"

href="{{ route('mantenimiento.tipo-soporte.index') }}">

Tipo de soporte

</a>

</li>

<li><hr class="dropdown-divider"></li>

<li>

<a class="dropdown-item {{ request()->routeIs('mantenimiento.iguala.*') ? 'active' : '' }}"

href="{{ route('mantenimiento.iguala.index') }}">

Iguala

</a>

</li>

<li><hr class="dropdown-divider"></li>

<li>

<a class="dropdown-item {{ request()->routeIs('mantenimiento.categorias.*') ? 'active' : '' }}"

href="{{ route('mantenimiento.categorias.index') }}">

Categorías

</a>

</li>

<li><hr class="dropdown-divider"></li>

<li>

<a class="dropdown-item {{ request()->routeIs('mantenimiento.estados-requerimiento.*') ? 'active' : '' }}"

href="{{ route('mantenimiento.estados-requerimiento.index') }}">

Estados de Req.

</a>

</li>

</ul>

</li>

<hr class="border-secondary my-2">

<li class="nav-item">

<a class="nav-link text-danger"

href="{{ route('logout') }}">

<i class="bi bi-box-arrow-right me-2"></i>

Cerrar sesión

</a>

</li>

</ul>

</div>

</div>

</div>

</nav>

<div class="spgi-page-offset"></div>

<main class="container spgi-main">

@yield('content')

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

@stack('scripts')

</body>
</html>