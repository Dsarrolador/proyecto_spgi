@extends('layouts.app')

@section('page_title', 'Facturación de Requerimientos')

@section('content')

<style>
  .spgi-bg{ padding: 12px 0 24px 0; }

  .spgi-toolbar{
    background: var(--bg-surface-glass); border: 1px solid var(--border-main);
    border-radius: 20px; box-shadow: var(--shadow-main); backdrop-filter: blur(16px);
    padding: 24px; margin-bottom: 24px;
    display: flex; justify-content: space-between; align-items: center;
  }

  .toolbar-filter{ display:flex; gap:12px; align-items:center; }
  .filter-btn{
    padding: 8px 20px; border-radius: 12px; border: 1px solid var(--border-main);
    background: var(--bg-surface); color: var(--text-main); text-decoration: none;
    font-weight: 700; transition: all 0.3s;
  }
  .filter-btn.active{
    background: var(--spgi-primary); color: #fff; border-color: var(--spgi-primary);
    box-shadow: 0 4px 12px var(--spgi-primary-glow);
  }

  .spgi-table-box{
    background: var(--bg-surface-glass); border: 1px solid var(--border-main);
    border-radius: 22px; box-shadow: var(--shadow-main); overflow-x: auto; backdrop-filter: blur(16px);
  }

  .spgi-table{ margin-bottom: 0; width: 100%; }
  .spgi-table thead th{
    background: #0b1220; color: #fff; text-align:center;
    border-color: rgba(255,255,255,0.08) !important;
    font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; padding: 16px;
  }

  .spgi-table tbody td{ 
    border-color: var(--border-main) !important; 
    color: var(--text-main); 
    padding: 16px; 
    vertical-align: middle;
  }

  .btn-action{
    width: 42px; height: 42px; border-radius: 12px; display: inline-flex;
    align-items: center; justify-content: center; transition: all 0.2s;
    border: 1px solid transparent;
  }
  .btn-facturar{ background: rgba(16, 185, 129, 0.1); color: #10b981; border-color: rgba(16, 185, 129, 0.2); }
  .btn-facturar:hover{ background: #10b981; color: #fff; }
  
  .btn-upload{ background: rgba(var(--spgi-primary), 0.1); color: var(--spgi-primary); border-color: rgba(var(--spgi-primary), 0.2); }
  .btn-upload:hover{ background: var(--spgi-primary); color: #fff; }

  .status-badge{
    padding: 8px 16px; border-radius: 12px; font-weight: 900; font-size: 0.65rem;
    text-transform: uppercase; letter-spacing: 1.2px; display: inline-flex; 
    align-items: center; justify-content: center; min-width: 130px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1); border: 1px solid transparent;
    transition: all 0.3s ease;
  }
  .status-no-facturado{ 
    background: rgba(245, 158, 11, 0.15); color: #fbbf24; border-color: rgba(245, 158, 11, 0.3);
    box-shadow: 0 4px 15px rgba(245, 158, 11, 0.1);
  }
  .status-facturado{ 
    background: rgba(16, 185, 129, 0.15); color: #34d399; border-color: rgba(16, 185, 129, 0.3);
    box-shadow: 0 4px 15px rgba(16, 185, 129, 0.1);
  }

  /* Modal Billing Interface */
  .billing-split{ display: flex; min-height: 500px; }
  .billing-preview{ 
    flex: 1.2; background: #525659; display: flex; flex-direction: column; 
    align-items: center; justify-content: center; position: relative;
    border-right: 1px solid var(--border-main);
  }
  .billing-form-side{ flex: 0.8; padding: 30px; background: var(--bg-surface); }
  
  .pdf-placeholder{ color: #fff; text-align: center; opacity: 0.6; }
  .pdf-placeholder i{ font-size: 4rem; display: block; margin-bottom: 10px; }

  @media (max-width: 991px) {
    .billing-split{ flex-direction: column; }
    .billing-preview{ min-height: 300px; }
  }
</style>

<div class="spgi-bg">
  <div class="container">
    <div class="mb-4 d-flex justify-content-between align-items-end">
      <div>
        <h3 class="fw-bold text-gradient">Apartado de Facturación</h3>
        <p class="text-muted small mb-0">Gestión de cobros y comprobantes de requerimientos.</p>
      </div>
      <div class="toolbar-filter">
        <a href="{{ route('requerimientos.facturacion', ['filtro' => 'no_facturados']) }}" 
           class="filter-btn {{ $filtro === 'no_facturados' ? 'active' : '' }}">
          No Facturados
        </a>
        <a href="{{ route('requerimientos.facturacion', ['filtro' => 'facturados']) }}" 
           class="filter-btn {{ $filtro === 'facturados' ? 'active' : '' }}">
          Facturados
        </a>
      </div>
    </div>

    <div class="spgi-table-box">
      <table class="spgi-table table align-middle">
        <thead>
          <tr>
            <th style="width: 250px;">Cliente / Proyecto</th>
            <th>Descripción del Requerimiento</th>
            <th style="width: 150px;">Fecha</th>
            <th style="width: 150px;">Estado</th>
            <th style="width: 180px;">Acciones de Factura</th>
          </tr>
        </thead>
        <tbody>
          @forelse($requerimientos as $req)
            <tr>
              <td>
                <div class="fw-bold">{{ $req->clienteRelation->nombre ?? 'Sin Cliente' }}</div>
                <div class="text-muted small">ID #{{ $req->id }}</div>
              </td>
              <td>
                <div class="text-truncate" style="max-width: 400px;" title="{{ $req->texto_imagen }}">
                  {{ $req->texto_imagen }}
                </div>
              </td>
              <td class="text-center">
                {{ $req->created_at->format('d/m/Y') }}
              </td>
              <td class="text-center">
                @if($req->facturado)
                  <span class="status-badge status-facturado">Facturado</span>
                @else
                  <span class="status-badge status-no-facturado">No Facturado</span>
                @endif
              </td>
              <td>
                <div class="d-flex justify-content-center gap-2">
                  <!-- Botón Toggle Estado -->
                  <form action="{{ route('requerimientos.toggle-facturado', $req->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-action btn-facturar" title="{{ $req->facturado ? 'Marcar como NO facturado' : 'Marcar como facturado' }}">
                      <i class="bi {{ $req->facturado ? 'bi-x-circle' : 'bi-check-circle' }}"></i>
                    </button>
                  </form>

                  <!-- Botón Modal Factura -->
                  <button type="button" 
                          class="btn-action btn-upload" 
                          onclick="openInvoiceModal({{ $req->id }}, '{{ addslashes($req->clienteRelation->nombre) }}', '{{ $req->archivo_factura ? asset('storage/' . $req->archivo_factura) : '' }}')"
                          title="Gestionar Factura PDF">
                    <i class="bi {{ $req->archivo_factura ? 'bi-file-earmark-pdf-fill' : 'bi-upload' }}"></i>
                  </button>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="text-center p-5 text-muted">
                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                No se encontraron requerimientos en esta categoría.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-4 d-flex justify-content-center">
      {{ $requerimientos->links() }}
    </div>
  </div>
</div>

<!-- MODAL DE FACTURACIÓN -->
<div class="modal fade" id="modalFactura" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content glass-card-premium border-0 overflow-hidden">
      
      <div class="modal-header border-0 p-4" style="background: linear-gradient(135deg, #10b981, #059669);">
        <h5 class="modal-title text-white fw-bold mb-0" id="invoiceModalTitle">Gestionar Factura</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body p-0">
        <div class="billing-split">
          
          <!-- Lado Izquierdo: Preview -->
          <div class="billing-preview" id="pdfPreviewContainer">
            <div class="pdf-placeholder" id="pdfPlaceholder">
              <i class="bi bi-file-earmark-pdf"></i>
              <p>Sin factura adjunta</p>
            </div>
            <iframe id="pdfFrame" src="" width="100%" height="100%" style="display: none; border: none;"></iframe>
          </div>

          <!-- Lado Derecho: Formulario -->
          <div class="billing-form-side">
            <h5 class="fw-bold mb-3">Subir Factura PDF</h5>
            <p class="text-muted small mb-4">Seleccione el archivo PDF de la factura correspondiente a este requerimiento. Al subirlo, el estado cambiará automáticamente a "Facturado".</p>

            <form id="invoiceForm" action="" method="POST" enctype="multipart/form-data">
              @csrf
              <div class="mb-4">
                <label class="form-label fw-bold">Archivo PDF (Max 10MB)</label>
                <div class="input-group">
                  <input type="file" name="archivo_factura" class="form-control" accept=".pdf" required onchange="previewPDF(this)">
                </div>
              </div>

              <div class="alert alert-info border-0 rounded-4 p-3 mb-4">
                <div class="d-flex gap-3">
                  <i class="bi bi-info-circle-fill fs-4"></i>
                  <div class="small">
                    La factura será almacenada de forma segura y podrá ser consultada en cualquier momento desde este apartado.
                  </div>
                </div>
              </div>

              <button type="submit" class="btn btn-success w-100 rounded-pill py-3 fw-bold">
                <i class="bi bi-cloud-upload me-2"></i>
                Publicar Factura
              </button>
            </form>

            <div id="downloadInvoiceArea" class="mt-4 pt-4 border-top" style="display: none;">
              <a id="downloadInvoiceBtn" href="#" target="_blank" class="btn btn-outline-primary w-100 rounded-pill py-2 fw-bold">
                <i class="bi bi-download me-2"></i>
                Descargar Factura Actual
              </a>
            </div>
          </div>

        </div>
      </div>

    </div>
  </div>
</div>

<script>
  function openInvoiceModal(id, cliente, pdfUrl) {
    const modal = new bootstrap.Modal(document.getElementById('modalFactura'));
    const title = document.getElementById('invoiceModalTitle');
    const form = document.getElementById('invoiceForm');
    const pdfFrame = document.getElementById('pdfFrame');
    const pdfPlaceholder = document.getElementById('pdfPlaceholder');
    const downloadArea = document.getElementById('downloadInvoiceArea');
    const downloadBtn = document.getElementById('downloadInvoiceBtn');

    title.innerText = `Factura: ${cliente}`;
    form.action = `/requerimientos/${id}/subir-factura`;
    
    if (pdfUrl) {
      pdfFrame.src = pdfUrl;
      pdfFrame.style.display = 'block';
      pdfPlaceholder.style.display = 'none';
      downloadArea.style.display = 'block';
      downloadBtn.href = pdfUrl;
    } else {
      pdfFrame.src = '';
      pdfFrame.style.display = 'none';
      pdfPlaceholder.style.display = 'flex';
      downloadArea.style.display = 'none';
    }

    modal.show();
  }

  function previewPDF(input) {
    const pdfFrame = document.getElementById('pdfFrame');
    const pdfPlaceholder = document.getElementById('pdfPlaceholder');
    
    if (input.files && input.files[0]) {
      const reader = new FileReader();
      reader.onload = function(e) {
        pdfFrame.src = e.target.result;
        pdfFrame.style.display = 'block';
        pdfPlaceholder.style.display = 'none';
      };
      reader.readAsDataURL(input.files[0]);
    }
  }
</script>

@endsection
