<div class="row g-3">
    <div class="col-md-8">
        <label class="form-label fw-bold small">Nombre del Equipo</label>
        <input type="text" name="nombre" class="form-control rounded-3" value="{{ $equipo->nombre ?? '' }}" placeholder="Ej: Impresora de Etiquetas Térmica" required>
    </div>
    <div class="col-md-4">
        <label class="form-label fw-bold small">Tipo</label>
        <select name="tipo_equipo_id" class="form-select rounded-3" required>
            <option value="">-- Seleccionar --</option>
            @foreach($tipos as $t)
                <option value="{{ $t->id }}" {{ (isset($equipo) && $equipo->tipo_equipo_id == $t->id) ? 'selected' : '' }}>
                    {{ $t->nombre }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label fw-bold small">Marca</label>
        <input type="text" name="marca" class="form-control rounded-3" value="{{ $equipo->marca ?? '' }}" placeholder="Ej: Zebra, HP, Dell">
    </div>
    <div class="col-md-6">
        <label class="form-label fw-bold small">Modelo</label>
        <input type="text" name="modelo" class="form-control rounded-3" value="{{ $equipo->modelo ?? '' }}" placeholder="Ej: ZT230, LaserJet Pro">
    </div>
    <div class="col-12">
        <label class="form-label fw-bold small">Características Esenciales</label>
        <textarea name="caracteristicas" class="form-control rounded-3" rows="2" placeholder="Ej: Térmica, USB, Ethernet, 203 DPI">{{ $equipo->caracteristicas ?? '' }}</textarea>
    </div>
    <div class="col-12">
        <label class="form-label fw-bold small">Configuración Estándar / Base</label>
        <textarea name="configuracion_estandar" class="form-control rounded-3" rows="3" placeholder="Ej: Baudios: 9600, Paridad: None, Protocolo: ZPL">{{ $equipo->configuracion_estandar ?? '' }}</textarea>
    </div>
    <div class="col-12 mt-2">
        <label class="form-label fw-bold small"><i class="bi bi-cloud-upload me-1 text-primary"></i>Cargar Driver Universal (Catálogo)</label>
        <input type="file" name="driver_file" class="form-control rounded-3">
        @if(isset($equipo) && $equipo->driverDoc)
            <div class="mt-2 d-flex align-items-center gap-2">
                <span class="badge bg-success-soft text-success border border-success-subtle">
                    <i class="bi bi-check-circle-fill me-1"></i> Driver cargado
                </span>
                <a href="{{ route('wiki.download', $equipo->driverDoc->id) }}" class="btn btn-link btn-sm p-0 text-decoration-none">
                    Ver actual
                </a>
            </div>
        @endif
    </div>
    <div class="col-12">
        <label class="form-label fw-bold small">URL Externa de Drivers (Opcional)</label>
        <input type="url" name="driver_url" class="form-control rounded-3" value="{{ $equipo->driver_url ?? '' }}" placeholder="https://ejemplo.com/descarga-drivers">
    </div>
    @if(isset($equipo))
        <div class="col-12">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="activo" value="1" id="activo{{ $equipo->id }}" {{ $equipo->activo ? 'checked' : '' }}>
                <label class="form-check-label" for="activo{{ $equipo->id }}">Equipo Activo</label>
            </div>
        </div>
    @endif
</div>
