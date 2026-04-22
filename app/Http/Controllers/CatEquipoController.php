<?php

namespace App\Http\Controllers;

use App\Models\CatEquipo;
use App\Models\CatTipoEquipo;
use Illuminate\Http\Request;

class CatEquipoController extends Controller
{
    public function index()
    {
        $equipos = CatEquipo::with('tipoEquipo')->orderBy('nombre')->get();
        $tipos = CatTipoEquipo::where('activo', true)->orderBy('nombre')->get();
        return view('mantenimiento.equipos.index', compact('equipos', 'tipos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'         => 'required',
            'tipo_equipo_id' => 'required|exists:cat_tipos_equipo,id',
            'driver_file'    => 'nullable|file|max:5242880',
        ]);

        $data = $request->all();
        $tipo = CatTipoEquipo::find($request->tipo_equipo_id);
        $data['tipo'] = $tipo->nombre;

        $driverDocId = null;
        if ($request->hasFile('driver_file')) {
            $file = $request->file('driver_file');
            $fileName = time() . '_driver_' . $file->getClientOriginalName();
            $basePath = 'Wiki/Manuales';

            if (!\Storage::disk('ftp')->exists($basePath)) {
                \Storage::disk('ftp')->makeDirectory($basePath);
            }

            if ($file->storeAs($basePath, $fileName, 'ftp')) {
                $doc = \App\Models\WikiDocument::create([
                    'user_id'     => auth()->id(),
                    'title'       => 'Driver - ' . $request->nombre,
                    'description' => 'Driver universal desde el catálogo de equipos.',
                    'categoria'   => 'Manual',
                    'file_path'   => $basePath . '/' . $fileName,
                    'estado'      => 'Validado'
                ]);
                $driverDocId = $doc->id;
            }
        }

        $data['driver_doc_id'] = $driverDocId;
        CatEquipo::create($data);

        return back()->with('success', 'Equipo creado correctamente.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre'         => 'required',
            'tipo_equipo_id' => 'required|exists:cat_tipos_equipo,id',
            'driver_file'    => 'nullable|file|max:5242880',
        ]);

        $equipo = CatEquipo::findOrFail($id);
        $data = $request->all();
        $tipo = CatTipoEquipo::find($request->tipo_equipo_id);
        $data['tipo'] = $tipo->nombre;
        
        if ($request->hasFile('driver_file')) {
            $file = $request->file('driver_file');
            $fileName = time() . '_driver_' . $file->getClientOriginalName();
            $basePath = 'Wiki/Manuales';

            if (!\Storage::disk('ftp')->exists($basePath)) {
                \Storage::disk('ftp')->makeDirectory($basePath);
            }

            if ($file->storeAs($basePath, $fileName, 'ftp')) {
                // Borrar anterior si existe
                if ($equipo->driverDoc && \Storage::disk('ftp')->exists($equipo->driverDoc->file_path)) {
                    \Storage::disk('ftp')->delete($equipo->driverDoc->file_path);
                    $equipo->driverDoc->update([
                        'file_path' => $basePath . '/' . $fileName,
                        'title' => 'Driver - ' . $request->nombre
                    ]);
                    $data['driver_doc_id'] = $equipo->driver_doc_id;
                } else {
                    $doc = \App\Models\WikiDocument::create([
                        'user_id'     => auth()->id(),
                        'title'       => 'Driver - ' . $request->nombre,
                        'description' => 'Driver universal desde el catálogo de equipos.',
                        'categoria'   => 'Manual',
                        'file_path'   => $basePath . '/' . $fileName,
                        'estado'      => 'Validado'
                    ]);
                    $data['driver_doc_id'] = $doc->id;
                }
            }
        }
        
        $equipo->update($data);

        return back()->with('success', 'Equipo actualizado.');
    }

    public function destroy($id)
    {
        $equipo = CatEquipo::findOrFail($id);
        $equipo->delete();

        return back()->with('success', 'Equipo eliminado.');
    }
}
