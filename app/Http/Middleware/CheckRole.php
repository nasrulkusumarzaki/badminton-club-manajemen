<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    /**
     * Handle an incoming request.
     * Accepts a comma-separated list of roles. User must have at least one.
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!$request->user()) {
            abort(403, 'Unauthorized');
        }

        // Normalize roles: middleware may receive multiple parameters or a single comma-separated string
        $allowed = [];
        foreach ($roles as $r) {
            foreach (explode(',', $r) as $part) {
                $part = trim($part);
                if ($part !== '') $allowed[] = $part;
            }
        }

        // If no roles provided, deny access
        if (empty($allowed)) {
            abort(403, 'Akses ditolak: role tidak tercantum.');
        }

        // If user uses Spatie HasRoles trait, use hasAnyRole; otherwise try hasRole
        $user = $request->user();

        if (method_exists($user, 'hasAnyRole')) {
            if ($user->hasAnyRole($allowed)) {
                return $next($request);
            }
        } else {
            foreach ($allowed as $r) {
                if (method_exists($user, 'hasRole') && $user->hasRole($r)) {
                    return $next($request);
                }
            }
        }

        abort(403, 'Akses ditolak: role tidak mencukupi.');
    }
}
