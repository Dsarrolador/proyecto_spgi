@extends('layouts.app')

@section('page_title', 'Gestión de Cálculos y Cotizaciones')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-900 text-main mb-0">Dashboard de <span class="text-primary">Cálculos</span></h1>
            <p class="text-muted">Gestión de cotizaciones realizadas y documentos adjuntos.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('leads.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="bi bi-arrow-left me-1"></i> Ver Todos los Leads
            </a>
        </div>
    </div>

    <!-- Filtros por Estado -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
        <div class="card-body p-3 bg-surface">
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <span class="text-muted small fw-bold text-uppercase me-2">Filtrar por Estado:</span>
                <a href="{{ route('leads.indexCalculos') }}" class="btn btn-sm {{ !request('status') ? 'btn-primary' : 'btn-outline-primary' }} rounded-pill px-3">
                    Todos
                </a>
                @php
                    $statuses = ['Pendiente', 'Seguimiento', 'Ganado', 'Perdido'];
                @endphp
                @foreach($statuses as $st)
                    <a href="{{ route('leads.indexCalculos', ['status' => $st]) }}" 
                       class="btn btn-sm {{ request('status') == $st ? 'btn-primary' : 'btn-outline-primary' }} rounded-pill px-3">
                        {{ $st }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                Swal.fire({ icon: 'success', title: '¡Éxito!', text: '{{ session('success') }}', timer: 2000, showConfirmButton: false });
            });
        </script>
    @endif

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table align-middle mb-0 table-hover">
                <thead class="bg-dark text-white">
                    <tr>
                        <th class="ps-4 py-3">Lead / Cliente</th>
                        <th class="py-3">Estado</th>
                        <th class="py-3 text-end">Total Cotizado</th>
                        <th class="py-3 text-center">Cotización (File)</th>
                        <th class="py-3 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-surface">
                    @forelse($leads as $l)
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold text-main">{{ $l->nombre }}</div>
                            <div class="text-muted small"><i class="bi bi-person me-1"></i>{{ $l->persona_contacto }}</div>
                        </td>
                        <td>
                            @php
                                $statusClass = match($l->status) {
                                    'Ganado' => 'bg-success',
                                    'Perdido' => 'bg-danger',
                                    'Seguimiento' => 'bg-info',
                                    default => 'bg-warning text-dark'
                                };
                            @endphp
                            <span class="badge {{ $statusClass }} rounded-pill px-3">{{ $l->status }}</span>
                        </td>
                        <td class="text-end fw-900 text-primary fs-5">
                            ${{ number_format($l->total_estimado, 2) }}
                        </td>
                        <td class="text-center">
                            @if($l->cotizacion_pdf)
                                <div class="d-flex flex-column align-items-center gap-1">
                                    <a href="{{ asset('storage/' . $l->cotizacion_pdf) }}" target="_blank" class="btn btn-sm btn-light border rounded-pill px-3">
                                        <i class="bi bi-file-earmark-pdf text-danger"></i> Ver Documento
                                    </a>
                                    <button class="btn btn-link btn-sm text-muted p-0" onclick="document.getElementById('file_input_{{ $l->id }}').click()">
                                        <small>Reemplazar</small>
                                    </button>
                                </div>
                            @else
                                <button class="btn btn-sm btn-outline-success rounded-pill px-3" onclick="document.getElementById('file_input_{{ $l->id }}').click()">
                                    <i class="bi bi-upload me-1"></i> Subir Archivo
                                </button>
                            @endif
                            
                            <form action="{{ route('leads.updatePdf', $l->id) }}" method="POST" enctype="multipart/form-data" class="d-none">
                                @csrf
                                <input type="file" id="file_input_{{ $l->id }}" name="cotizacion_pdf" onchange="this.form.submit()" accept=".pdf,.xlsx,.xls">
                            </form>
                        </td>
                        <td class="text-center pe-4">
                            <div class="btn-group">
                                <a href="{{ route('leads.show', $l->id) }}" class="btn btn-sm btn-outline-primary rounded-start-pill px-3" title="Detalles">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('leads.calculadora', $l->id) }}" class="btn btn-sm btn-primary px-3" title="Recalcular">
                                    <i class="bi bi-calculator"></i>
                                </a>
                                <a href="{{ route('leads.edit', $l->id) }}" class="btn btn-sm btn-outline-dark rounded-end-pill px-3" title="Editar Lead">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="bi bi-calculator fs-1 d-block mb-2"></i>
                            No se han encontrado leads con cálculos realizados para este filtro.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection
