@extends('layouts.app')

@section('page_title', 'Detalle de Planilla de Horas Extras')

@section('content')
<style>
  .spgi-bg{ padding: 12px 0 24px 0; }
  .excel-card {
    background: var(--bg-surface);
    border: 1px solid var(--border-main);
    border-radius: 20px;
    box-shadow: var(--shadow-main);
    padding: 30px;
    margin-bottom: 30px;
    position: relative;
  }
  .excel-header-box {
    border: 2px solid var(--text-main);
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 25px;
    background: rgba(var(--text-main), 0.02);
  }
  .law-table {
    max-width: 320px;
    border: 1px solid var(--border-main);
    font-size: 0.85rem;
    margin-bottom: 0;
  }
  .law-table th {
    background: #0f172a !important;
    color: #ffffff !important;
    text-align: center;
    font-weight: 700;
  }
  .law-table td {
    text-align: center;
    font-weight: 600;
  }
  .excel-table {
    border: 1px solid var(--border-main);
    margin-top: 15px;
  }
  .excel-table thead th {
    background: #0b1220 !important;
    color: #ffffff !important;
    text-align: center;
    padding: 12px;
    font-size: 0.8rem;
    letter-spacing: 0.5px;
    font-weight: 700;
    border: 1px solid rgba(255,255,255,0.1) !important;
  }
  .excel-table tbody td {
    padding: 12px;
    border: 1px solid var(--border-main);
    text-align: center;
    vertical-align: middle;
  }
  .signature-section {
    display: flex;
    justify-content: space-around;
    margin-top: 50px;
    padding-top: 30px;
    border-top: 1px dashed var(--border-main);
    flex-wrap: wrap;
    gap: 30px;
  }
  .signature-box {
    width: 250px;
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
  }
  .signature-line {
    width: 100%;
    height: 1px;
    background-color: var(--text-main);
    margin-bottom: 10px;
  }
  .signature-title {
    font-size: 0.85rem;
    font-weight: 800;
    text-transform: uppercase;
    color: var(--text-muted);
  }
  .form-inline-add {
    background: rgba(59, 130, 246, 0.05);
    border: 1px dashed var(--spgi-primary);
    border-radius: 16px;
    padding: 20px;
    margin-bottom: 25px;
  }
  .state-badge {
    position: absolute;
    top: 30px;
    right: 30px;
  }
</style>

