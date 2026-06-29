<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\RequerimientoCliente;
use App\Models\RequerimientoAdministrativo;
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

        // 1. REQUERIMIENTOS DE CLIENTES
        $recurrentesCliente = RequerimientoCliente::where('es_recurrente', true)
            ->where('proxima_fecha_ejecucion', '<=', $now)
            ->get();

        foreach ($recurrentesCliente as $master) {
            $this->info("Procesando master Cliente ID: {$master->id} - Frecuencia: {$master->frecuencia}");

            // Crear la nueva instancia (clon)
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
                'es_recurrente'    => false,
                'facturado'        => 0,
            ]);

            if ($nuevo->asignado_user_id) {
                NotificacionSistema::create([
                    'user_id'   => $nuevo->asignado_user_id,
                    'sender_id' => null,
                    'mensaje'   => "Se ha generado automáticamente un requerimiento recurrente para el cliente: " . optional($master->clienteRelation)->nombre,
                ]);
            }

            $master->update([
                'proxima_fecha_ejecucion' => $master->calcularProximaFecha($master->proxima_fecha_ejecucion)
            ]);

            $this->info("Instancia Cliente creada ID: {$nuevo->id}. Próxima ejecución master: {$master->proxima_fecha_ejecucion}");
        }

        // 2. REQUERIMIENTOS ADMINISTRATIVOS
        $recurrentesAdmin = RequerimientoAdministrativo::where('es_recurrente', true)
            ->where('proxima_fecha_ejecucion', '<=', $now)
            ->get();

        foreach ($recurrentesAdmin as $masterAdmin) {
            $this->info("Procesando master Admin ID: {$masterAdmin->id} - Frecuencia: {$masterAdmin->frecuencia}");

            // Crear la nueva instancia (clon)
            $nuevoAdmin = RequerimientoAdministrativo::create([
                'titulo'           => "[RECURRENTE] " . $masterAdmin->titulo,
                'descripcion'      => $masterAdmin->descripcion,
                'prioridad'        => $masterAdmin->prioridad,
                'estado'           => 'Pendiente', // Nuevo
                'user_id'          => $masterAdmin->user_id,
                'asignado_user_id' => $masterAdmin->asignado_user_id,
                'es_recurrente'    => false,
                'fecha_limite'     => $masterAdmin->fecha_limite ? now()->addDays($masterAdmin->created_at->diffInDays($masterAdmin->fecha_limite)) : null,
            ]);

            if ($nuevoAdmin->asignado_user_id) {
                NotificacionSistema::create([
                    'user_id'   => $nuevoAdmin->asignado_user_id,
                    'sender_id' => null,
                    'titulo'    => 'Requerimiento Administrativo Recurrente',
                    'mensaje'   => "Se ha generado automáticamente un requerimiento administrativo recurrente: " . $masterAdmin->titulo,
                    'url'       => route('requerimientos-administrativos.index'),
                ]);
            }

            $masterAdmin->update([
                'proxima_fecha_ejecucion' => $masterAdmin->calcularProximaFecha($masterAdmin->proxima_fecha_ejecucion)
            ]);

            $this->info("Instancia Admin creada ID: {$nuevoAdmin->id}. Próxima ejecución master: {$masterAdmin->proxima_fecha_ejecucion}");
        }

        $this->info("Proceso completado.");
        return 0;
    }
}
