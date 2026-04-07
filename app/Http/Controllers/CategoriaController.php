<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class CategoriaController extends Controller
{
    private function resolveColumns(): array
    {
        $table = (new Categoria)->getTable();

        $nameCandidates = ['nombre', 'categoria', 'nombre_categoria', 'titulo', 'descripcion'];
        $descCandidates = ['descripcion', 'detalle', 'observacion', 'nota'];
        $activeCandidates = ['activo', 'estatus', 'status', 'is_active'];

        $nameCol = null;
        foreach ($nameCandidates as $c) {
            if (Schema::hasColumn($table, $c)) { $nameCol = $c; break; }
        }

        $descCol = null;
        foreach ($descCandidates as $c) {
            if (Schema::hasColumn($table, $c)) { $descCol = $c; break; }
        }

        $activeCol = null;
        foreach ($activeCandidates as $c) {
            if (Schema::hasColumn($table, $c)) { $activeCol = $c; break; }
        }

        // Requerimos al menos la columna principal (texto)
        if (!$nameCol) {
            abort(500, "No se encontró una columna de texto para categoría en la tabla '$table'.");
        }

        return compact('table', 'nameCol', 'descCol', 'activeCol');
    }

    public function index()
    {
        $cols = $this->resolveColumns();

        $categorias = Categoria::orderBy($cols['nameCol'], 'asc')->get();

        return view('mantenimiento.categorias.index', [
            'categorias' => $categorias,
            'nameCol'    => $cols['nameCol'],
            'descCol'    => $cols['descCol'],
            'activeCol'  => $cols['activeCol'],
        ]);
    }

    public function store(Request $request)
    {
        $cols = $this->resolveColumns();

        $rules = [
            'nombre' => ['required', 'string', 'max:255'],
        ];
        if ($cols['descCol']) $rules['descripcion'] = ['nullable', 'string', 'max:500'];
        if ($cols['activeCol']) $rules['activo'] = ['nullable', 'in:0,1'];

        $data = $request->validate($rules);

        $payload = [
            $cols['nameCol'] => $data['nombre'],
        ];
        if ($cols['descCol']) $payload[$cols['descCol']] = $data['descripcion'] ?? null;
        if ($cols['activeCol']) $payload[$cols['activeCol']] = isset($data['activo']) ? (int)$data['activo'] : 1;

        Categoria::create($payload);

        return redirect()->route('mantenimiento.categorias.index')->with('success', 'Categoría creada.');
    }

    public function update(Request $request, $id)
    {
        $cols = $this->resolveColumns();

        $rules = [
            'nombre' => ['required', 'string', 'max:255'],
        ];
        if ($cols['descCol']) $rules['descripcion'] = ['nullable', 'string', 'max:500'];
        if ($cols['activeCol']) $rules['activo'] = ['nullable', 'in:0,1'];

        $data = $request->validate($rules);

        $cat = Categoria::findOrFail($id);

        $cat->{$cols['nameCol']} = $data['nombre'];
        if ($cols['descCol']) $cat->{$cols['descCol']} = $data['descripcion'] ?? null;
        if ($cols['activeCol']) $cat->{$cols['activeCol']} = isset($data['activo']) ? (int)$data['activo'] : $cat->{$cols['activeCol']};

        $cat->save();

        return redirect()->route('mantenimiento.categorias.index')->with('success', 'Categoría actualizada.');
    }

    public function destroy($id)
    {
        $cat = Categoria::findOrFail($id);
        $cat->delete();

        return redirect()->route('mantenimiento.categorias.index')->with('success', 'Categoría eliminada.');
    }
}