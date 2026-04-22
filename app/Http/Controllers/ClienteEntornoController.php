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

        $equipos = ClienteEquipo::with(['catalogo', 'peripherals.catalogo', 'wikiDocument'])
            ->where('cliente_id', $clienteId)
            ->whereNull('parent_id')
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
        $equiposPadre = ClienteEquipo::where('cliente_id', $clienteId)->whereNull('parent_id')->get();
        $equiposInventario = ClienteEquipo::where('cliente_id', $clienteId)->get();
        
        // Link para histórico de requerimientos
        $historicoUrl = route('requerimientos.index', ['cliente_id' => $cliente->id]);

        return view('clientes.entorno.index', compact(
            'cliente', 
            'catalogoEquipos', 
            'historicoUrl',
            'documentos',
            'equipos',
            'equiposPadre',
            'equiposInventario',
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

    public function updateBitacora(Request $request, $clienteId, $id)
    {
        $request->validate(['nota' => 'required']);
        $nota = ClienteBitacora::findOrFail($id);
        $nota->update(['nota' => $request->nota]);

        return redirect()->to(route('clientes.entorno.show', $clienteId) . '#bitacora')->with('success', 'Nota actualizada.');
    }

    public function destroyBitacora($clienteId, $id)
    {
        $nota = ClienteBitacora::findOrFail($id);
        $nota->delete();
        return redirect()->to(route('clientes.entorno.show', $clienteId) . '#bitacora')->with('success', 'Nota eliminada.');
    }

    // --- DOCUMENTOS ---
    public function storeDocumento(Request $request, $clienteId)
    {
        $request->validate([
            'tipo' => 'required',
            'nombre' => 'required',
            'archivo' => 'nullable|file|max:5242880',
        ]);

        $data = $request->only(['tipo', 'nombre', 'url', 'usuario', 'clave']);
        $data['cliente_id'] = $clienteId;

        if ($request->hasFile('archivo')) {
            $file = $request->file('archivo');
            $originalName = $file->getClientOriginalName();
            $path = $file->storeAs('entorno/' . $clienteId, $originalName, 'public');
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

        if (!$doc->archivo_path || !Storage::disk('public')->exists($doc->archivo_path)) {
            return back()->with('error', 'El archivo no existe físicamente en el servidor.');
        }

        $extension = pathinfo($doc->archivo_path, PATHINFO_EXTENSION);
        $downloadName = str_replace(' ', '_', $doc->nombre) . '.' . $extension;

        return Storage::disk('public')->download($doc->archivo_path, $downloadName);
    }

    public function destroyDocumento($clienteId, $id)
    {
        // Restauramos funcionalidad a solicitud de usuario pero notificamos
        $doc = ClienteEntornoDocumento::findOrFail($id);
        $cliente = ClienteMaestro::find($clienteId);

        $this->notifyAdmins($clienteId, 'Entorno: Registro eliminado', auth()->user()->name . " eliminó un documento/clave del cliente " . ($cliente->nombre ?? ''));

        if ($doc->archivo_path) {
            Storage::disk('public')->delete($doc->archivo_path);
        }
        $doc->delete();
        
        return redirect()->to(route('clientes.entorno.show', $clienteId) . '#docs')->with('success', 'Registro eliminado correctamente.');
    }

    // --- EQUIPOS (INVENTARIO) ---
    public function storeEquipo(Request $request, $clienteId)
    {
        $validator = \Validator::make($request->all(), [
            'cat_equipo_id' => 'required_unless:wizard_mode,software,recurso',
            'sistema_file'  => 'nullable|file|max:5242880',
            'driver_file'   => 'nullable|file|max:5242880',
            'sistema_extra_file' => 'nullable|file|max:5242880',
        ]);

        if ($validator->fails()) {
            return redirect()->to(route('clientes.entorno.show', $clienteId) . '#equipos')
                             ->withErrors($validator)
                             ->withInput();
        }

        $wikiDocId = null;

        if ($request->hasFile('sistema_file')) {
            $file = $request->file('sistema_file');
            $originalName = $file->getClientOriginalName();
            if (strlen($originalName) > 200) {
                $ext = $file->getClientOriginalExtension();
                $originalName = substr($originalName, 0, 190) . '.' . $ext;
            }
            
            $fileName = time() . '_' . $originalName;
            $basePath = 'Wiki/Sistema';

            if (!Storage::disk('ftp')->exists($basePath)) {
                Storage::disk('ftp')->makeDirectory($basePath);
            }

            $path = $file->storeAs($basePath, $fileName, 'ftp');
            
            if ($path) {
                $doc = \App\Models\WikiDocument::create([
                    'user_id'     => auth()->id(),
                    'title'       => 'Sistema - ' . ($request->alias ?? 'Equipo asignado'),
                    'description' => 'Documento de Sistema generado desde el Inventario del Entorno.',
                    'tags'        => 'Sistema',
                    'categoria'   => 'Sistema',
                    'file_path'   => $basePath . '/' . $fileName,
                    'estado'      => 'Validado'
                ]);
                $wikiDocId = $doc->id;
            }
        }

        $driverId = null;
        if ($request->hasFile('driver_file')) {
            $file = $request->file('driver_file');
            $fileName = time() . '_driver_especifico_' . $file->getClientOriginalName();
            $basePath = 'Wiki/Manuales';
            if (!\Storage::disk('ftp')->exists($basePath)) \Storage::disk('ftp')->makeDirectory($basePath);
            if ($file->storeAs($basePath, $fileName, 'ftp')) {
                $doc = \App\Models\WikiDocument::create([
                    'user_id'   => auth()->id(),
                    'title'     => ($request->driver_nombre ?: 'Driver Específico') . ' - ' . ($request->alias ?: $request->cat_equipo_id),
                    'categoria' => 'Manual',
                    'file_path' => $basePath . '/' . $fileName,
                    'estado'    => 'Validado'
                ]);
                $driverId = $doc->id;
            }
        }

        $extraSystemId = null;
        if ($request->hasFile('sistema_extra_file')) {
            $file = $request->file('sistema_extra_file');
            $fileName = time() . '_sistema_extra_' . $file->getClientOriginalName();
            $basePath = 'Wiki/Sistema';
            if (!\Storage::disk('ftp')->exists($basePath)) \Storage::disk('ftp')->makeDirectory($basePath);
            if ($file->storeAs($basePath, $fileName, 'ftp')) {
                $doc = \App\Models\WikiDocument::create([
                    'user_id'   => auth()->id(),
                    'title'     => ($request->extra_system_nombre ?: 'Sistema Extra') . ' - ' . ($request->alias ?: $request->cat_equipo_id),
                    'categoria' => 'Sistema',
                    'file_path' => $basePath . '/' . $fileName,
                    'estado'    => 'Validado'
                ]);
                $extraSystemId = $doc->id;
            }
        }

        // Si es modo software/recurso y el parent_id está presente, tal vez queremos ACTUALIZAR el padre 
        // en lugar de crear un item nuevo. Pero para no romper la lógica de 'store', crearemos un item virtual
        // si no hay cat_equipo_id.
        
        $catId = $request->cat_equipo_id;
        if (!$catId && $request->parent_id) {
            // Intentar usar la categoría del padre para no fallar el NOT NULL
            $parent = \App\Models\ClienteEquipo::find($request->parent_id);
            $catId = $parent ? $parent->cat_equipo_id : 1; 
        }

        $equipo = \App\Models\ClienteEquipo::create([
            'cliente_id'               => $clienteId,
            'cat_equipo_id'            => $catId ?: 1,
            'parent_id'                => $request->parent_id,
            'wiki_document_id'         => $wikiDocId,
            'driver_id'                => $driverId,
            'driver_nombre'            => $request->driver_nombre,
            'extra_system_id'          => $extraSystemId,
            'extra_system_nombre'      => $request->extra_system_nombre,
            'alias'                    => $request->alias ?: ($request->wizard_mode == 'software' ? $request->extra_system_nombre : ($request->wizard_mode == 'recurso' ? $request->driver_nombre : null)),
            'serie'                    => $request->serie,
            'configuracion_especifica' => $request->configuracion_especifica,
            'notas'                    => $request->notas,
        ]);

        $cliente = ClienteMaestro::find($clienteId);
        if (!$request->parent_id) {
            $this->notifyAdmins($clienteId, 'Entorno: Equipo raíz asignado', auth()->user()->name . " instaló un equipo principal en el cliente " . ($cliente->nombre ?? ''));
        }

        return redirect()->to(route('clientes.entorno.show', $clienteId) . '#equipos')->with('success', 'Equipo registrado con éxito en el inventario.');
    }

    public function updateEquipo(Request $request, $clienteId, $id)
    {
        $validator = \Validator::make($request->all(), [
            'serie'                   => 'nullable|string|max:255',
            'configuracion_especifica'=> 'nullable|string',
            'sistema_file'            => 'nullable|file|max:5242880',
            'driver_file'             => 'nullable|file|max:5242880',
            'sistema_extra_file'      => 'nullable|file|max:5242880',
        ]);

        if ($validator->fails()) {
            return redirect()->to(route('clientes.entorno.show', $clienteId) . '#equipos')
                             ->withErrors($validator)
                             ->withInput();
        }

        $inv = ClienteEquipo::findOrFail($id);
        $wikiDocId = $inv->wiki_document_id;
        $driverId = $inv->driver_id;
        $extraSystemId = $inv->extra_system_id;

        if ($request->hasFile('sistema_file')) {
            $file = $request->file('sistema_file');
            $fileName = time() . '_sistema_' . $file->getClientOriginalName();
            $basePath = 'Wiki/Sistema';
            if (!Storage::disk('ftp')->exists($basePath)) Storage::disk('ftp')->makeDirectory($basePath);
            $path = $file->storeAs($basePath, $fileName, 'ftp');
            
            if ($path) {
                if ($wikiDocId) {
                    $wikiDoc = \App\Models\WikiDocument::find($wikiDocId);
                    if ($wikiDoc) {
                        if ($wikiDoc->file_path && Storage::disk('ftp')->exists($wikiDoc->file_path)) Storage::disk('ftp')->delete($wikiDoc->file_path);
                        $wikiDoc->update(['file_path' => $basePath . '/' . $fileName]);
                    }
                } else {
                    $doc = \App\Models\WikiDocument::create([
                        'user_id' => auth()->id(),
                        'title' => 'Sistema - ' . ($request->alias ?? $inv->alias ?? 'Equipo'),
                        'categoria' => 'Sistema',
                        'file_path' => $basePath . '/' . $fileName,
                        'estado' => 'Validado'
                    ]);
                    $wikiDocId = $doc->id;
                }
            }
        }

        if ($request->hasFile('driver_file')) {
            $file = $request->file('driver_file');
            $fileName = time() . '_driver_especifico_' . $file->getClientOriginalName();
            $basePath = 'Wiki/Manuales';
            if (!Storage::disk('ftp')->exists($basePath)) Storage::disk('ftp')->makeDirectory($basePath);
            $path = $file->storeAs($basePath, $fileName, 'ftp');
            if ($path) {
                if ($driverId) {
                    $doc = \App\Models\WikiDocument::find($driverId);
                    if ($doc) {
                        if ($doc->file_path && Storage::disk('ftp')->exists($doc->file_path)) Storage::disk('ftp')->delete($doc->file_path);
                        $doc->update(['file_path' => $basePath . '/' . $fileName]);
                    }
                } else {
                    $doc = \App\Models\WikiDocument::create([
                        'user_id'   => auth()->id(),
                        'title'     => ($request->driver_nombre ?: 'Driver Específico') . ' - ' . ($request->alias ?? $inv->alias ?? 'Equipo'),
                        'categoria' => 'Manual',
                        'file_path' => $basePath . '/' . $fileName,
                        'estado'    => 'Validado'
                    ]);
                    $driverId = $doc->id;
                }
            }
        }

        if ($request->hasFile('sistema_extra_file')) {
            $file = $request->file('sistema_extra_file');
            $fileName = time() . '_sistema_extra_' . $file->getClientOriginalName();
            $basePath = 'Wiki/Sistema';
            if (!Storage::disk('ftp')->exists($basePath)) Storage::disk('ftp')->makeDirectory($basePath);
            $path = $file->storeAs($basePath, $fileName, 'ftp');
            if ($path) {
                if ($extraSystemId) {
                    $doc = \App\Models\WikiDocument::find($extraSystemId);
                    if ($doc) {
                        if ($doc->file_path && Storage::disk('ftp')->exists($doc->file_path)) Storage::disk('ftp')->delete($doc->file_path);
                        $doc->update(['file_path' => $basePath . '/' . $fileName]);
                    }
                } else {
                    $doc = \App\Models\WikiDocument::create([
                        'user_id'   => auth()->id(),
                        'title'     => ($request->extra_system_nombre ?: 'Sistema Extra') . ' - ' . ($request->alias ?? $inv->alias ?? 'Equipo'),
                        'categoria' => 'Sistema',
                        'file_path' => $basePath . '/' . $fileName,
                        'estado'    => 'Validado'
                    ]);
                    $extraSystemId = $doc->id;
                }
            }
        }

        $inv->update([
            'alias'                    => $request->alias ?? $inv->alias,
            'serie'                    => $request->serie,
            'wiki_document_id'         => $wikiDocId,
            'driver_id'                => $driverId,
            'driver_nombre'            => $request->driver_nombre ?? $inv->driver_nombre,
            'extra_system_id'          => $extraSystemId,
            'extra_system_nombre'      => $request->extra_system_nombre ?? $inv->extra_system_nombre,
            'configuracion_especifica' => $request->configuracion_especifica,
            'notas'                    => $request->notas,
        ]);

        $cliente = ClienteMaestro::find($clienteId);
        $this->notifyAdmins($clienteId, 'Entorno: Equipo actualizado', auth()->user()->name . " actualizó un equipo del inventario del cliente " . ($cliente->nombre ?? ''));

        return redirect()->to(route('clientes.entorno.show', $clienteId) . '#equipos')->with('success', 'Equipo actualizado correctamente.');
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
