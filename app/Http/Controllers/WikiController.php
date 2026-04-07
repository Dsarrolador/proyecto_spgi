<?php

namespace App\Http\Controllers;

use App\Models\WikiDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class WikiController extends Controller
{
    public function index(Request $request)
    {
        $query = WikiDocument::query();

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhere('tags', 'LIKE', "%{$search}%");
            });
        }

        // 🔎 Filter by status (estado)
        if ($request->has('estado') && !empty($request->estado)) {
            if ($request->estado !== 'Todos') {
                $query->where('estado', $request->estado);
            }
        }

        $documents = $query->orderByDesc('created_at')->paginate(10);

        return view('wiki.index', compact('documents'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'tags'        => 'nullable|string',
            'file'        => 'required|file|max:30720',
        ]);

        try {
            $file = $request->file('file');
            
            // Truncate filename if it's too long to prevent DB column overflow (255 chars)
            $originalName = $file->getClientOriginalName();
            if (strlen($originalName) > 200) {
                $ext = $file->getClientOriginalExtension();
                $originalName = substr($originalName, 0, 190) . '.' . $ext;
            }
            
            $fileName = time() . '_' . $originalName;
            
            // Ensure the directory exists
            if (!Storage::disk('public')->exists('Wiki')) {
                Storage::disk('public')->makeDirectory('Wiki');
            }

            $path = $file->storeAs('Wiki', $fileName, 'public');

            if (!$path) {
                throw new \Exception("No se pudo guardar el archivo en el disco.");
            }

            WikiDocument::create([
                'user_id'     => auth()->id(),
                'title'       => $request->title,
                'description' => $request->description,
                'tags'        => $request->tags,
                'file_path'   => 'Wiki/' . $fileName,
                'estado'      => 'Sin validar'
            ]);

            return redirect()->route('wiki.index')->with('success', 'Documento subido correctamente.');
        } catch (\Exception $e) {
            \Log::error("Error subiendo archivo a la Wiki: " . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al subir el documento: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $doc = WikiDocument::findOrFail($id);

        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'tags'        => 'nullable|string',
            'file'        => 'nullable|file|max:30720',
        ]);

        try {
            $data = $request->only(['title', 'description', 'tags']);

            if ($request->hasFile('file')) {
                // Delete old file if exists
                if ($doc->file_path && Storage::disk('public')->exists($doc->file_path)) {
                    Storage::disk('public')->delete($doc->file_path);
                }

                $file = $request->file('file');
                $originalName = $file->getClientOriginalName();
                if (strlen($originalName) > 200) {
                    $ext = $file->getClientOriginalExtension();
                    $originalName = substr($originalName, 0, 190) . '.' . $ext;
                }
                
                $fileName = time() . '_' . $originalName;
                $file->storeAs('Wiki', $fileName, 'public');
                $data['file_path'] = 'Wiki/' . $fileName;
            }

            $doc->update($data);

            return redirect()->route('wiki.index')->with('success', 'Documento actualizado correctamente.');
        } catch (\Exception $e) {
            \Log::error("Error actualizando archivo en la Wiki: " . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al actualizar el documento: ' . $e->getMessage());
        }
    }

    public function download($id)
    {
        try {
            $doc = WikiDocument::findOrFail($id);

            if ($doc->file_path && Storage::disk('public')->exists($doc->file_path)) {
                return Storage::disk('public')->download($doc->file_path, $doc->title);
            }

            return redirect()->back()->with('error', 'El archivo no existe físicamente en el servidor.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al descargar el archivo: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $doc = WikiDocument::findOrFail($id);

            if ($doc->file_path && Storage::disk('public')->exists($doc->file_path)) {
                Storage::disk('public')->delete($doc->file_path);
            }

            $doc->delete();

            return redirect()->route('wiki.index')->with('success', 'Documento eliminado correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al eliminar el documento: ' . $e->getMessage());
        }
    }

    public function approve($id)
    {
        try {
            $doc = WikiDocument::findOrFail($id);
            $doc->update(['estado' => 'Validado']);

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Documento validado correctamente.',
                    'id'      => $id,
                    'estado'  => 'Validado'
                ]);
            }

            return redirect()->route('wiki.index')->with('success', 'Documento validado correctamente.');
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Error al validar el documento: ' . $e->getMessage());
        }
    }
}
