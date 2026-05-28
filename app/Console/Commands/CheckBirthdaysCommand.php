<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LibretaContacto;
use App\Models\NotificacionSistema;
use Carbon\Carbon;

class CheckBirthdaysCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spgi:check-birthdays';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica cumpleaños y envía notificaciones al inicio de mes, semana antes, día antes y mismo día.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $today = Carbon::today();
        
        $usuarios = \App\Models\User::with('cumpleanos')->whereHas('cumpleanos')->get();

        foreach ($usuarios as $usuario) {
            $fecha_nacimiento = Carbon::parse($usuario->cumpleanos->fecha_nacimiento);
            $cumpleanosEsteAno = Carbon::create($today->year, $fecha_nacimiento->month, $fecha_nacimiento->day);

            $mensaje = "";
            $titulo = "Recordatorio de Cumpleaños de Usuario";

            // Si el cumpleaños es este mes y hoy es el inicio de mes
            if ($today->day == 1 && $fecha_nacimiento->month == $today->month) {
                $mensaje = "El usuario del sistema {$usuario->name} cumple años este mes ({$cumpleanosEsteAno->format('d/m/Y')}).";
            }
            // Falta 1 semana
            elseif ($cumpleanosEsteAno->copy()->subDays(7)->isSameDay($today)) {
                $mensaje = "Falta 1 semana para el cumpleaños de {$usuario->name} ({$cumpleanosEsteAno->format('d/m/Y')}).";
            }
            // Falta 1 día
            elseif ($cumpleanosEsteAno->copy()->subDay()->isSameDay($today)) {
                $mensaje = "Mañana es el cumpleaños del usuario {$usuario->name}.";
            }
            // El mismo día
            elseif ($cumpleanosEsteAno->isSameDay($today)) {
                $mensaje = "¡Hoy es el cumpleaños de {$usuario->name}!";
            }

            if ($mensaje !== "") {
                // Notificar a todos los administradores (cod_roleUser = 1 es admin)
                $admins = \App\Models\User::where('cod_roleUser', 1)->get();
                foreach ($admins as $admin) {
                    NotificacionSistema::create([
                        'user_id' => $admin->id,
                        'titulo' => $titulo,
                        'mensaje' => $mensaje,
                        'url' => route('usuarios.index'),
                    ]);
                }
            }
        }

        $this->info('Verificación de cumpleaños completada.');
        return 0;
    }
}
