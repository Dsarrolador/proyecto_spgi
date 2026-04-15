<?php

namespace App\Http\Controllers;

use App\Models\NotificacionSistema;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Mail\NuevaNotificacionMail;
use Illuminate\Support\Facades\Mail;

class NotificacionController extends Controller
{
    public function index()
    {
        $notificaciones = NotificacionSistema::where('user_id', Auth::id())
            ->with('sender:id,name')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('notificaciones.index', compact('notificaciones'));
    }

    /**
     * Obtener notificaciones no leídas para el usuario actual
     */
    public function getUnread()
    {
        $notificaciones = NotificacionSistema::where('user_id', Auth::id())
            ->with('sender:id,name')
            ->orderBy('created_at', 'desc')
            ->limit(100) // Aumentamos el límite a 100 para permitir que el contador llegue a 99+
            ->get();


        return response()->json($notificaciones);
    }

    /**
     * Marcar una notificación como leída
     */
    public function markAsRead($id)
    {
        $notif = NotificacionSistema::where('user_id', Auth::id())->findOrFail($id);
        $notif->update(['leido_at' => now()]);
        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $notif = NotificacionSistema::where('user_id', Auth::id())->findOrFail($id);
        $notif->delete();
        return response()->json(['success' => true]);
    }

    public function destroyAll()
    {
        NotificacionSistema::where('user_id', Auth::id())->delete();
        return response()->json(['success' => true]);
    }

    /**
     * Vista administrativa para enviar avisos
     */
    public function adminPanel()
    {
        if (!Auth::user()->esAdmin && !Auth::user()->esEncargado) {
            return redirect()->route('bienvenido')->with('error', 'No tiene permisos para acceder a esta sección.');
        }

        $usuarios = User::where('id', '!=', Auth::id())->orderBy('name')->get();
        return view('notificaciones.admin', compact('usuarios'));
    }

    /**
     * Enviar notificaciones
     */
    public function send(Request $request)
    {
        $request->validate([
            'mensaje' => 'required|string|max:500',
        ]);

        $mensaje = $request->mensaje;
        $esGlobal = $request->has('destinatario_global');
        $destinatarios = $request->destinatarios; // Array de IDs

        if ($esGlobal) {
            $users = User::all();
        } elseif (!empty($destinatarios)) {
            $users = User::whereIn('id', (array)$destinatarios)->get();
        } else {
            return back()->with('error', 'Debe seleccionar al menos un destinatario.');
        }

        $emailsDestino = [];

        foreach ($users as $user) {
            NotificacionSistema::create([
                'user_id' => $user->id,
                'sender_id' => Auth::id(),
                'mensaje' => $mensaje,
            ]);

            if (!empty($user->email)) {
                $emailsDestino[] = $user->email;
            }
        }

        // Enviar un solo correo con todos los destinos en copia oculta (BCC)
        // FUNCIONALIDAD DE CORREO PAUSADA POR SOLICITUD DEL USUARIO
        /*
        if (count($emailsDestino) > 0) {
            $remitenteNombre = Auth::user()->name;
            $remitenteEmail = Auth::user()->email;

            try {
                Mail::to($remitenteEmail)
                    ->bcc($emailsDestino)
                    ->send(new NuevaNotificacionMail($mensaje, $remitenteNombre, $remitenteEmail));
            } catch (\Exception $e) {
                \Log::error('Error enviando correo masivo de notificacion: ' . $e->getMessage());
            }
        }
        */

        return back()->with('success', 'Aviso enviado correctamente.');
    }
}
