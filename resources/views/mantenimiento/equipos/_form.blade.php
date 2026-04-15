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
    <div class="col-12">
        <label class="form-label fw-bold small">URL de Drivers</label>
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
