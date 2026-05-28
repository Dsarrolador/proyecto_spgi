<?php

namespace App\Http\Controllers;

use App\Models\NovedadRequerimientoProyecto;
use App\Models\RequerimientoProyecto;
use App\Models\NotificacionSistema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NovedadRequerimientoProyectoController extends Controller
{
    public function index($requerimientoId)
    {
        $req = RequerimientoProyecto::find($requerimientoId);
        if ($req && $req->user_id === auth()->id() && $req->notas_last_user_id !== auth()->id() && !$req->notas_seen) {
            $req->update(['notas_seen' => true]);
        }

        $novedades = NovedadRequerimientoProyecto::with('user')
            ->where('requerimiento_proyecto_id', $requerimientoId)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function($nov) {
                return [
                    'id' => $nov->id,
                    'user_id' => $nov->user_id,
                    'user_name' => $nov->user?->name ?? 'Usuario',
                    'novedad' => $nov->novedad,
                    'created_at' => $nov->created_at ? $nov->created_at->format('d/m/Y H:i') : '',
                    'file_url' => $nov->adjunto ? route('proyectos-novedades.download', $nov->id) : null,
                    'file_name' => $nov->nombre_original ?? ($nov->adjunto ? basename($nov->adjunto) : null),
                    'tipo' => $nov->tipo ?? 'cliente'
                ];
            });

        return response()->json($novedades);
    }

    public function store(Request $request)
    {
        $request->validate([
            'requerimiento_proyecto_id' => 'required',
            'cliente_id'                => 'required',
            'novedad'                   => 'required',
            'adjunto'                   => 'nullable|file|max:30720',
            'tipo'                      => 'required|in:cliente,interno',
        ]);

        $data = $request->only(['requerimiento_proyecto_id', 'cliente_id', 'novedad', 'tipo']);
        $data['user_id'] = auth()->id();
        $ftpWarning = null;

        if ($request->hasFile('adjunto')) {
            try {
                $file = $request->file('adjunto');
                $fileName = $file->getClientOriginalName();
                $path = 'novedades_proyectos/' . $request->requerimiento_proyecto_id;
                $file->storeAs($path, $fileName, 'public');

                $data['adjunto'] = $path . '/' . $fileName;
                $data['nombre_original'] = $fileName;
            } catch (\Exception $e) {
                \Log::error("Error subiendo adjunto de novedad de proyecto: " . $e->getMessage());
                $ftpWarning = 'El seguimiento fue guardado, pero el archivo adjunto no pudo guardarse.';
            }
        }

        try {
            $novedad = NovedadRequerimientoProyecto::create($data);

            // Actualizar el requerimiento padre para alertar/notificar cambios
            $req = RequerimientoProyecto::find($request->requerimiento_proyecto_id);
            if ($req) {
                $req->update([
                    'notas_last_user_id' => auth()->id(),
                    'notas_seen' => false
                ]);
            }

            // NOTIFICAR SEGUIMIENTOS (Si es colaborativo)
            if ($req && $req->es_colaborativo) {
                $urlDeVista = route('requerimientos_proyecto.show', $req->id) . '#novedades';
                $usuarioUpdater = auth()->id();

                $senderName = auth()->user()->name ?? 'Un usuario';
                $participantes = array_filter(array_unique([$req->user_id, $req->asignado_user_id]));
                
                // Agregar colaboradores
                if ($req->colaboradores) {
                    foreach ($req->colaboradores as $c) {
                        $participantes[] = $c->id;
                    }
                }
                
                $participantes = array_filter(array_unique($participantes));

                foreach ($participantes as $p) {
                    if ($p && $p != $usuarioUpdater) {
                        NotificacionSistema::create([
                            'user_id'   => $p,
                            'sender_id' => $usuarioUpdater,
                            'titulo'    => 'Nueva Novedad de Proyecto (#' . $req->id . ')',
                            'mensaje'   => $senderName . ' ha agregado un seguimiento al requerimiento del proyecto.',
                            'url'       => $urlDeVista,
                        ]);
                    }
                }
            }

            if ($request->ajax()) {
                return response()->json([
                    'success'   => true,
                    'warning'   => $ftpWarning,
                    'novedad'   => $novedad,
                    'user_name' => auth()->user()->name,
                    'created_at'=> $novedad->created_at->format('d/m/Y H:i'),
                    'file_url'  => $novedad->adjunto ? route('proyectos-novedades.download', $novedad->id) : null,
                    'file_name' => $novedad->nombre_original ?? ($novedad->adjunto ? basename($novedad->adjunto) : null),
                    'tipo'      => $novedad->tipo
                ]);
            }

            $successMsg = $ftpWarning ?? 'Novedad agregada correctamente.';
            return back()->with($ftpWarning ? 'warning' : 'success', $successMsg);

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

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'novedad' => $novedad
            ]);
        }

        return back()->with('success', 'Novedad actualizada correctamente.');
    }

    public function destroy($id)
    {
        $novedad = NovedadRequerimientoProyecto::findOrFail($id);
        $novedad->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Novedad eliminada.');
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
