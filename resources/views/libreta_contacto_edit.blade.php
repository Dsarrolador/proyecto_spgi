<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Editar Contacto</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body class="bg-light">

<nav class="navbar navbar-dark bg-dark shadow-sm">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="{{ route('libreta_contacto.index') }}">Proyecto SPGI</a>
  </div>
</nav>

<div class="container mt-5">

  <div class="card shadow-lg">
    <div class="card-header bg-warning">
      <h4 class="mb-0"><i class="bi bi-pencil-square"></i> Editar contacto</h4>
    </div>

    <div class="card-body">

      <form method="POST" action="{{ route('libreta_contacto.update', $contacto->id) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
          <label class="form-label fw-bold">Nombre</label>
          <input type="text" name="nombre" class="form-control" value="{{ $contacto->nombre }}" required>
        </div>

        <div class="mb-3">
          <label class="form-label fw-bold">Rol</label>
          <select name="codigo_rol" class="form-select">
            @foreach($roles as $rol)
              <option value="{{ $rol->id }}" @if($rol->id == $contacto->codigo_rol) selected @endif>
                {{ $rol->nombre }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label fw-bold">Teléfono</label>
          <input type="text" name="telefono" class="form-control" value="{{ $contacto->telefono }}">
        </div>

        <div class="mb-3">
          <label class="form-label fw-bold">Correo</label>
          <input type="email" name="correo" class="form-control" value="{{ $contacto->correo }}">
        </div>

        <div class="mb-3">
          <label class="form-label fw-bold">Nota</label>
          <textarea name="nota" class="form-control" rows="3">{{ $contacto->nota }}</textarea>
        </div>

        <div class="mt-4 d-flex justify-content-between">
          <a href="{{ route('libreta_contacto.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Volver
          </a>

          <button type="submit" class="btn btn-success">
            <i class="bi bi-save"></i> Guardar cambios
          </button>
        </div>

      </form>

    </div>
  </div>

</div>

</body>
</html>
