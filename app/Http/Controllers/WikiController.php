<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\WikiDocument;
use Illuminate\Support\Facades\Storage;

class WikiController extends Controller
{
    public function index(Request $request)
    {
        $query = WikiDocument::with('creator.role');

        $user = auth()->user();
        $userRoleName = ($user && $user->role) ? $user->role->nombre : null;

        // Filtro de visibilidad:
        // Los roles que NO son Administracion ni Encargado NO pueden ver documentos
        // subidos por usuarios con esos roles.
        if (!in_array($userRoleName, ['Administracion', 'Encargado'])) {
            $query->where(function($q) {
                $q->whereDoesntHave('creator.role', function($rq) {
                    $rq->whereIn('nombre', ['Administracion', 'Encargado']);
                })->orWhereNull('user_id'); // Preservar antiguos o sin dueño como públicos si aplica
            });
        }

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('tags', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        $documents = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('wiki.index', compact('documents'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'required|file|max:30720', // max 30MB
            'tags' => 'nullable|string'
        ]);

        $file = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('wiki', $fileName, 'public');

        WikiDocument::create([
            'title' => $request->title,
            'description' => $request->description,
            'file_path' => $filePath,
            'tags' => $request->tags,
            'user_id' => auth()->id()
        ]);

        return redirect()->route('wiki.index')->with('success', 'Documento subido correctamente.');
    }

    public function update(Request $request, WikiDocument $wikiDocument)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'nullable|file|max:30720', // optional
            'tags' => 'nullable|string'
        ]);

        $data = [
            'title' => $request->title,
            'description' => $request->description,
            'tags' => $request->tags
        ];

        if ($request->hasFile('file')) {
            // Delete old file
            if (Storage::disk('public')->exists($wikiDocument->file_path)) {
                Storage::disk('public')->delete($wikiDocument->file_path);
            }

            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $data['file_path'] = $file->storeAs('wiki', $fileName, 'public');
        }

        $wikiDocument->update($data);

        return redirect()->route('wiki.index')->with('success', 'Documento actualizado correctamente.');
    }

    public function download(WikiDocument $wikiDocument)
    {
        // Restricción: No se puede descargar si no está validado
        if ($wikiDocument->estado !== 'Validado') {
            return redirect()->back()->with('error', 'Este documento aún no ha sido validado para su descarga.');
        }

        if (Storage::disk('public')->exists($wikiDocument->file_path)) {
            $filename = basename($wikiDocument->file_path);
            $originalName = preg_replace('/^\d+_/', '', $filename);
            return Storage::disk('public')->download($wikiDocument->file_path, $originalName);
        }

        return redirect()->back()->with('error', 'El archivo no existe.');
    }

    public function destroy(WikiDocument $wikiDocument)
    {
        if (Storage::disk('public')->exists($wikiDocument->file_path)) {
            Storage::disk('public')->delete($wikiDocument->file_path);
        }
        
        $wikiDocument->delete();

        return redirect()->route('wiki.index')->with('success', 'Documento eliminado correctamente.');
    }
    public function approve(WikiDocument $wikiDocument)
    {
        if (auth()->user()->role && in_array(auth()->user()->role->nombre, ['Administracion', 'Encargado'])) {
            $wikiDocument->update(['estado' => 'Validado']);
            return redirect()->route('wiki.index')->with('success', 'Documento validado correctamente.');
        }

        return redirect()->back()->with('error', 'No tienes permisos para validar este documento.');
    }
}
