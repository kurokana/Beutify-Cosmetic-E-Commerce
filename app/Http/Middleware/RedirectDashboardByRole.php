<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectDashboardByRole
{
    /**
     * Redirect authenticated users to the dashboard that matches their role.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return $next($request);
        }

        if (Auth::user()->role === 'admin' && $request->routeIs('dashboard')) {
            return redirect()->route('admin.dashboard');
        }

        return $next($request);
    }
}