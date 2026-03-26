<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\ClienteMaestro;
use App\Models\LibretaContacto;
use App\Models\Roles;
use Illuminate\Http\Request;

class ClienteMaestroController extends Controller
{
    /**
     * LISTADO DE CLIENTES
     */
    public function index(Request $request)
    {
        $q = $request->get('q');

        $clientesQuery = ClienteMaestro::with(['categoria', 'contactos.rol']);

        if ($q) {
            $clientesQuery->where(function ($qq) use ($q) {
                $qq->where('nombre', 'like', "%{$q}%")
                    ->orWhere('rnc', 'like', "%{$q}%")
                    ->orWhere('telefono_principal', 'like', "%{$q}%");
            });
        }

        $clientes = $clientesQuery->get();

        $categorias = Categoria::orderBy('categoria')->get();
        $roles = Roles::orderBy('nombre')->get();

        return view('cliente_maestro', compact('clientes', 'categorias', 'roles'));
    }

    /**
     * FORMULARIO CREAR CLIENTE
     */
    public function create()
    {
        $categorias = Categoria::orderBy('categoria')->get();
        $roles = Roles::orderBy('nombre')->get();

        // si tu vista de crear se llama crear_cliente.blade.php
        return view('crear_cliente', compact('categorias', 'roles'));
    }

    /**
     * GUARDAR CLIENTE
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre'                => 'required|string|max:150',
            'rnc'                   => 'nullable|string|max:50',
            'telefono_principal'    => 'nullable|string|max:30',
            'clasificacion_negocio' => 'nullable|string|max:10',
            'clasificacion_interna' => 'nullable|exists:categoria,id',
            'categoria_iguala'      => 'nullable|string|max:60',
            'direccion_escrita'     => 'nullable|string|max:255',
            'notas'                 => 'nullable|string',
        ]);

        ClienteMaestro::create([
            'nombre'                => $request->nombre,
            'rnc'                   => $request->rnc,
            'telefono_principal'    => $request->telefono_principal,
            'clasificacion_negocio' => $request->clasificacion_negocio,
            'clasificacion_interna' => $request->clasificacion_interna,
            'categoria_iguala'      => $request->categoria_iguala,
            'direccion_escrita'     => $request->direccion_escrita,
            'notas'                 => $request->notas,
        ]);

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente creado correctamente');
    }

    /**
     * ACTUALIZAR CLIENTE
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre'                => 'required|string|max:150',
            'rnc'                   => 'nullable|string|max:50',
            'telefono_principal'    => 'nullable|string|max:30',
            'clasificacion_negocio' => 'nullable|string|max:10',
            'clasificacion_interna' => 'nullable|exists:categoria,id',
            'categoria_iguala'      => 'nullable|string|max:60',
            'direccion_escrita'     => 'nullable|string|max:255',
            'notas'                 => 'nullable|string',
        ]);

        $cliente = ClienteMaestro::findOrFail($id);

        $cliente->update([
            'nombre'                => $request->nombre,
            'rnc'                   => $request->rnc,
            'telefono_principal'    => $request->telefono_principal,
            'clasificacion_negocio' => $request->clasificacion_negocio,
            'clasificacion_interna' => $request->clasificacion_interna,
            'categoria_iguala'      => $request->categoria_iguala,
            'direccion_escrita'     => $request->direccion_escrita,
            'notas'                 => $request->notas,
        ]);

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente actualizado correctamente');
    }

    /**
     * ELIMINAR CLIENTE
     */
    public function destroy($id)
    {
        ClienteMaestro::findOrFail($id)->delete();

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente eliminado correctamente');
    }

    /**
     * CONTACTOS POR CLIENTE (AJAX)
     */
    public function contactos($cliente)
    {
        $clienteId = $cliente instanceof ClienteMaestro ? $cliente->id : $cliente;

        $fk = 'codigo_cliente_maestro';

        $rows = LibretaContacto::query()
            ->where($fk, $clienteId)
            ->orderBy('nombre')
            ->get();

        $contactos = $rows->map(function ($c) {
            $nombre =
                $c->nombre
                ?? $c->nombre_contacto
                ?? $c->contacto
                ?? $c->nombre_completo
                ?? $c->persona
                ?? ('Contacto #' . $c->id);

            return [
                'id' => $c->id,
                'nombre' => $nombre,
            ];
        })->values();

        return response()->json($contactos);
    }
}