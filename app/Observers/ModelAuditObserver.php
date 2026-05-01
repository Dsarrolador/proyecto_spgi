<?php

namespace App\Observers;

use App\Services\AuditService;
use Illuminate\Database\Eloquent\Model;

class ModelAuditObserver
{
    public function created(Model $model)
    {
        $className = class_basename($model);
        $description = "Registro creado en {$className}";
        
        if ($className === 'RequerimientoCliente') {
            $cliente = $model->clienteRelation ? $model->clienteRelation->nombre : 'Cliente';
            $description = "Se creó un nuevo requerimiento para {$cliente}";
        }
        
        AuditService::log('created', $description, $className, $model);
    }

    public function updated(Model $model)
    {
        $className = class_basename($model);
        $changes = $model->getChanges();
        unset($changes['updated_at']);
        
        if (count($changes) > 0) {
            $description = "Registro actualizado en {$className}";
            
            if ($className === 'RequerimientoCliente') {
                $cliente = $model->clienteRelation ? $model->clienteRelation->nombre : 'Cliente';
                
                if (isset($changes['estado_id'])) {
                    if ($model->estado_id == 6) {
                        $description = "Se eliminó el requerimiento de {$cliente} (Movido a papelera)";
                        AuditService::log('deleted', $description, $className, $model);
                        return; // No procesar como actualización normal
                    }
                    
                    $newEstado = \App\Models\EstadoRequerimiento::find($model->estado_id);
                    $description = "Se cambió el estado del requerimiento de {$cliente} a: " . ($newEstado ? $newEstado->nombre : $model->estado_id);
                } else {
                    $description = "Se actualizó el requerimiento de {$cliente}. Cambios: " . implode(', ', array_keys($changes));
                }
            } else {
                $description .= ". Cambios: " . implode(', ', array_keys($changes));
            }
            
            AuditService::log('updated', $description, $className, $model);
        }
    }

    public function deleted(Model $model)
    {
        $className = class_basename($model);
        $description = "Registro eliminado en {$className}";
        
        if ($className === 'RequerimientoCliente') {
            $cliente = $model->clienteRelation ? $model->clienteRelation->nombre : 'Cliente';
            $description = "Se eliminó un requerimiento de {$cliente}";
        } elseif (isset($model->nombre)) {
            $description = "Se eliminó {$className}: {$model->nombre}";
        } elseif (isset($model->name)) {
            $description = "Se eliminó {$className}: {$model->name}";
        }
        
        AuditService::log('deleted', $description, $className, $model);
    }
}
