<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        \Log::info('RoleMiddleware: masuk', [
            'user_id' => optional($request->user())->id,
            'user_email' => optional($request->user())->email,
            'required_role' => $role,
            'has_role' => $request->user() ? $request->user()->hasRole($role) : false,
            'route' => $request->path(),
        ]);
        if (!$request->user() || !$request->user()->hasRole($role)) {
            \Log::warning('RoleMiddleware: akses ditolak', [
                'user_id' => optional($request->user())->id,
                'user_email' => optional($request->user())->email,
                'required_role' => $role,
                'route' => $request->path(),
            ]);
            abort(403, 'Unauthorized action.');
        }
        \Log::info('RoleMiddleware: akses diizinkan', [
            'user_id' => optional($request->user())->id,
            'user_email' => optional($request->user())->email,
            'required_role' => $role,
            'route' => $request->path(),
        ]);
        return $next($request);
    }
}
