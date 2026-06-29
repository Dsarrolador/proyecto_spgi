<?php

namespace App\Http\Controllers;

use App\Models\ClienteMaestro;
use App\Models\ClienteDocumentoAdmin;
use App\Models\ClienteNovedadAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ClienteBitacoraAdminController extends Controller
{
    private function esAdminOEncargado()
    {
        $u = Auth::user();
        return $u && ($u->es_admin || $u->es_encargado);
    }

    public function index(Request $request)
    {
        if (!$this->esAdminOEncargado()) {
            return redirect()->route('bienvenido')->with('error', 'Acceso denegado.');
        }

        $query = ClienteMaestro::query();

        if ($request->filled('search')) {
            $query->where('nombre', 'like', '%' . $request->search . '%')
                  ->orWhere('rnc', 'like', '%' . $request->search . '%');
        }

        $clientes = $query->orderBy('nombre')->paginate(15);

        return view('administracion.bitacora_clientes.index', compact('clientes'));
    }

    public function show($clienteId)
    {
        if (!$this->esAdminOEncargado()) {
            return redirect()->route('bienvenido')->with('error', 'Acceso denegado.');
        }

        $cliente = ClienteMaestro::findOrFail($clienteId);

        $documentos = ClienteDocumentoAdmin::where('cliente_id', $clienteId)
            ->orderBy('id', 'desc')
            ->get();

        $contactos = ClienteNovedadAdmin::with('user')
            ->where('cliente_id', $clienteId)
            ->orderBy('fecha', 'desc')
            ->get();

        return view('administracion.bitacora_clientes.show', compact('cliente', 'documentos', 'contactos'));
    }

    public function storeDocumento(Request $request, $clienteId)
    {
        if (!$this->esAdminOEncargado()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $request->validate([
            'nombre' => 'required|string|max:255',
            'archivo' => 'required|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,png,zip',
        ]);

        $cliente = ClienteMaestro::findOrFail($clienteId);

        if ($request->hasFile('archivo')) {
            $file = $request->file('archivo');
            $originalName = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('contratos/' . $clienteId, $originalName, 'public');

            ClienteDocumentoAdmin::create([
                'cliente_id' => $clienteId,
                'nombre' => $request->nombre,
                'archivo_path' => $path,
            ]);
        }

        return redirect()->route('administracion.bitacora-clientes.show', $clienteId)
            ->with('success', 'Documento/Contrato guardado correctamente.');
    }

    public function destroyDocumento($clienteId, $id)
    {
        if (!$this->esAdminOEncargado()) {
            return redirect()->route('bienvenido')->with('error', 'Acceso denegado.');
        }

        $doc = ClienteDocumentoAdmin::findOrFail($id);

        if ($doc->archivo_path && Storage::disk('public')->exists($doc->archivo_path)) {
            Storage::disk('public')->delete($doc->archivo_path);
        }

        $doc->delete();

        return redirect()->route('administracion.bitacora-clientes.show', $clienteId)
            ->with('success', 'Documento/Contrato eliminado correctamente.');
    }

    public function downloadDocumento($id)
    {
        if (!$this->esAdminOEncargado()) {
            abort(403);
        }

        $doc = ClienteDocumentoAdmin::findOrFail($id);

        if (!$doc->archivo_path || !Storage::disk('public')->exists($doc->archivo_path)) {
            return back()->with('error', 'El archivo no existe en el servidor.');
        }

        return Storage::disk('public')->download($doc->archivo_path);
    }

    public function storeContacto(Request $request, $clienteId)
    {
        if (!$this->esAdminOEncargado()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $request->validate([
            'fecha' => 'required|date',
            'medio' => 'required|string|max:100',
            'detalle' => 'required|string',
        ]);

        ClienteNovedadAdmin::create([
            'cliente_id' => $clienteId,
            'user_id' => Auth::id(),
            'fecha' => $request->fecha,
            'medio' => $request->medio,
            'detalle' => $request->detalle,
        ]);

        return redirect()->route('administracion.bitacora-clientes.show', $clienteId)
            ->with('success', 'Contacto registrado en bitácora correctamente.');
    }

    public function destroyContacto($clienteId, $id)
    {
        if (!$this->esAdminOEncargado()) {
            return redirect()->route('bienvenido')->with('error', 'Acceso denegado.');
        }

        $contacto = ClienteNovedadAdmin::findOrFail($id);
        $contacto->delete();

        return redirect()->route('administracion.bitacora-clientes.show', $clienteId)
            ->with('success', 'Interacción eliminada correctamente.');
    }
}
