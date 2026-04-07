<?php

namespace App\Http\Controllers;

use App\Models\NovedadRequerimiento;
use App\Models\RequerimientoCliente;
use Illuminate\Http\Request;

class NovedadRequerimientoController extends Controller
{
    public function index($requerimientoId)
    {
        $novedades = NovedadRequerimiento::with('user')
            ->where('requerimiento_id', $requerimientoId)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function($nov) {
                return [
                    'id' => $nov->id,
                    'user_name' => $nov->user->name ?? 'Usuario',
                    'novedad' => $nov->novedad,
                    'created_at' => $nov->created_at->format('d/m/Y H:i'),
                    'file_url' => $nov->adjunto ? route('novedades.download', $nov->id) : null,
                    'file_name' => $nov->nombre_original ?? basename($nov->adjunto)
                ];
            });

        return response()->json($novedades);
    }

    public function store(Request $request)
    {
        $request->validate([
            'requerimiento_id' => 'required',
            'cliente_id'       => 'required',
            'novedad'          => 'required',
            'adjunto'          => 'nullable|file|max:30720',
        ]);

        try {
            $data = $request->only(['requerimiento_id', 'cliente_id', 'novedad']);
            $data['user_id'] = auth()->id();

            if ($request->hasFile('adjunto')) {
                $file = $request->file('adjunto');
                $fileName = $file->getClientOriginalName();
                $path = 'novedades/' . $request->requerimiento_id;
                $file->storeAs($path, $fileName, 'public');
                
                $data['adjunto'] = $path . '/' . $fileName;
                $data['nombre_original'] = $fileName;
            }

            $novedad = NovedadRequerimiento::create($data);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'novedad' => $novedad,
                    'user_name' => auth()->user()->name,
                    'created_at' => $novedad->created_at->format('d/m/Y H:i'),
                    'file_url' => $novedad->adjunto ? route('novedades.download', $novedad->id) : null,
                    'file_name' => $novedad->nombre_original ?? basename($novedad->adjunto)
                ]);
            }

            return back()->with('success', 'Novedad agregada correctamente.');
        } catch (\Exception $e) {
            \Log::error("Error guardando novedad: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'novedad' => 'required|string',
        ]);

        $novedad = NovedadRequerimiento::findOrFail($id);
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
        $novedad = NovedadRequerimiento::findOrFail($id);
        $novedad->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Novedad eliminada.');
    }

    public function download($id)
    {
        try {
            $nov = NovedadRequerimiento::findOrFail($id);

            if (!$nov->adjunto) {
                return redirect()->back()->with('error', 'Esta novedad no tiene un archivo adjunto.');
            }

            $disk = \Illuminate\Support\Facades\Storage::disk('public');
            $path = $nov->adjunto;

            // 1. Try standard path
            if ($disk->exists($path)) {
                return $disk->download($path, $nov->nombre_original ?? basename($path));
            }

            // 2. Try legacy path (archivos_novedades)
            $legacyPath = str_replace('novedades/', 'archivos_novedades/', $path);
            if ($disk->exists($legacyPath)) {
                return $disk->download($legacyPath, $nov->nombre_original ?? basename($path));
            }

            return redirect()->back()->with('error', 'El archivo no existe físicamente en el servidor.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al procesar la descarga: ' . $e->getMessage());
        }
    }
}
