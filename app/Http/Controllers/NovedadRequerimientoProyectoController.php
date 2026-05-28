<?php

namespace App\Http\Controllers;

use App\Models\NovedadRequerimientoProyecto;
use App\Models\RequerimientoProyecto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NovedadRequerimientoProyectoController extends Controller
{
    public function index($requerimientoProyectoId)
    {
        $novedades = NovedadRequerimientoProyecto::with('user')
            ->where('requerimiento_proyecto_id', $requerimientoProyectoId)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function($nov) {
                return [
                    'id' => $nov->id,
                    'user_id' => $nov->user_id,
                    'user_name' => $nov->user->name ?? 'Usuario',
                    'novedad' => $nov->novedad,
                    'created_at' => $nov->created_at->format('d/m/Y H:i'),
                    'file_url' => $nov->adjunto ? route('proyectos.requerimientos.novedades.download', $nov->id) : null,
                    'file_name' => $nov->nombre_original ?? basename($nov->adjunto),
                    'tipo' => $nov->tipo ?? 'cliente'
                ];
            });

        // Al consultar, marcar automáticamente como leído si corresponde
        $req = RequerimientoProyecto::find($requerimientoProyectoId);
        if ($req && $req->user_id === auth()->id() && $req->notas_last_user_id && $req->notas_last_user_id !== auth()->id() && !$req->notas_seen) {
            $req->update(['notas_seen' => true]);
        }

        return response()->json($novedades);
    }

    public function store(Request $request)
    {
        $request->validate([
            'requerimiento_proyecto_id' => 'required|exists:requerimiento_proyecto,id',
            'cliente_id'                => 'nullable|exists:cliente_maestro,id',
            'novedad'                   => 'required|string',
            'adjunto'                   => 'nullable|file|max:30720',
            'tipo'                      => 'required|in:cliente,interno',
        ]);

        $data = $request->only(['requerimiento_proyecto_id', 'cliente_id', 'novedad', 'tipo']);
        $data['user_id'] = auth()->id();
        $warning = null;

        // Subida de adjunto
        if ($request->hasFile('adjunto')) {
            try {
                $file = $request->file('adjunto');
                $fileName = $file->getClientOriginalName();
                $path = 'novedades_proyecto/' . $request->requerimiento_proyecto_id;
                $file->storeAs($path, $fileName, 'public');

                $data['adjunto'] = $path . '/' . $fileName;
                $data['nombre_original'] = $fileName;
            } catch (\Exception $e) {
                \Log::error("Error subiendo adjunto de novedad de proyecto: " . $e->getMessage());
                $warning = 'El seguimiento fue guardado, pero el archivo adjunto no pudo guardarse.';
            }
        }

        try {
            $novedad = NovedadRequerimientoProyecto::create($data);

            // Actualizar alertas del requerimiento de proyecto
            $req = RequerimientoProyecto::find($request->requerimiento_proyecto_id);
            if ($req) {
                $req->update([
                    'notas_last_user_id' => auth()->id(),
                    'notas_seen' => false,
                ]);
            }

            if ($request->ajax()) {
                return response()->json([
                    'success'   => true,
                    'warning'   => $warning,
                    'novedad'   => $novedad,
                    'user_name' => auth()->user()->name,
                    'created_at'=> $novedad->created_at->format('d/m/Y H:i'),
                    'file_url'  => $novedad->adjunto ? route('proyectos.requerimientos.novedades.download', $novedad->id) : null,
                    'file_name' => $novedad->nombre_original ?? ($novedad->adjunto ? basename($novedad->adjunto) : null),
                    'tipo'      => $novedad->tipo
                ]);
            }

            $successMsg = $warning ?? 'Novedad de proyecto agregada correctamente.';
            return back()->with($warning ? 'warning' : 'success', $successMsg);

        } catch (\Exception $e) {
            \Log::error("Error guardando novedad de proyecto en BD: " . $e->getMessage());

            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }

            return back()->with('error', 'Error al guardar el seguimiento: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'novedad' => 'required|string',
        ]);

        $novedad = NovedadRequerimientoProyecto::findOrFail($id);
        $novedad->update([
            'novedad' => $request->novedad
        ]);

        // Marcar como no visto para notificar
        if ($novedad->requerimientoProyecto) {
            $novedad->requerimientoProyecto->update([
                'notas_last_user_id' => auth()->id(),
                'notas_seen' => false
            ]);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'novedad' => $novedad
            ]);
        }

        return back()->with('success', 'Novedad de proyecto actualizada correctamente.');
    }

    public function destroy($id)
    {
        $novedad = NovedadRequerimientoProyecto::findOrFail($id);
        $novedad->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Novedad de proyecto eliminada.');
    }

    public function download($id)
    {
        try {
            $nov = NovedadRequerimientoProyecto::findOrFail($id);

            if (!$nov->adjunto) {
                return redirect()->back()->with('error', 'Esta novedad no tiene un archivo adjunto.');
            }

            $disk = Storage::disk('public');
            $path = $nov->adjunto;

            if ($disk->exists($path)) {
                return $disk->download($path, $nov->nombre_original ?? basename($path));
            }

            return redirect()->back()->with('error', 'El archivo no existe físicamente en el servidor.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al procesar la descarga: ' . $e->getMessage());
        }
    }
}
