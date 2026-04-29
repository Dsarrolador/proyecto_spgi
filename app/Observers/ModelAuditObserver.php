<?php

namespace App\Observers;

use App\Services\AuditService;
use Illuminate\Database\Eloquent\Model;

class ModelAuditObserver
{
    public function created(Model $model)
    {
        $className = class_basename($model);
        AuditService::log('created', "Registro creado en {$className}", $className, $model);
    }

    public function updated(Model $model)
    {
        $className = class_basename($model);
        
        // Obtenemos los cambios
        $changes = $model->getChanges();
        unset($changes['updated_at']); // Ignorar solo cambio de fecha
        
        if (count($changes) > 0) {
            $description = "Registro actualizado en {$className}. Campos modificados: " . implode(', ', array_keys($changes));
            AuditService::log('updated', $description, $className, $model);
        }
    }

    public function deleted(Model $model)
    {
        $className = class_basename($model);
        AuditService::log('deleted', "Registro eliminado en {$className}", $className, $model);
    }
}
