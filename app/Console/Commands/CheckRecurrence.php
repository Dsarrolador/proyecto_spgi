<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\RequerimientoCliente;
use App\Models\NotificacionSistema;
use Carbon\Carbon;

class CheckRecurrence extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:recurrence';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica requerimientos recurrentes y crea nuevas instancias automáticamente.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $now = now();
        $this->info("Iniciando chequeo de recurrencia: " . $now);

        $recurrentes = RequerimientoCliente::where('es_recurrente', true)
            ->where('proxima_fecha_ejecucion', '<=', $now)
            ->get();

        if ($recurrentes->isEmpty()) {
            $this->info("No hay requerimientos recurrentes para procesar.");
            return 0;
        }

        foreach ($recurrentes as $master) {
            $this->info("Procesando master ID: {$master->id} - Frecuencia: {$master->frecuencia}");

            // 1. Crear la nueva instancia (clon)
            $nuevo = RequerimientoCliente::create([
                'cliente_id'       => $master->cliente_id,
                'contacto_id'      => $master->contacto_id,
                'tipo_soporte_id'  => $master->tipo_soporte_id,
                'texto_imagen'     => "[RECURRENTE] " . $master->texto_imagen,
                'foto'             => $master->foto,
                'estado_id'        => 1, // Nuevo
                'user_id'          => $master->user_id,
                'creador_user_id'  => $master->creador_user_id,
                'asignado_user_id' => $master->asignado_user_id,
                'es_recurrente'    => false, // La instancia no es master (opcional)
                'facturado'        => 0,
            ]);

            // 2. Notificar al encargado si existe
            if ($nuevo->asignado_user_id) {
                NotificacionSistema::create([
                    'user_id'   => $nuevo->asignado_user_id,
                    'sender_id' => null, // Sistema
                    'mensaje'   => "Se ha generado automáticamente un requerimiento recurrente para el cliente: " . optional($master->clienteRelation)->nombre,
                ]);
            }

            // 3. Actualizar la fecha de la próxima ejecución en el master
            // Calculamos la siguiente fecha basándonos en la fecha que tocaba (proxima_fecha_ejecucion)
            // para no perder precisión si el comando corre tarde.
            $master->update([
                'proxima_fecha_ejecucion' => $master->calcularProximaFecha($master->proxima_fecha_ejecucion)
            ]);

            $this->info("Instancia creada ID: {$nuevo->id}. Próxima ejecución master: {$master->proxima_fecha_ejecucion}");
        }

        $this->info("Proceso completado.");
        return 0;
    }
}
