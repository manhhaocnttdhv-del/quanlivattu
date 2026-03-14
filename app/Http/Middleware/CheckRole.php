<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!$request->user()) {
            return redirect('login');
        }

        $userRole = trim($request->user()->role);
        $allowedRoles = array_map('trim', $roles);

        if (in_array($userRole, $allowedRoles)) {
            return $next($request);
        }

        abort(403, 'Bạn không có quyền truy cập trang này.');
    }
}
