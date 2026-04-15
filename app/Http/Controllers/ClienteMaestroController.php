<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\ClienteMaestro;
use App\Models\LibretaContacto;
use App\Models\Roles;
use App\Models\CategoriaIguala;
use App\Models\User;
use App\Models\NotificacionSistema;
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
        $categoriasIguala = CategoriaIguala::orderBy('nombre')->get();
        $roles = Roles::orderBy('nombre')->get();

        return view('cliente_maestro', compact('clientes', 'categorias', 'categoriasIguala', 'roles'));
    }

    /**
     * FORMULARIO CREAR CLIENTE
     */
    public function create()
    {
        $categorias = Categoria::orderBy('categoria')->get();
        $categoriasIguala = CategoriaIguala::orderBy('nombre')->get();
        $roles = Roles::orderBy('nombre')->get();

        // si tu vista de crear se llama crear_cliente.blade.php
        return view('crear_cliente', compact('categorias', 'categoriasIguala', 'roles'));
    }

    /**
     * GUARDAR CLIENTE
     */
    public function store(Request $request)
    {
        $validData = $request->validate([
            'nombre'                => 'required|string|max:150',
            'rnc'                   => 'nullable|string|max:50',
            'telefono_principal'    => 'nullable|string|max:30',
            'clasificacion_negocio' => 'nullable|string|max:10',
            'clasificacion_interna' => 'nullable|exists:categoria,id',
            'categoria_iguala_id'   => 'nullable|exists:categorias_iguala,id',
            'direccion_escrita'     => 'nullable|string|max:255',
            'notas'                 => 'nullable|string',
        ]);

        // Sincronizar el nombre de la iguala para compatibilidad
        $categoriaNombre = null;
        if ($request->categoria_iguala_id) {
            $plan = CategoriaIguala::find($request->categoria_iguala_id);
            $categoriaNombre = $plan ? $plan->nombre : null;
        }

        $nuevoCliente = ClienteMaestro::create(array_merge($validData, [
            'categoria_iguala' => $categoriaNombre
        ]));

        // NOTIFICACIÓN GLOBAL
        $usuarios = User::where('id', '!=', auth()->id())->get(['id']);
        $notificaciones = [];
        $sender_id = auth()->id();
        $sender_name = auth()->user()->name ?? 'Un usuario';
        
        foreach ($usuarios as $u) {
            $notificaciones[] = [
                'user_id' => $u->id,
                'sender_id' => $sender_id,
                'titulo' => 'NUEVO CLIENTE REGISTRADO',
                'mensaje' => "{$sender_name} creó al cliente <b>" . $nuevoCliente->nombre . "</b>",
                'url' => route('clientes.index'),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (count($notificaciones) > 0) {
            NotificacionSistema::insert($notificaciones);
        }

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente creado correctamente');
    }

    /**
     * ACTUALIZAR CLIENTE
     */
    public function update(Request $request, $id)
    {
        $validData = $request->validate([
            'nombre'                => 'required|string|max:150',
            'rnc'                   => 'nullable|string|max:50',
            'telefono_principal'    => 'nullable|string|max:30',
            'clasificacion_negocio' => 'nullable|string|max:10',
            'clasificacion_interna' => 'nullable|exists:categoria,id',
            'categoria_iguala_id'   => 'nullable|exists:categorias_iguala,id',
            'direccion_escrita'     => 'nullable|string|max:255',
            'notas'                 => 'nullable|string',
        ]);

        $cliente = ClienteMaestro::findOrFail($id);

        // Sincronizar el nombre de la iguala para compatibilidad
        $categoriaNombre = null;
        if ($request->categoria_iguala_id) {
            $plan = CategoriaIguala::find($request->categoria_iguala_id);
            $categoriaNombre = $plan ? $plan->nombre : null;
        }

        $cliente->update(array_merge($validData, [
            'categoria_iguala' => $categoriaNombre
        ]));

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