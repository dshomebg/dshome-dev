<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class FilamentAuthMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!Auth::guard('web')->check()) {
            return redirect()->route('filament.admin.auth.login');
        }

        // Check if user can access the panel
        $user = Auth::guard('web')->user();
        $panel = filament()->getCurrentPanel();

        if ($panel && !$user->canAccessPanel($panel)) {
            abort(403);
        }

        return $next($request);
    }
}
