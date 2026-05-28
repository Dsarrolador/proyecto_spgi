<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LibretaContacto;
use App\Models\Roles;

class LibretaContactoController extends Controller
{
    /* =========================
     * LISTADO
     * ========================= */
    public function index()
    {
        $contactos = LibretaContacto::with(['rol', 'cliente'])->get();
        $roles = Roles::all();
        $clientes = \App\Models\ClienteMaestro::orderBy('nombre')->get();
        return view('LibretaContacto', compact('contactos', 'roles', 'clientes'));
    }

    /* =========================
     * FORM CREAR
     * ========================= */
    public function create()
    {
        $roles = Roles::all();
        return view('libreta_contacto_create', compact('roles'));
    }

    /* =========================
     * GUARDAR CONTACTO
     * ========================= */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo_cliente_maestro' => 'required|integer',
            'nombre'   => 'required|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'correo'   => 'nullable|string|max:100',
            'fecha_nacimiento' => 'nullable|date',
            'nota'     => 'nullable|string|max:255',
            'codigo_rol' => 'nullable|integer',
        ]);

        // 🔐 ROL OBLIGATORIO (BD)
        $validated['codigo_rol'] = $validated['codigo_rol'] ?? 1; // 👈 ROL POR DEFECTO

        LibretaContacto::create($validated);

        return redirect()->back()
            ->with('success', 'Contacto agregado correctamente.');
    }

    /* =========================
     * EDITAR
     * ========================= */
    public function edit($id)
    {
        $contacto = LibretaContacto::findOrFail($id);
        $roles = Roles::all();

        return view('libreta_contacto_edit', compact('contacto', 'roles'));
    }

    /* =========================
     * ACTUALIZAR
     * ========================= */
    public function update(Request $request, $id)
    {
        $contacto = LibretaContacto::findOrFail($id);

        $validated = $request->validate([
            'nombre'   => 'required|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'correo'   => 'nullable|string|max:100',
            'fecha_nacimiento' => 'nullable|date',
            'nota'     => 'nullable|string|max:255',
            'codigo_rol' => 'required|integer', // 🔐 YA ES OBLIGATORIO
        ]);

        $contacto->update($validated);

        return redirect()->back()
            ->with('success', 'Contacto actualizado correctamente.');
    }

    /* =========================
     * ELIMINAR
     * ========================= */
    public function destroy($id)
    {
        $contacto = LibretaContacto::findOrFail($id);
        $contacto->delete();

        return redirect()->back()
            ->with('success', 'Contacto eliminado correctamente.');
    }

    /* =========================
     * CONTACTOS POR CLIENTE
     * ========================= */
    public function contactos($id)
    {
        $contactos = LibretaContacto::where(
            'codigo_cliente_maestro',
            $id
        )->with('rol')->get();

        return view('cliente_maestro.contactos', compact('contactos'));
    }
}
