<?php

namespace App\Http\Controllers;

use App\Models\ClienteMaestro;
use App\Models\CatEquipo;
use App\Models\ClienteAnydesk;
use App\Models\ClienteBitacora;
use App\Models\ClienteEntornoDocumento;
use App\Models\ClienteEquipo;
use App\Models\RequerimientoCliente;
use App\Models\User;
use App\Models\NotificacionSistema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ClienteEntornoController extends Controller
{
    private function notifyAdmins($clienteId, $titulo, $mensaje)
    {
        $admins = User::where('cod_roleUser', 1)->get();
        $senderId = auth()->id();
        $url = route('clientes.entorno.show', $clienteId);

        foreach ($admins as $admin) {
            NotificacionSistema::create([
                'user_id' => $admin->id,
                'sender_id' => $senderId,
                'titulo' => $titulo,
                'mensaje' => $mensaje,
                'url' => $url,
            ]);
        }
    }

    public function show($clienteId)
    {
        $cliente = ClienteMaestro::findOrFail($clienteId);

        // Paginación independiente para cada sección
        $documentos = ClienteEntornoDocumento::where('cliente_id', $clienteId)
            ->orderBy('id', 'desc')
            ->paginate(10, ['*'], 'page_docs');

        $equipos = ClienteEquipo::with('catalogo')
            ->where('cliente_id', $clienteId)
            ->orderBy('id', 'desc')
            ->paginate(10, ['*'], 'page_equipos');

        $anydesks = ClienteAnydesk::where('cliente_id', $clienteId)
            ->orderBy('id', 'desc')
            ->paginate(10, ['*'], 'page_anydesk');

        $bitacoras = ClienteBitacora::with('user')
            ->where('cliente_id', $clienteId)
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'page_bitacora');

        $catalogoEquipos = CatEquipo::where('activo', true)->orderBy('nombre')->get();
        
        // Link para histórico de requerimientos
        $historicoUrl = route('requerimientos.index', ['cliente_id' => $cliente->id]);

        return view('clientes.entorno.index', compact(
            'cliente', 
            'catalogoEquipos', 
            'historicoUrl',
            'documentos',
            'equipos',
            'anydesks',
            'bitacoras'
        ));
    }

    // --- ANYDESK ---
    public function storeAnydesk(Request $request, $clienteId)
    {
        $request->validate(['anydesk_id' => 'required']);
        $ad = ClienteAnydesk::create([
            'cliente_id' => $clienteId,
            'anydesk_id' => $request->anydesk_id,
            'alias' => $request->alias,
            'notas' => $request->notas,
        ]);

        $cliente = ClienteMaestro::find($clienteId);
        $this->notifyAdmins($clienteId, 'Entorno: AnyDesk agregado', auth()->user()->name . " agregó un ID de AnyDesk al cliente " . ($cliente->nombre ?? ''));

        return redirect()->to(route('clientes.entorno.show', $clienteId) . '#anydesk')->with('success', 'ID de AnyDesk agregado.');
    }
    public function updateAnydesk(Request $request, $clienteId, $id)
    {
        $request->validate(['anydesk_id' => 'required']);
        $ad = ClienteAnydesk::findOrFail($id);
        
        $ad->update([
            'anydesk_id' => $request->anydesk_id,
            'alias' => $request->alias,
            'notas' => $request->notas,
        ]);

        $cliente = ClienteMaestro::find($clienteId);
        $this->notifyAdmins($clienteId, 'Entorno: AnyDesk actualizado', auth()->user()->name . " actualizó un ID de AnyDesk del cliente " . ($cliente->nombre ?? ''));

        return redirect()->to(route('clientes.entorno.show', $clienteId) . '#anydesk')->with('success', 'ID de AnyDesk actualizado.');
    }

    public function destroyAnydesk($clienteId, $id)
    {
        $ad = ClienteAnydesk::findOrFail($id);
        $cliente = ClienteMaestro::find($clienteId);
        
        $this->notifyAdmins($clienteId, 'Entorno: AnyDesk eliminado', auth()->user()->name . " eliminó un ID de AnyDesk del cliente " . ($cliente->nombre ?? ''));
        
        $ad->delete();
        return redirect()->to(route('clientes.entorno.show', $clienteId) . '#anydesk')->with('success', 'ID de AnyDesk eliminado.');
    }

    // --- BITACORA ---
    public function storeBitacora(Request $request, $clienteId)
    {
        $request->validate(['nota' => 'required']);
        ClienteBitacora::create([
            'cliente_id' => $clienteId,
            'user_id' => auth()->id(),
            'nota' => $request->nota,
        ]);

        $cliente = ClienteMaestro::find($clienteId);
        $this->notifyAdmins($clienteId, 'Entorno: Nueva Nota', auth()->user()->name . " agregó una nota a la bitácora del cliente " . ($cliente->nombre ?? ''));

        return redirect()->to(route('clientes.entorno.show', $clienteId) . '#bitacora')->with('success', 'Nota agregada a la bitácora.');
    }

    // --- DOCUMENTOS ---
    public function storeDocumento(Request $request, $clienteId)
    {
        $request->validate([
            'tipo' => 'required',
            'nombre' => 'required',
            'archivo' => 'nullable|file|max:20480',
        ]);

        $data = $request->only(['tipo', 'nombre', 'url', 'usuario', 'clave']);
        $data['cliente_id'] = $clienteId;

        if ($request->hasFile('archivo')) {
            $file = $request->file('archivo');
            $originalName = $file->getClientOriginalName();
            $path = $file->storeAs('entorno/' . $clienteId, $originalName, 'ftp');
            $data['archivo_path'] = $path;
        }

        ClienteEntornoDocumento::create($data);

        $cliente = ClienteMaestro::find($clienteId);
        $this->notifyAdmins($clienteId, 'Entorno: Documento/Clave agregado', auth()->user()->name . " agregó registro de " . $request->tipo . " para el cliente " . ($cliente->nombre ?? ''));

        return redirect()->to(route('clientes.entorno.show', $clienteId) . '#docs')->with('success', 'Registro/Documento guardado.');
    }

    public function downloadDocumento($clienteId, $id)
    {
        $doc = ClienteEntornoDocumento::findOrFail($id);

        if (!$doc->archivo_path || !Storage::disk('ftp')->exists($doc->archivo_path)) {
            return back()->with('error', 'El archivo no existe físicamente en el servidor.');
        }

        $extension = pathinfo($doc->archivo_path, PATHINFO_EXTENSION);
        $downloadName = str_replace(' ', '_', $doc->nombre) . '.' . $extension;

        return Storage::disk('ftp')->download($doc->archivo_path, $downloadName);
    }

    public function destroyDocumento($clienteId, $id)
    {
        // Restauramos funcionalidad a solicitud de usuario pero notificamos
        $doc = ClienteEntornoDocumento::findOrFail($id);
        $cliente = ClienteMaestro::find($clienteId);

        $this->notifyAdmins($clienteId, 'Entorno: Registro eliminado', auth()->user()->name . " eliminó un documento/clave del cliente " . ($cliente->nombre ?? ''));

        if ($doc->archivo_path) {
            Storage::disk('ftp')->delete($doc->archivo_path);
        }
        $doc->delete();
        
        return redirect()->to(route('clientes.entorno.show', $clienteId) . '#docs')->with('success', 'Registro eliminado correctamente.');
    }

    // --- EQUIPOS (INVENTARIO) ---
    public function storeEquipo(Request $request, $clienteId)
    {
        $request->validate(['cat_equipo_id' => 'required']);
        $inv = ClienteEquipo::create([
            'cliente_id' => $clienteId,
            'cat_equipo_id' => $request->cat_equipo_id,
            'serie' => $request->serie,
            'configuracion_especifica' => $request->configuracion_especifica,
            'notas' => $request->notas,
        ]);

        $cliente = ClienteMaestro::find($clienteId);
        $this->notifyAdmins($clienteId, 'Entorno: Equipo asignado', auth()->user()->name . " asignó equipo de inventario al cliente " . ($cliente->nombre ?? ''));

        return redirect()->to(route('clientes.entorno.show', $clienteId) . '#equipos')->with('success', 'Equipo asignado al cliente.');
    }

    public function destroyEquipo($clienteId, $id)
    {
        $inv = ClienteEquipo::findOrFail($id);
        $cliente = ClienteMaestro::find($clienteId);
        
        $this->notifyAdmins($clienteId, 'Entorno: Equipo removido', auth()->user()->name . " removió un equipo del inventario del cliente " . ($cliente->nombre ?? ''));
        
        $inv->delete();
        return redirect()->to(route('clientes.entorno.show', $clienteId) . '#equipos')->with('success', 'Equipo removido del inventario del cliente.');
    }
}
