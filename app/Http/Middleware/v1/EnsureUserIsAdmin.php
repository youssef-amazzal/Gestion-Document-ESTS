<?php

namespace App\Http\Middleware\v1;

use App\Enums\Roles;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->user()->role === Roles::ADMIN) {
            return $next($request);
        }

        return response()->json([
            'message' => 'You are not authorized to access this resource'
        ], Response::HTTP_FORBIDDEN);
    }
}
