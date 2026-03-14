<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Request;

class LogSuccessfulLogout
{
    public function handle(Logout $event): void
    {
        if ($event->user) {
            AuditLog::create([
                'user_id' => $event->user->id,
                'event' => 'logout',
                'url' => Request::fullUrl(),
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
            ]);
        }
    }
}
