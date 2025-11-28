<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PelangganMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Cara 1: Gunakan $request->user() yang sudah dihandle oleh auth:sanctum
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Silakan login terlebih dahulu.'
            ], 401);
        }

        // Cek role
        if ($user->role !== 'pelanggan') {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden. Hanya pelanggan yang dapat mengakses.'
            ], 403);
        }

        return $next($request);
    }
}
