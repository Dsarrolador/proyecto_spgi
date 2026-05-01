<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_name',
        'action',
        'description',
        'module',
        'model_type',
        'model_id',
        'ip_address',
        'device_type',
        'browser',
        'os',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function subject()
    {
        return $this->morphTo('model');
    }

    public function getTargetUrl()
    {
        if (!$this->model_type || !$this->model_id) {
            return null;
        }

        try {
            switch ($this->module) {
                case 'RequerimientoCliente':
                    return route('requerimientos.show', $this->model_id);
                case 'ClienteMaestro':
                    return route('clientes.show', $this->model_id);
                case 'User':
                    return route('usuarios.show', $this->model_id);
                case 'Proyecto':
                    return route('proyectos.show', $this->model_id);
                case 'Lead':
                    return route('leads.show', $this->model_id);
                default:
                    return null;
            }
        } catch (\Exception $e) {
            return null;
        }
    }
}
