<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAccountActive
{
    /**
     * Handle an incoming request.
     *
     * Blocks login for accounts that have been deactivated by an admin.
     * Implements Requirement 12.2: inactive account → display
     * "Akun Anda telah dinonaktifkan, hubungi layanan pelanggan".
     *
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && ! $user->is_active) {
            // Log the user out immediately
            Auth::guard('web')->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->withErrors([
                    'email' => 'Akun Anda telah dinonaktifkan, hubungi layanan pelanggan.',
                ]);
        }

        return $next($request);
    }
}
