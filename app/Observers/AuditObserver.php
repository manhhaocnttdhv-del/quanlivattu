<?php

namespace App\Observers;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditObserver
{
    public function created(Model $model)
    {
        $this->log($model, 'created');
    }

    public function updated(Model $model)
    {
        // Detect if it's a status change or just data change
        $event = 'updated';
        if ($model->isDirty('status')) {
            $event = $model->status; // e.g., 'completed', 'cancelled'
        }
        
        $this->log($model, $event);
    }

    public function deleted(Model $model)
    {
        $this->log($model, 'deleted');
    }

    protected function log(Model $model, string $event)
    {
        if (app()->runningInConsole()) {
            return; // Don't log seeders/migrations usually, but optional
        }

        $oldValues = $event === 'updated' ? array_intersect_key($model->getOriginal(), $model->getDirty()) : null;
        $newValues = $event === 'updated' ? $model->getDirty() : ($event === 'created' ? $model->toArray() : null);

        // Remove sensitive or too large data
        unset($newValues['updated_at'], $oldValues['updated_at']);

        AuditLog::create([
            'user_id' => Auth::id(),
            'event' => $event,
            'auditable_type' => get_class($model),
            'auditable_id' => $model->id,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'url' => Request::fullUrl(),
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }
}