<div class="spgi-bg">
  <div class="container">
    
    <!-- Top Bar actions -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold text-gradient">Visualización de Planilla</h1>
            <p class="text-muted mb-0">Vista oficial e interactiva del reporte de horas extras.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('horas-extras.pdf', $planilla->id) }}" target="_blank" class="btn btn-outline-primary rounded-pill fw-bold">
                <i class="bi bi-file-earmark-pdf me-2"></i> Exportar PDF
            </a>
            <a href="{{ route('horas-extras.index') }}" class="btn btn-outline-secondary rounded-pill">
                <i class="bi bi-arrow-left me-2"></i> Volver
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4 animate__animated animate__fadeIn">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4 animate__animated animate__fadeIn">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
        </div>
    @endif

    <!-- Excel-like Structure Card -->
    <div class="excel-card animate__animated animate__fadeInUp">
      
      <!-- State Badge -->
      <div class="state-badge">
        @if($planilla->estado == 'Borrador')
          <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fs-7 fw-bold">ESTADO: BORRADOR</span>
        @else
          <span class="badge bg-success px-3 py-2 rounded-pill fs-7 fw-bold">ESTADO: APROBADO</span>
        @endif
      </div>

      <!-- Excel Header: Company Details -->
      <div class="excel-header-box text-center">
        <h2 class="h4 fw-extrabold mb-1" style="letter-spacing: 1px; color: var(--text-main);">INTECSOL, SRL</h2>
        <p class="mb-1 fw-bold">RNC: 1-3027434-7</p>
        <p class="mb-1 text-muted small">Ave. Nuñez de Cáceres No. 250, Res. M+B Apto. 2B, El Millón Sto. Dgo</p>
        <p class="mb-3 text-muted small">TELEFONO: 829-598-0119</p>
        
        <div class="border-top pt-3 mt-2 d-flex justify-content-between align-items-center flex-wrap gap-3">
          <div class="text-start">
            <span class="fw-bold d-block">PLANILLA DE HORAS EXTRAS TRABAJADAS</span>
            <span class="text-muted small">{{ $planilla->titulo }}</span>
          </div>
          <div class="text-end">
            <span class="fw-bold d-block">Fecha Registro:</span>
            <span class="text-muted small">{{ $planilla->fecha_registro->format('d/m/Y') }}</span>
          </div>
        </div>
      </div>

      <!-- Law Context / DR Code Section -->
      <div class="d-flex justify-content-between align-items-start flex-wrap gap-4 mb-4">
        <div>
          <span class="badge bg-dark px-3 py-2 rounded-pill mb-2">CÓDIGO DE TRABAJO REP. DOM. ARTÍCULO 203</span>
          <p class="text-muted small mb-0" style="max-width: 500px;">
            El recargo legal de horas extras se calcula según el artículo 203: las horas normales de jornada semanal acumuladas tienen recargos específicos una vez superada la jornada legal.
          </p>
        </div>
        
        <!-- Surcharges Info Table -->
        <table class="table table-sm table-bordered law-table shadow-sm">
          <thead>
            <tr>
              <th>TOPE DE HORAS</th>
              <th>% A AUMENTAR</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>DE 45-68 HORAS</td>
              <td class="text-warning fw-bold">35%</td>
            </tr>
            <tr>
              <td>DE 69 EN ADELANTE</td>
              <td class="text-danger fw-bold">100%</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Add Detail Row Form (Only visible in Borrador state) -->
      @if($planilla->estado == 'Borrador')
      <div class="form-inline-add">
        <h5 class="fw-bold mb-3 text-primary"><i class="bi bi-plus-circle-fill me-2"></i>Agregar Registro a la Planilla</h5>
        <form action="{{ route('horas-extras.detalles.store', $planilla->id) }}" method="POST">
          @csrf
          <div class="row g-3">
            
            <div class="col-md-2 col-sm-6">
              <label for="fecha" class="form-label small fw-bold">Fecha</label>
              <input type="date" name="fecha" id="fecha" class="form-control form-control-sm" value="{{ date('Y-m-d') }}" required>
            </div>
            
            <div class="col-md-3 col-sm-6">
              <label for="colaborador" class="form-label small fw-bold">Colaborador</label>
              <input type="text" name="colaborador" id="colaborador" class="form-control form-control-sm" list="colaboradores-list" placeholder="Nombre completo" required autocomplete="off">
              <datalist id="colaboradores-list">
                @foreach($usuarios as $u)
                  <option value="{{ $u->name }}">
                @endforeach
              </datalist>
            </div>
            
            <div class="col-md-2 col-sm-12">
              <label for="concepto" class="form-label small fw-bold">Concepto</label>
              <input type="text" name="concepto" id="concepto" class="form-control form-control-sm" placeholder="Ej: Soporte Servidores" required>
            </div>
            
            <div class="col-md-2 col-sm-6">
              <label class="form-label small fw-bold">H. Inicio (12h)</label>
              <div class="d-flex gap-1">
                <select id="start_hour" class="form-select form-select-sm" onchange="updateStart24()" style="padding-left: 5px; padding-right: 5px; font-weight: 500;">
                  @for($i=1; $i<=12; $i++)
                    <option value="{{ sprintf('%02d', $i) }}" {{ $i == 8 ? 'selected' : '' }}>{{ $i }}</option>
                  @endfor
                </select>
                <select id="start_minute" class="form-select form-select-sm" onchange="updateStart24()" style="padding-left: 5px; padding-right: 5px; font-weight: 500;">
                  @for($i=0; $i<60; $i++)
                    <option value="{{ sprintf('%02d', $i) }}">{{ sprintf('%02d', $i) }}</option>
                  @endfor
                </select>
                <select id="start_ampm" class="form-select form-select-sm" onchange="updateStart24()" style="padding-left: 5px; padding-right: 5px; font-weight: 700; color: var(--spgi-primary);">
                  <option value="AM" selected>AM</option>
                  <option value="PM">PM</option>
                </select>
              </div>
              <input type="hidden" name="hora_inicio" id="hora_inicio" value="08:00">
            </div>
            
            <div class="col-md-2 col-sm-6">
              <label class="form-label small fw-bold">H. Salida (12h)</label>
              <div class="d-flex gap-1">
                <select id="end_hour" class="form-select form-select-sm" onchange="updateEnd24()" style="padding-left: 5px; padding-right: 5px; font-weight: 500;">
                  @for($i=1; $i<=12; $i++)
                    <option value="{{ sprintf('%02d', $i) }}" {{ $i == 5 ? 'selected' : '' }}>{{ $i }}</option>
                  @endfor
                </select>
                <select id="end_minute" class="form-select form-select-sm" onchange="updateEnd24()" style="padding-left: 5px; padding-right: 5px; font-weight: 500;">
                  @for($i=0; $i<60; $i++)
                    <option value="{{ sprintf('%02d', $i) }}">{{ sprintf('%02d', $i) }}</option>
                  @endfor
                </select>
                <select id="end_ampm" class="form-select form-select-sm" onchange="updateEnd24()" style="padding-left: 5px; padding-right: 5px; font-weight: 700; color: var(--spgi-primary);">
                  <option value="AM">AM</option>
                  <option value="PM" selected>PM</option>
                </select>
              </div>
              <input type="hidden" name="hora_salida" id="hora_salida" value="17:00">
            </div>
            
            <div class="col-md-1 col-sm-4">
              <label for="total_horas" class="form-label small fw-bold">Horas</label>
              <input type="number" step="0.01" name="total_horas" id="total_horas" class="form-control form-control-sm fw-bold" readonly required>
            </div>
            
          </div>
          
          <div class="d-flex justify-content-end mt-3">
            <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4 fw-bold shadow-sm">
              <i class="bi bi-plus-lg me-1"></i> Insertar Fila
            </button>
          </div>
        </form>
      </div>
      @endif

      <!-- Overtime Details Table -->
      <div class="table-responsive">
        <h4 class="h5 fw-bold mb-3 border-bottom pb-2">PLANILLA HORA EXTRA DE COLABORADORES</h4>
        <table class="table excel-table align-middle">
          <thead>
            <tr>
              <th style="width: 120px;">FECHA</th>
              <th>COLABORADOR</th>
              <th>CONCEPTO</th>
              <th style="width: 110px;">HORA INICIO</th>
              <th style="width: 110px;">HORA SALIDA</th>
              <th style="width: 150px;">TOTAL HORAS EXTRAS TRABAJADAS</th>
              <th style="width: 220px;">TARIFA A PAGO POR HORA (A LAPIZERO)</th>
              @if($planilla->estado == 'Borrador')
                <th style="width: 80px;">ACCIONES</th>
              @endif
            </tr>
          </thead>
          <tbody>
            @forelse($planilla->detalles as $det)
            <tr>
              <td>{{ $det->fecha->format('d/m/Y') }}</td>
              <td class="text-start fw-bold">{{ $det->colaborador }}</td>
              <td class="text-start text-muted">{{ $det->concepto }}</td>
              <td>{{ date('h:i A', strtotime($det->hora_inicio)) }}</td>
              <td>{{ date('h:i A', strtotime($det->hora_salida)) }}</td>
              <td class="fw-bold text-primary">{{ number_format($det->total_horas, 2) }}</td>
              <td>
                <span style="border-bottom: 1px dotted var(--text-muted); display: inline-block; width: 120px; height: 18px; margin-top: 5px;"></span>
              </td>
              @if($planilla->estado == 'Borrador')
                <td>
                  <form action="{{ route('horas-extras.detalles.destroy', [$planilla->id, $det->id]) }}" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Seguro que deseas eliminar esta fila?')" title="Eliminar fila">
                      <i class="bi bi-trash"></i>
                    </button>
                  </form>
                </td>
              @endif
            </tr>
            @empty
            <tr>
              <td colspan="{{ $planilla->estado == 'Borrador' ? '8' : '7' }}" class="py-4 text-center text-muted small">
                No hay filas agregadas en esta planilla. Usa el formulario superior para agregar registros.
              </td>
            </tr>
            @endforelse
          </tbody>
          
          <!-- Sum / Total Row at the bottom of the table -->
          <tfoot>
            <tr class="table-light fw-extrabold border-top">
              <td colspan="5" class="text-end fw-bold py-3 text-uppercase">Totales de la Planilla:</td>
              <td class="fw-bold text-center text-primary py-3">{{ number_format($planilla->total_horas, 2) }} hrs</td>
              <td>
                <span style="border-bottom: 1px dotted var(--text-muted); display: inline-block; width: 120px; height: 18px; margin-top: 5px;"></span>
              </td>
              @if($planilla->estado == 'Borrador')
                <td></td>
              @endif
            </tr>
          </tfoot>
        </table>
      </div>

      <!-- Signatures at the bottom, outside the table columns as requested -->
      <div class="signature-section">
        
        <div class="signature-box">
          <div class="signature-line"></div>
          <span class="signature-title">Firma del Empleado</span>
          <span class="text-muted small">Colaborador / Reportante</span>
        </div>
        
        <div class="signature-box">
          <div class="signature-line"></div>
          <span class="signature-title">Firma Gerente General</span>
          <span class="text-muted small">Aprobación General</span>
        </div>
        
        <div class="signature-box">
          <div class="signature-line"></div>
          <span class="signature-title">Firma Aprobatoria</span>
          <span class="text-muted small">Supervisor / Administrador</span>
          <span class="small fw-semibold mt-1">
            @if($planilla->responsable)
              {{ $planilla->responsable->name }} ({{ $planilla->updated_at->format('d/m/Y') }})
            @else
              <span class="text-danger">Pendiente de Aprobación</span>
            @endif
          </span>
        </div>
        
      </div>

    </div>

    <!-- Metadata & Approval Panels -->
    <div class="row g-4 animate__animated animate__fadeInUp" style="animation-delay: 0.1s;">
      
      <!-- Observations and Metadata Form -->
      <div class="col-md-6">
        <div class="card shadow-sm h-100 p-4">
          <h5 class="fw-bold mb-3 text-secondary"><i class="bi bi-info-circle-fill me-2"></i>Notas y Observaciones</h5>
          <form action="{{ route('horas-extras.general.update', $planilla->id) }}" method="POST">
            @csrf @method('PUT')
            
            <div class="mb-3">
              <label for="observaciones" class="form-label small fw-bold">Observaciones Generales</label>
              <textarea name="observaciones" id="observaciones" class="form-control" rows="3" placeholder="Ingresa comentarios o especificaciones adicionales sobre estas horas extras..." {{ $planilla->estado == 'Aprobado' ? 'readonly' : '' }}>{{ $planilla->observaciones }}</textarea>
            </div>
            
            @if($planilla->estado == 'Borrador')
              <button type="submit" class="btn btn-outline-success btn-sm rounded-pill px-4 fw-bold mt-2">
                <i class="bi bi-save me-1"></i> Guardar Comentarios
              </button>
            @endif
          </form>
        </div>
      </div>

      <!-- Action Panel: Approve & Lock (Only Admin or Supervisors) -->
      <div class="col-md-6">
        <div class="card shadow-sm h-100 p-4 d-flex flex-column justify-content-between">
          <div>
            <h5 class="fw-bold mb-3 text-secondary"><i class="bi bi-shield-lock-fill me-2"></i>Control de Aprobación</h5>
            <p class="text-muted small mb-4">
              Una vez que la planilla haya sido completada con todos los detalles correspondientes, el supervisor puede dar clic en aprobar. Esto bloqueará la edición de registros para auditoría e incluirá la firma aprobatoria oficial.
            </p>
          </div>
          
          <div class="d-flex flex-column gap-2">
            @if($planilla->estado == 'Borrador')
              @if(Auth::user()->es_admin || Auth::user()->es_encargado)
                <!-- Trigger modal button instead of direct form submission -->
                <button type="button" class="btn btn-success rounded-pill py-2.5 fw-bold w-100 shadow-sm" data-bs-toggle="modal" data-bs-target="#aprobarPlanillaModal">
                  <i class="bi bi-check-circle-fill me-2"></i> Aprobar y Cerrar Planilla
                </button>

                <!-- Modal de Aprobación -->
                <div class="modal fade" id="aprobarPlanillaModal" tabindex="-1" aria-labelledby="aprobarPlanillaModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 rounded-4 shadow-lg" style="background: var(--bg-surface); border: 1px solid var(--border-main) !important;">
                      <div class="modal-header border-0 pb-0 justify-content-end">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter: var(--glass-icon-filter);"></button>
                      </div>
                      <div class="modal-body text-center px-4 pb-4">
                        <div class="mb-3 text-success animate__animated animate__heartBeat">
                          <i class="bi bi-shield-check" style="font-size: 4.5rem; line-height: 1;"></i>
                        </div>
                        <h3 class="modal-title fw-extrabold mb-2 text-gradient" id="aprobarPlanillaModalLabel" style="font-size: 1.5rem;">¿Aprobar y Cerrar Planilla?</h3>
                        <p class="text-muted mb-4 px-2" style="font-size: 0.88rem; line-height: 1.5;">
                          Al aprobar, la planilla se archivará de forma permanente y se aplicará la firma digital del supervisor. No se podrán realizar modificaciones posteriores ni agregar más registros.
                        </p>
                        
                        <form action="{{ route('horas-extras.aprobar', $planilla->id) }}" method="POST">
                          @csrf
                          <div class="d-flex gap-3 justify-content-center">
                            <button type="button" class="btn btn-outline-secondary rounded-pill px-4 fw-bold" data-bs-dismiss="modal">
                              Cancelar
                            </button>
                            <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm">
                              Sí, Aprobar y Cerrar
                            </button>
                          </div>
                        </form>
                      </div>
                    </div>
                  </div>
                </div>
              @else
                <button class="btn btn-secondary rounded-pill py-2.5 fw-bold w-100" disabled>
                  <i class="bi bi-lock-fill me-2"></i> Solo Supervisores pueden Aprobar
                </button>
              @endif
            @else
              <div class="alert alert-success border-0 rounded-4 text-center py-3 mb-0">
                <i class="bi bi-check-circle-fill display-6 d-block mb-2 text-success"></i>
                <span class="fw-extrabold text-uppercase">Planilla Aprobada y Bloqueada</span>
              </div>
            @endif
          </div>
        </div>
      </div>
      
    </div>

  </div>
