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
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized - Please login',
            ], 401);
        }

        // Defensive: If roles are passed as a single comma-separated string (e.g. from some cache configs), explode them
        $parsedRoles = [];
        foreach ($roles as $role) {
            if (str_contains($role, ',')) {
                $parsedRoles = array_merge($parsedRoles, explode(',', $role));
            } else {
                $parsedRoles[] = $role;
            }
        }
        $parsedRoles = array_map('trim', $parsedRoles);

        // Check if user has any of the required roles
        if (! empty($parsedRoles) && ! $user->hasAnyRole($parsedRoles)) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden - You do not have permission to access this resource',
            ], 403);
        }

        return $next($request);
    }
}
