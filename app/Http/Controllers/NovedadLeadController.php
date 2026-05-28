<?php

namespace App\Http\Controllers;

use App\Models\NovedadLead;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class NovedadLeadController extends Controller
{
    public function store(Request $request, $lead_id)
    {
        $request->validate([
            'mensaje' => 'required|string',
            'adjunto' => 'nullable|file|max:20480', // 20MB max
        ]);

        $lead = Lead::findOrFail($lead_id);

        $novedad = new NovedadLead();
        $novedad->lead_id = $lead->id;
        $novedad->user_id = Auth::id();
        $novedad->mensaje = $request->mensaje;

        if ($request->hasFile('adjunto')) {
            $file = $request->file('adjunto');
            $originalName = $file->getClientOriginalName();
            $path = $file->store('leads/novedades', 'public');
            
            $novedad->adjunto = $path;
            $novedad->nombre_original = $originalName;
        }

        $novedad->save();

        return redirect()->back()->with('success', 'Novedad agregada correctamente.');
    }

    public function download($id)
    {
        $novedad = NovedadLead::findOrFail($id);

        if ($novedad->adjunto) {
            $path = storage_path('app/public/' . $novedad->adjunto);
            if (file_exists($path)) {
                return response()->download($path, $novedad->nombre_original);
            }
        }

        return redirect()->back()->with('error', 'Archivo no encontrado.');
    }
}
