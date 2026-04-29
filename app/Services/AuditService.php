<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Request;
use Jenssegers\Agent\Agent;

class AuditService
{
    /**
     * Log an action to the audit trail.
     *
     * @param string $action
     * @param string|null $description
     * @param string|null $module
     * @param \Illuminate\Database\Eloquent\Model|null $model
     * @param \Illuminate\Contracts\Auth\Authenticatable|null $user Override the current auth user
     * @return \App\Models\AuditLog
     */
    public static function log($action, $description = null, $module = null, $model = null, $user = null)
    {
        $agent = new Agent();
        
        $deviceType = 'Desktop';
        if ($agent->isMobile()) {
            $deviceType = 'Mobile';
        } elseif ($agent->isTablet()) {
            $deviceType = 'Tablet';
        } elseif ($agent->isRobot()) {
            $deviceType = 'Robot';
        }

        $currentUser = $user ?? auth()->user();

        return AuditLog::create([
            'user_id'     => $currentUser ? $currentUser->id : null,
            'user_name'   => $currentUser ? ($currentUser->name ?? $currentUser->email) : Request::input('name', 'Sistema'),
            'action'      => $action,
            'description' => $description,
            'module'      => $module,
            'model_type'  => $model ? get_class($model) : null,
            'model_id'    => $model ? $model->id : null,
            'ip_address'  => Request::ip(),
            'device_type' => $deviceType,
            'browser'     => $agent->browser() . ' ' . $agent->version($agent->browser()),
            'os'          => $agent->platform() . ' ' . $agent->version($agent->platform()),
        ]);
    }
}