</div>

<script>
  // Helper to convert 12-hour values to 24-hour HH:MM format for the hidden inputs
  function convertTo24Hour(hour, minute, ampm) {
    let h = parseInt(hour, 10);
    const m = minute;
    if (ampm === 'PM' && h < 12) {
      h += 12;
    } else if (ampm === 'AM' && h === 12) {
      h = 0;
    }
    const hStr = String(h).padStart(2, '0');
    return `${hStr}:${m}`;
  }

  function updateStart24() {
    const hour = document.getElementById('start_hour').value;
    const minute = document.getElementById('start_minute').value;
    const ampm = document.getElementById('start_ampm').value;
    const val = convertTo24Hour(hour, minute, ampm);
    document.getElementById('hora_inicio').value = val;
    calculateHours();
  }

  function updateEnd24() {
    const hour = document.getElementById('end_hour').value;
    const minute = document.getElementById('end_minute').value;
    const ampm = document.getElementById('end_ampm').value;
    const val = convertTo24Hour(hour, minute, ampm);
    document.getElementById('hora_salida').value = val;
    calculateHours();
  }

  // Dynamic JavaScript Overtime Calculation
  function calculateHours() {
    const start = document.getElementById('hora_inicio').value;
    const end = document.getElementById('hora_salida').value;
    
    if (start && end) {
      let [startHours, startMinutes] = start.split(':').map(Number);
      let [endHours, endMinutes] = end.split(':').map(Number);
      
      let startTotal = startHours * 60 + startMinutes;
      let endTotal = endHours * 60 + endMinutes;
      
      // If the end time is less than start time, assume shift crosses midnight
      if (endTotal < startTotal) {
        endTotal += 24 * 60;
      }
      
      let diffMinutes = endTotal - startTotal;
      let diffHours = diffMinutes / 60;
      
      document.getElementById('total_horas').value = diffHours.toFixed(2);
    }
  }

  // Set default hours and run calculations once page is loaded
  document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('start_hour')) {
      updateStart24();
      updateEnd24();
    }
  });
</script>
@endsection
