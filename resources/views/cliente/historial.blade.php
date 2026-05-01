@extends('layouts.cliente')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0">Historial Completo</h5>
                <form action="{{ route('cliente.historial') }}" method="GET" class="d-flex gap-2 flex-wrap justify-content-end">
                    <input type="text" name="search" class="form-control form-control-sm rounded-pill px-3" style="width: 150px;" placeholder="Buscar..." value="{{ request('search') }}">
                    
                    <select name="mes" class="form-select form-select-sm rounded-pill px-3" style="width: 120px;" onchange="this.form.submit()">
                        <option value="">Cualquier Mes</option>
                        @foreach($meses as $num => $nombre)
                            <option value="{{ $num }}" {{ request('mes') == $num ? 'selected' : '' }}>{{ $nombre }}</option>
                        @endforeach
                    </select>

                    <select name="anio" class="form-select form-select-sm rounded-pill px-3" style="width: 100px;" onchange="this.form.submit()">
                        <option value="">Año</option>
                        @for($a = now()->year; $a >= 2023; $a--)
                            <option value="{{ $a }}" {{ request('anio') == $a ? 'selected' : '' }}>{{ $a }}</option>
                        @endfor
                    </select>

                    <select name="estado" class="form-select form-select-sm rounded-pill px-3" style="width: 150px;" onchange="this.form.submit()">
                        <option value="">Todos los Estados</option>
                        @foreach($estados as $est)
                            <option value="{{ $est->id }}" {{ request('estado') == $est->id ? 'selected' : '' }}>{{ $est->nombre }}</option>
                        @endforeach
                    </select>
                </form>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Fecha</th>
                            <th>Descripción</th>
                            <th>Estado</th>
                            <th>Encargado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($requirements as $req)
                        @php
                            $statusColor = $req->estadoRequerimiento->color ?? '#6c757d';
                            $statusName = $req->estadoRequerimiento->nombre ?? 'N/A';
                        @endphp
                        <tr>
                            <td class="small">{{ $req->created_at->timezone('America/Santo_Domingo')->format('d/m/Y H:i') }}</td>
                            <td class="small text-truncate" style="max-width: 200px;">{{ $req->texto_imagen }}</td>
                            <td>
                                <span class="badge" style="background-color: {{ $statusColor }}; color: #fff; border: 1px solid rgba(0,0,0,0.1);">
                                    {{ $statusName }}
                                </span>
                            </td>
                            <td class="small">{{ $req->asignado->name ?? 'Pendiente' }}</td>
                            <td class="text-end">
                                <a href="{{ route('cliente.requerimientos.show', $req->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                    <i class="bi bi-journal-text me-1"></i> Detalles
                                </a>
                            </td>
                        </tr>
                        @endforeach
                        @if($requirements->isEmpty())
                        <tr>
                            <td colspan="5" class="text-center text-muted py-5">No se encontraron registros en el historial.</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $requirements->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
