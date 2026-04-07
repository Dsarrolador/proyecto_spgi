<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Agregar Cliente</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body class="bg-light">

<!-- 🔹 NAVBAR -->
<nav class="navbar bg-dark navbar-dark shadow-sm">
  <div class="container-fluid d-flex align-items-center justify-content-between">

    <div class="d-flex align-items-center">
      <a href="{{ route('clientes.index') }}" class="text-white me-2 fs-5">
        <i class="bi bi-arrow-left-circle"></i>
      </a>
      <span class="navbar-brand fw-bold mb-0">Proyecto SPGI</span>
    </div>

  </div>
</nav>

<div class="container py-5" style="margin-top: 60px;">

  <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
    <h3 class="fw-bold text-primary mb-0">
      <i class="bi bi-person-plus"></i> Agregar Cliente
    </h3>
  </div>

  <!-- 🔹 FORMULARIO PRINCIPAL -->
  <div class="card shadow-sm border-0 rounded-3">
    <div class="card-body">

      <form action="{{ route('clientes.store') }}" method="POST">
        @csrf

        <div class="row">

          <!-- 🧾 Nombre -->
          <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold">Nombre</label>
            <input type="text" name="nombre" class="form-control" required value="{{ old('nombre') }}">
          </div>

          <!-- 🆔 RNC -->
          <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold">RNC</label>
            <input type="text" name="rnc" class="form-control" value="{{ old('rnc') }}">
          </div>

          <!-- ☎ Teléfono -->
          <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold">Teléfono</label>
            <input type="text" name="telefono_principal" class="form-control" value="{{ old('telefono_principal') }}">
          </div>

          <!-- 🏷 Clasificación -->
          <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold">Clasificación</label>
            <select name="clasificacion_negocio" class="form-select">
              <option value="">-- Selecciona una opción --</option>
              <option value="A" {{ old('clasificacion_negocio') == 'A' ? 'selected' : '' }}>A</option>
              <option value="B" {{ old('clasificacion_negocio') == 'B' ? 'selected' : '' }}>B</option>
              <option value="C" {{ old('clasificacion_negocio') == 'C' ? 'selected' : '' }}>C</option>
              <option value="D" {{ old('clasificacion_negocio') == 'D' ? 'selected' : '' }}>D</option>
            </select>
          </div>

          <!-- 🗂 Categoría -->
          <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold">Categoría</label>
            <select name="clasificacion_interna" class="form-select">
              <option value="">-- Selecciona una categoría --</option>
              @foreach ($categorias as $categoria)
                <option value="{{ $categoria->id }}" {{ (string)old('clasificacion_interna') === (string)$categoria->id ? 'selected' : '' }}>
                  {{ $categoria->categoria }}
                </option>
              @endforeach
            </select>
          </div>

          <!-- 🧾 NUEVO: Categoría iguala (al lado de Categoría) -->
          <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold">Categoría iguala</label>
            <select name="categoria_iguala" class="form-select">
              <option value="">-- Selecciona una opción --</option>
              <option value="Cliente de iguala solo sistema" {{ old('categoria_iguala') == 'Cliente de iguala solo sistema' ? 'selected' : '' }}>Cliente de iguala solo sistema</option>
              <option value="Cliente de iguala premium" {{ old('categoria_iguala') == 'Cliente de iguala premium' ? 'selected' : '' }}>Cliente de iguala premium</option>
              <option value="Cliente de iguala avanzada" {{ old('categoria_iguala') == 'Cliente de iguala avanzada' ? 'selected' : '' }}>Cliente de iguala avanzada</option>
              <option value="Cliente de iguala Basico" {{ old('categoria_iguala') == 'Cliente de iguala Basico' ? 'selected' : '' }}>Cliente de iguala Basico</option>
              <option value="Cliente sin iguala" {{ old('categoria_iguala') == 'Cliente sin iguala' ? 'selected' : '' }}>Cliente sin iguala</option>
            </select>
          </div>

          <!-- 📝 Notas -->
          <div class="col-md-12 mb-3">
            <label class="form-label fw-semibold">Notas</label>
            <textarea name="notas" class="form-control" rows="2">{{ old('notas') }}</textarea>
          </div>

          <!-- 📍 Dirección -->
          <div class="col-md-12 mb-3">
            <label class="form-label fw-semibold">Dirección</label>
            <input type="text" name="direccion_escrita" class="form-control" value="{{ old('direccion_escrita') }}">
          </div>

        </div>

        <!-- 🔹 BOTONES -->
        <div class="text-end">
          <a href="{{ route('clientes.index') }}" class="btn btn-secondary">
            <i class="bi bi-x-circle"></i> Cancelar
          </a>
          <button type="submit" class="btn btn-primary">
            <i class="bi bi-save"></i> Guardar Cliente
          </button>
        </div>

      </form>

    </div>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
