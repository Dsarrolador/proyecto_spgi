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
                    'file_url' => $nov->adjunto ? asset('storage/' . $nov->adjunto) : null,
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
            'adjunto'          => 'nullable|file|max:10240',
        ]);

        $data = $request->only(['requerimiento_id', 'cliente_id', 'novedad']);
        $data['user_id'] = auth()->id();

        if ($request->hasFile('adjunto')) {
            $file = $request->file('adjunto');
            $data['adjunto'] = $file->store('novedades', 'public');
            $data['nombre_original'] = $file->getClientOriginalName();
        }

        $novedad = NovedadRequerimiento::create($data);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'novedad' => $novedad,
                'user_name' => auth()->user()->name,
                'created_at' => $novedad->created_at->format('d/m/Y H:i'),
                'file_url' => $novedad->adjunto ? asset('storage/' . $novedad->adjunto) : null,
                'file_name' => $novedad->nombre_original ?? basename($novedad->adjunto)
            ]);
        }

        return back()->with('success', 'Novedad agregada correctamente.');
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
}
