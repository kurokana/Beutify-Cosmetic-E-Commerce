<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Verifies that the authenticated user has the 'admin' role before
     * allowing access to admin panel routes.
     *
     * Implements Requirement 14.5: verify admin role before displaying
     * Panel_Admin content.
     *
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // If not authenticated, redirect to login
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        // If authenticated but not an admin, deny access
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk mengakses halaman ini.');
        }

        return $next($request);
    }
}
